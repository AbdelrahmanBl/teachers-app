<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\AttendanceRepo;
use App\Models\Attendance;
use App\Models\Appointment;
use App\Models\Subscrption;
use App\Models\User;
use App\Helper;

class attendanceController extends Controller
{
    public function index(Request $req)
    {
        $teacher_id     = $req->get('id');
        $paginate       = (int)$req->get('pagination');
        $month          = (int)$req->get('month');
        $year           = (int)$req->get('year');
        $appointment_id = (int)$req->get('appointment_id');

        $where[] = ["teacher_id",$teacher_id];
        if($month)
            $where[] = ["month",$month];
        if($year)
            $where[] = ["year",$year];
        if($appointment_id)
            $where[] = ["appointment_id",$appointment_id];
        $model = AttendanceRepo::where($where)->orderBy('id','DESC')->paginate($paginate);

        $model->data = $model->getCollection()->transform(function($data) {
            $data->day       = $data->appointment->day->day;
            $data->time_from = $data->appointment->time_from;

            return $data->makeHidden(['appointment','updated_at','teacher_id']);
        });

        return Helper::return($model);
    } 

    public function show(Request $req,$id)
    {
        $id = (int)$id;
        $teacher_id = $req->get('id');

        $attendance_repo = AttendanceRepo::find($id);
        if($teacher_id != $attendance_repo->teacher_id)
            return Helper::returnError(__('messages.not_allowed'));

        // Install Attendance Repo With Students Based On Appointment
        if($attendance_repo->is_install == 0) {
            $where = ['appointment_id' => $attendance_repo->appointment_id , 'status' => 'ON'];
            $subscrptions = Subscrption::where($where)->pluck('student_id');
            $created_attenances = [];
            foreach($subscrptions as $student_id) {
                $attendance = [
                    'teacher_id'         => $teacher_id,
                    'student_id'         => $student_id,
                    'attendance_repo_id' => $id,
                    'month'              => $attendance_repo->month,
                    'status'             => false
                ];

                $created_attenances[] = $attendance;
            }

            if(count($created_attenances) == 0)
                return Helper::returnError(__('messages.empty_students'));
            
            Attendance::insert($created_attenances);
            Subscrption::where('teacher_id',$teacher_id)->whereIn('student_id',collect($created_attenances)->pluck('student_id'))->update([
                'missed_no'       => DB::raw('missed_no + 1'),
                'missed_sequence' => DB::raw('missed_sequence + 1')
            ]);
            $attendance_repo->is_install = 1;
            $attendance_repo->save();
        }

        // Get Attendance Data For This Repo
        $data  = Attendance::where('attendance_repo_id',$attendance_repo->id)->get();
        $users = User::whereIn('id',$data->pluck('student_id'))->get();
        
        $data->transform(function($item) use ($users){
            $user = $users->find($item['student_id']);

            $map['id']   = $item->_id;
            $map['name'] = "{$user->first_name} {$user->last_name}";
            $map['parent_mobile1'] = $user->parent_mobile1;
            $map['status'] = $item->status;
            return $map;
        });

        $info = [
            'days'      => $attendance_repo->appointment->day->day,
            'time_from' => $attendance_repo->appointment->time_from,
            'month'     => $attendance_repo->month,
            'class_no'  => $attendance_repo->class_no
        ];
        
        return Helper::return([
            'info'       => $info,
            'attendance' => $data
        ]);
    }

    public function store(Request $req)
    {
        $teacher_id = $req->get('id');
        $req->validate([
            'class_no'          => 'required|numeric|min:1|max:30',
            'month'             => 'required|numeric|min:1|max:12',
            'year'              => 'required|numeric|in:1,2,3',
            'appointment_id'    => "required|numeric|exists:appointments,id,teacher_id,{$teacher_id}",
        ]);
        $class_no = (int)$req->class_no;

        $insert_arr = $req->all(['class_no','month','year','appointment_id']);
        $insert_arr['teacher_id'] = $teacher_id;

        $appointment = Appointment::find((int)$req->appointment_id);
        if($class_no > $appointment->max_class_no)
            return Helper::returnError(__('messages.not_allowed'));
        
        $appointment->current_class_no = $class_no;
        $appointment->save();    

        $attendance  = AttendanceRepo::create($insert_arr);

        return Helper::return([
            'id'        => $attendance->id,
            'days'      => $attendance->appointment->day->day,
            'time_from' => $attendance->appointment->time_from,
            'created_at'=> date('Y-m-d H:i:s')
        ]);
    }

