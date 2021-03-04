<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\PaymentRepo;
use App\Models\Payment;
use App\Models\Subscrption;
use App\Models\User;
use App\Helper;

class paymentController extends Controller
{
    public function index(Request $req)
    {
        $teacher_id     = $req->get('id');
        $paginate       = (int)$req->get('pagination');
        $year           = (int)$req->get('year');

        $where[] = ["teacher_id",$teacher_id];
        if($year)
            $where[] = ["year",$year];

        $model = PaymentRepo::where($where)->orderBy('id','DESC')->paginate($paginate);

        $model->data = $model->getCollection()->transform(function($data) {
            $data->appointments    = $data->appointments;

            return $data->makeHidden(['appointment_ids','updated_at','teacher_id']);
        });

        return Helper::return($model);
    } 

    public function show(Request $req,$id)
    {
        // return Payment::get();
        $id = (int)$id;
        $teacher_id = $req->get('id');

        $payment_repo = PaymentRepo::find($id);
        if(!$payment_repo || $teacher_id != $payment_repo->teacher_id)
            return Helper::returnError(__('messages.not_allowed'));

        $subscrptions = Subscrption::where('status','ON')->whereIn('appointment_id',$payment_repo->appointmentsArr)->get();
        if(count($subscrptions) == 0)
            return Helper::returnError(__('messages.empty_students'));
        // Install Payment Repo With Students Based On Appointment
        if($payment_repo->is_install == 0) {
            // $subscrptions = $subscrptions->where('status','ON')->get();
            $created_payments = [];
            $students_no = 0;
            foreach($subscrptions->pluck('student_id') as $student_id) {
                $payment = [
                    'teacher_id'         => $teacher_id,
                    'student_id'         => $student_id,
                    'payment_repo_id'    => $id,
                    'status'             => false
                ];

                $created_payments[] = $payment;
                $students_no++;
            }

            if(count($created_payments) == 0)
                return Helper::returnError(__('messages.empty_students'));
            
            Payment::insert($created_payments);

            $payment_repo->is_install   = 1;
            $payment_repo->students_no  = $students_no;
        }else {
            $payment_repo->is_reinstall = 0;
        }

        // Get Payment Data For This Repo
        $data  = Payment::where('payment_repo_id',$payment_repo->id)->get();
        $users = User::whereIn('id',$subscrptions->pluck('student_id'))->get();

        // Handle New Added Students
        $students_no      = 0;
        if($payment_repo->is_install == 1) {
            $new_registers = $subscrptions->whereNotIn('student_id',$data->pluck('student_id'))->pluck('student_id');
            $created_payments = [];

            foreach($new_registers as $student_id) {
                $payment = [
                    'teacher_id'         => $teacher_id,
                    'student_id'         => $student_id,
                    'payment_repo_id'    => $id,
                    'status'             => false
                ];

                $created_payments[] = $payment;
                $students_no++;
            }
            
            if(count($created_payments) > 0) {
                Payment::insert($created_payments);
                $news = Payment::where('payment_repo_id',$id)->where('_id','>',$data->last()->_id)->get();
                $arrays = [];
                foreach($news as $object)
                {
                    $data[] = $object;
                }
            }
        }
        
        $data->transform(function($item) use ($users,$subscrptions){
            $user = $users->find($item['student_id']);
            
            if(!$user) {
                return ['appointment_id' => 0,'id' => $item['_id']];
            }

            $map['id']             = $item['_id'];
            $map['name']           = "{$user->first_name} {$user->last_name}";
            $map['parent_mobile1'] = $user->parent_mobile1;
            $map['appointment_id'] = $subscrptions->where('student_id',$user->id)->first()->appointment->id;
            $map['days']           = $subscrptions->where('student_id',$user->id)->first()->appointment->day->day;
            $map['time_from']      = $subscrptions->where('student_id',$user->id)->first()->appointment->time_from;
            $map['status']         = $item->status;
            return $map; 
        });
        
        $payment_deleted = $data->where('appointment_id',0)->pluck('id');
        if(count($payment_deleted) > 0)
            Payment::whereIn('_id',$payment_deleted)->delete();
        
        $info = [
            'appointments'   => $payment_repo->appointments,
            'name'           => $payment_repo->name,
            'desc'           => $payment_repo->desc,
            'year'           => $payment_repo->year,
        ];

        $payments = [];
        foreach($data as $item) {
            if($item['appointment_id'] == 0)
                continue;
            $payments[] = $item;
        }
        $payments = count($payments) > 0 ? collect($payments) : $data;

        $new_students_no = $students_no - count($payment_deleted);
        if($new_students_no != 0) {
            $payment_repo->students_no  += $new_students_no;
            $payment_repo->is_paid      = $payments->where('status',true)->count() == $payment_repo->students_no ? 1 : 0;
        }
        
        $payment_repo->save();

        return Helper::return([
            'info'       => $info,
            'attendance' => $payments
        ]);
    }

