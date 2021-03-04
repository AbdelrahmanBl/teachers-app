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
        if(!$attendance_repo || $teacher_id != $attendance_repo->teacher_id)
            return Helper::returnError(__('messages.not_allowed'));

        $where = ['appointment_id' => $attendance_repo->appointment_id , 'status' => 'ON'];
        $subscrptions = Subscrption::where($where)->get();
        if(count($subscrptions) == 0)
            return Helper::returnError(__('messages.empty_students'));
        // Install Attendance Repo With Students Based On Appointment
        if($attendance_repo->is_install == 0) {
            $created_attenances = [];
            foreach($subscrptions->pluck('student_id') as $student_id) {
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
            
            $attendance_repo->is_install  = 1;
        }else {
            $attendance_repo->is_reinstall = 0;
        }

        // Get Attendance Data For This Repo
        $data  = Attendance::where('attendance_repo_id',$attendance_repo->id)->get();
        $users = User::whereIn('id',$subscrptions->pluck('student_id'))->get();

        if($attendance_repo->is_install == 1) {
            $new_registers = $subscrptions->whereNotIn('student_id',$data->pluck('student_id'))->pluck('student_id');

            $created_attenances = [];
            foreach($new_registers as $student_id) {
                $attendance = [
                    'teacher_id'         => $teacher_id,
                    'student_id'         => $student_id,
                    'attendance_repo_id'    => $id,
                    'status'             => false
                ];

                $created_attenances[] = $attendance;
            }
            
            if(count($created_attenances) > 0) {
                Attendance::insert($created_attenances);
                $news = Attendance::where('attendance_repo_id',$id)->where('_id','>',$data->last()->_id)->get();
                $arrays = [];
                foreach($news as $object)
                {
                    $data[] = $object;

                }
                Subscrption::where('teacher_id',$teacher_id)->whereIn('student_id',collect($created_attenances)->pluck('student_id'))->update([
                    'missed_no'       => DB::raw('missed_no + 1'),
                    'missed_sequence' => DB::raw('missed_sequence + 1')
                ]);
            }

        }
        
        $data->transform(function($item) use ($users){
            $user = $users->find($item['student_id']);

            if(!$user) {
                return ['parent_mobile1' => '%not_found%','id' => $item['_id'],'status' => $item['status'],'student_id' => $item['student_id']];
            }

            $map['id']   = $item->_id;
            $map['name'] = "{$user->first_name} {$user->last_name}";
            $map['parent_mobile1'] = $user->parent_mobile1;
            $map['status'] = $item->status;
            return $map;
        });
        
        $attendance_deleted = $data->where('parent_mobile1','%not_found%');
        $update_attend = [];
        $update_missed = [];

        foreach($attendance_deleted as $item) {
            if($item['status'] == true)
                $update_attend[] = ['student_id' => $item['student_id']];
            else $update_missed[] = ['student_id' => $item['student_id']];    
        }
        
        $update_attend = collect($update_attend);
        $update_missed = collect($update_missed);
        if(count($update_attend) > 0)
            Subscrption::where('teacher_id',$teacher_id)->whereIn('student_id',$update_attend->pluck('student_id'))->decrement('attend_no');
        if(count($attendance_deleted) > 0) {
            Subscrption::where('teacher_id',$teacher_id)->whereIn('student_id',$attendance_deleted->pluck('student_id'))->update([
                'missed_no'       => DB::raw('missed_no - 1'),
                'missed_sequence' => DB::raw('missed_sequence - 1')
            ]);
            
            Attendance::whereIn('_id',$attendance_deleted->pluck('id'))->delete();
        }

        $info = [
            'appointment_id' => $attendance_repo->appointment->id,
            'days'           => $attendance_repo->appointment->day->day,
            'time_from'      => $attendance_repo->appointment->time_from,
            'month'          => $attendance_repo->month,
            'class_no'       => $attendance_repo->class_no
        ];

        $attendances = [];
        foreach($data as $item) {
            if($item['parent_mobile1'] == '%not_found%')
                continue;
            $attendances[] = $item;
        }
        $attendances = count($attendances) > 0 ? collect($attendances) : $data;

        $attendance_repo->save();
        
        return Helper::return([
            'info'       => $info,
            'attendance' => $attendances
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

        $appointment_id = (int)$req->input('appointment_id');

        $insert_arr = $req->all(['class_no','month','year','appointment_id']);
        $insert_arr['teacher_id'] = $teacher_id;

        $where = [
            'id'          => (int)$id,
            'teacher_id'  => $teacher_id
        ];
        $attendance = AttendanceRepo::where('teacher_id',$teacher_id)->find((int)$id);
        if(!$attendance)
            return Helper::returnError(__('messages.not_allowed'));

        if($attendance->appointment_id != $appointment_id) {
            if($attendance->is_install == 1)
                $attendance->is_reinstall = 1;

            Appointment::where('id',$appointment_id)->increment('current_class_no');
            Appointment::where('id',$attendance->appointment_id)->decrement('current_class_no');
        }

        $attendance->update($insert_arr);

        return Helper::return([
            'days'      => $attendance->appointment->day->day,
            'time_from' => $attendance->appointment->time_from,
            'is_reinstall'   => $attendance->is_reinstall
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

        $appointment        = Appointment::where('id',$attendance_repo->appointment_id);
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
        $appointment->decrement('current_class_no');
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

        // Handle Attendance Process
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
        // Get Missed Sequence
        $subscrptions = Subscrption::where('teacher_id',$teacher_id)
                        ->whereIn('student_id',$attendances->whereIn('_id',collect($update_attend)->pluck('id'))
                        ->pluck('student_id'))->get();

        $missed_sequence = [];
        foreach($subscrptions as $subscrption) {
            
            $attendance = $attendances->where('student_id',$subscrption->student_id)->first();
            if(!$attendance)
                continue;

            $gt_attend = $attendance_obj->where('id',$attendance->_id)->first();
            if($gt_attend['status'] == false)
                $subscrption->missed_sequence--;
            
            if($subscrption->missed_sequence > 0) {
                $missed_sequence[] = [
                    'id'                => $attendance->_id,
                    'student_id'        => $subscrption->student_id,
                    'missed_sequence'   => $subscrption->missed_sequence
                ];
            }
            
        }
         // Reset Missed Sequence If Exist
        if(count($missed_sequence) > 0)
            Subscrption::where('teacher_id',$teacher_id)->whereIn('student_id',collect($missed_sequence)->pluck('student_id'))->update(['missed_sequence' => 0]);

        
        return Helper::return($missed_sequence);
    }

    public function student_statistics(Request $req,$student_id)
    {
        $teacher_id = $req->get('id');
        $student_id = (int)$student_id;
        
        $statistics = Helper::getStudentStatistics($student_id,$teacher_id);
        if(isset($statistics['error']))
            return Helper::returnError(__('messages.not_allowed'));
        
        return Helper::return([
            'main'   => $statistics['main'],//[20, 2],
            'months' => $statistics['months'],//['يناير', 'فبراير', 'مارس', 'أبريل']
            'attend' => $statistics['attend'],//[11, 8, 6, 8],
            'missed' => $statistics['missed'],//[1, 0, 2, 0]
        ]);
    }
}