    public function update(Request $req,$id)
    {
        $teacher_id = $req->get('id');
        $req->validate([
            'class_no'          => 'required|numeric|min:1|max:30',
            'month'             => 'required|numeric|min:1|max:12',
            'year'              => 'required|numeric|in:1,2,3',
            'appointment_id'    => "required|numeric|exists:appointments,id,teacher_id,{$teacher_id}",
        ]);

        $insert_arr = $req->all(['class_no','month','year','appointment_id']);
        $insert_arr['teacher_id'] = $teacher_id;

        $where = [
            'id'          => (int)$id,
            'teacher_id'  => $teacher_id
        ];
        $attendance = AttendanceRepo::where('teacher_id',$teacher_id)->find((int)$id);
        if(!$attendance)
            return Helper::returnError(__('messages.not_allowed'));

        $attendance->update($insert_arr);

        return Helper::return([
            'days'      => $attendance->appointment->day->day,
            'time_from' => $attendance->appointment->time_from
        ]);
    }

    public function destroy(Request $req,$id)
    {
        $teacher_id = $req->get('id');
        $where = [
            'id'          => (int)$id,
            'teacher_id'  => $teacher_id
        ];
        $attendance_repo = AttendanceRepo::where('teacher_id',$teacher_id)->find((int)$id);
        if(!$attendance_repo)
            return Helper::returnError(__('messages.not_allowed'));
        // if($attendance_repo->is_install == 1)
        //     return Helper::returnError(__('messages.not_allowed'));

        $attendances_select = Attendance::where('attendance_repo_id',$attendance_repo->id);
        $attendances        = $attendances_select->get();

        if(count($attendances) > 0) {
        $update_missed = [];
        $update_attend = [];
        foreach($attendances as $attendance) {
            if((boolean)$attendance['status'] == true)
                $update_attend[] = ['id' => $attendance['_id'] , 'student_id' => $attendance->student_id];
            else $update_missed[] = ['id' => $attendance['_id'] , 'student_id' => $attendance->student_id];
        }
        $update_attend = collect($update_attend);

        if($update_attend) 
            Subscrption::where('teacher_id',$teacher_id)->whereIn('student_id',$update_attend->pluck('student_id'))->decrement('attend_no');
        
        if($attendances) 
            Subscrption::where('teacher_id',$teacher_id)->whereIn('student_id',$attendances->pluck('student_id'))->update([
                'missed_no'       => DB::raw('missed_no - 1'),
                'missed_sequence' => DB::raw('missed_sequence - 1')
            ]);
        
        }
        $attendance_repo->delete();
        $attendances_select->delete();

        return Helper::return([]);
    }

    public function attend_students(Request $req)
    {
        $teacher_id     = $req->get('id');
        $req->validate([
            'attendances'          => 'required|array',
            // 'attendances.*.id'     => "required|string|exists:mongodb.attendances,_id",
            'attendances.*.status' => "required|boolean",
        ]);
        $attendance_obj = collect($req->input('attendances'));
        $attendance_arr = $attendance_obj->pluck('id');

        $attendances = Attendance::where('teacher_id',$teacher_id)->whereIn('_id',$attendance_arr)->get();
        if(count($attendances) > 0) {
        $update_missed = [];
        $update_attend = [];
        foreach($attendances as $attendance) {
            $gt_attend = $attendance_obj->where('id',$attendance->_id)->first();
            if((boolean)$attendance->status == (boolean)$gt_attend['status'])
                continue;

            if((boolean)$gt_attend['status'] == true)
                $update_attend[] = ['id' => $gt_attend['id'] , 'student_id' => $attendance->student_id];
            else $update_missed[] = ['id' => $gt_attend['id'] , 'student_id' => $attendance->student_id];
        }
        $update_attend = collect($update_attend);
        $update_missed = collect($update_missed);
        if($update_attend) {
        Attendance::whereIn('_id',$update_attend->pluck('id'))->update(['status' => true]);
        Subscrption::where('teacher_id',$teacher_id)->whereIn('student_id',$update_attend->pluck('student_id'))->increment('attend_no');
        }
        if($update_missed) {
        Attendance::whereIn('_id',$update_missed->pluck('id'))->update(['status' => false]);
        Subscrption::where('teacher_id',$teacher_id)->whereIn('student_id',$update_missed->pluck('student_id'))->decrement('attend_no');
        }
        }
        
        return Helper::return([]);
    }
}