    public function store(Request $req)
    {
        $teacher_id = $req->get('id');
        $req->validate([
            'name'              => 'required|string|max:50',
            'desc'              => 'nullable|string|max:255',
            'year'              => 'required|numeric|in:1,2,3',
            'appointment_ids'   => 'required|array',
            'appointment_ids.*' => "required|exists:appointments,id,teacher_id,{$teacher_id}",
        ]);
        $appointment_ids = collect($req->appointment_ids)->transform(function($item) {
            return (int)$item;
        })->toArray();
        
        $insert_arr = $req->all(['name','desc','year']);
        $insert_arr['teacher_id']      = $teacher_id;
        $insert_arr['appointment_ids'] = json_encode($appointment_ids);

        $payment  = PaymentRepo::create($insert_arr);

        return Helper::return([
            'id'            => $payment->id,
            'appointments'  => $payment->appointments,
            'created_at'    => date('Y-m-d H:i:s')
        ]);
    }

    public function update(Request $req,$id)
    {
        $teacher_id = $req->get('id');
        $req->validate([
            'name'              => 'required|string|max:255',
            'desc'              => 'nullable|string|max:255',
            'year'              => 'required|numeric|in:1,2,3',
            'appointment_ids'   => 'required|array',
            'appointment_ids.*' => "required|exists:appointments,id,teacher_id,{$teacher_id}",
        ]);

        $appointment_ids = collect($req->appointment_ids)->transform(function($item) {
            return (int)$item;
        })->toArray();

        $insert_arr = $req->all(['name','desc','year']);
        $insert_arr['teacher_id']      = $teacher_id;
        $insert_arr['appointment_ids'] = json_encode($appointment_ids);

        $where = [
            'id'          => (int)$id,
            'teacher_id'  => $teacher_id
        ];
        $payment = PaymentRepo::where('teacher_id',$teacher_id)->find((int)$id);
        if(!$payment)
            return Helper::returnError(__('messages.not_allowed'));

        if($payment->appointment_ids != json_encode($appointment_ids)) {
            if($payment->is_install == 1)
                $payment->is_reinstall = 1;
        }
        
        $payment->update($insert_arr);

        return Helper::return([
            'appointments'      => $payment->appointments,
            'is_reinstall'      => $payment->is_reinstall
        ]);
    }

    public function destroy(Request $req,$id)
    {
        $teacher_id = $req->get('id');
        $where = [
            'id'          => (int)$id,
            'teacher_id'  => $teacher_id
        ];
        $payment_repo = PaymentRepo::where('teacher_id',$teacher_id)->find((int)$id);
        if(!$payment_repo)
            return Helper::returnError(__('messages.not_allowed'));
        // if($payment_repo->is_install == 1)
        //     return Helper::returnError(__('messages.not_allowed'));

        $payments_select = Payment::where('payment_repo_id',$payment_repo->id);

        $payment_repo->delete();
        $payments_select->delete();

        return Helper::return([]);
    }

    public function payment_students(Request $req,$payment_repo_id)
    {
        $teacher_id     = $req->get('id');
        $req->validate([
            'attendances'          => 'required|array',
            // 'attendances.*.id'     => "required|string|exists:mongodb.attendances,_id",
            'attendances.*.status' => "required|boolean",
        ]);
        $payment_repo_id = (int)$payment_repo_id;
        $payment_obj  = collect($req->input('attendances'));
        $payment_arr  = $payment_obj->pluck('id');

        $payment_repo = PaymentRepo::find($payment_repo_id);
        if(!$payment_repo || $payment_repo->teacher_id != $teacher_id)
            return Helper::returnError(__('messages.not_allowed'));
        // Handle Attendance Process
        $payments = Payment::where('teacher_id',$teacher_id)->whereIn('_id',$payment_arr)->get();
        if(count($payments) > 0) {
            $update_missed = [];
            $update_payment = [];
            $paid_count = 0;
            foreach($payments as $payment) {
                $gt_payment = $payment_obj->where('id',$payment->_id)->first();
                
                if($gt_payment['status'] == true)
                    $paid_count++;
                if((boolean)$payment->status == (boolean)$gt_payment['status'])
                    continue;

                if((boolean)$gt_payment['status'] == true)
                    $update_payment[] = ['id' => $gt_payment['id'] , 'student_id' => $payment->student_id];
                else $update_missed[] = ['id' => $gt_payment['id'] , 'student_id' => $payment->student_id];
            }
            $update_payment = collect($update_payment);
            $update_missed  = collect($update_missed);
            if($update_payment) {
            Payment::whereIn('_id',$update_payment->pluck('id'))->update(['status' => true]);
            }
            if($update_missed) {
            Payment::whereIn('_id',$update_missed->pluck('id'))->update(['status' => false]);
            }

            $payment_repo->is_paid = ($paid_count == $payment_repo->students_no) ? 1 : 0;
            $payment_repo->save();
        }
        
        return Helper::return([]);
    }
}
