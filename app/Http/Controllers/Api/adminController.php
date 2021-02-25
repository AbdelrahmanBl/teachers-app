<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Package;
use App\Helper;

use validate;
use Hash;
class adminController extends Controller
{
   protected $package_storage = 'storage/packages'; 
	public function get_packages(Request $req)
    {try{
        $admin_id      = $req->get('id');

        $model       = new Package();
        $model_data  = $model::get();
        $model_data->transform(function($package) {
         $package['image'] = $package->getImage;
         return $package;
        });

        return Helper::return([
            'packages'    => $model_data
        ]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function get_teachers(Request $req)
    {try{
        $admin_id      = $req->get('id');

        $pagination    = $req->get('pagination');

        $select = array('users.id','packages.id as package_id','packages.name as package_name','users.first_name','users.last_name','users.mobile','users.image','users.students_number','users.appointments_number','users.exams_number','users.accept_register','users.is_rtl','users.created_at','users.status');
        $model       = new User();
        $model_select  = $model->where('users.type','T');
        $model_select    = $model_select->join('packages','packages.id','users.package_id')->select($select)->paginate($pagination);

        $model_data = $model_select->transform(function ($map){
        	$map['solves_storage'] = Helper::directory_size("storage/solves/{$map['id']}");
        	$map['images_storage'] = Helper::directory_size("storage/questions/{$map['id']}");

        	return $map;
        });

        return Helper::return([
        	'total'       => $model_select->total(),
	      'current_page'=> $model_select->currentPage(),
	      'per_page'    => $model_select->perPage(),
         'teachers'    => $model_data
        ]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    /*------------------------------------------------------------*/
    public function add_teacher(Request $req)
    {try{
        $admin_id      = $req->get('id');

        $req->validate([
        	'package_id'    	=> 'required|numeric|exists:packages,id',
        	'first_name'    	=> 'required|string|max:15',
        	'last_name'     	=> 'required|string|max:40',
        	'email'         	=> 'required|email|max:64|unique:users|unique:temp_students',
        	'password'          => 'required|string|min:6|max:16',
            'verify_password'   => 'required|string|same:password',
            'is_rtl'            => 'required|bool',
            'mobile'            => 'nullable|string|max:11',
        ]);
        $now    = date('Y-m-d H:i:s');
        $my_arr = $req->all(['package_id','first_name','last_name','email','is_rtl','mobile']);
        $my_arr['password'] 		    = Hash::make($req->input('password'));
        $my_arr['type']     		    = 'T';
        $my_arr['students_number']      = 0;
        $my_arr['appointments_number']  = 0;
        $my_arr['exams_number']         = 0;
        $my_arr['accept_register']      = 1;

        $user = new User($my_arr);
        $user->save();
        return Helper::return([
        	'id'    => $user->id,
        	'created_at' => $now
        ]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function update_teacher(Request $req)
    {try{
        $admin_id      = $req->get('id');

        $req->validate([
        	'teacher_id'        => 'required|numeric|exists:users,id,type,T',
        	'package_id'    	=> 'required|numeric|exists:packages,id',
        	'first_name'    	=> 'required|string|max:15',
        	'last_name'     	=> 'required|string|max:40',
            'mobile'            => 'nullable|string|max:11',
        ]);
        $my_arr = $req->all(['package_id','first_name','last_name','mobile']);

        $user = new User();
        $user::where('id',(int)$req->input('teacher_id'))->update($my_arr);
        
        return Helper::return([]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function change_teacher_status(Request $req)
    {try{
        $admin_id      = $req->get('id');

        $req->validate([
          'teacher_id'        => 'required|numeric|exists:users,id,type,T',
        ]);
        $model = new User();
        $where = array(
          'id'   => (int)$req->input('teacher_id'),
        );
        $model = $model::where($where);
        $status = Helper::disable($model,'status','ON','OFF','ON','OFF');
        
        return Helper::return([
          'status' => $status
        ]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function change_teacher_is_rtl(Request $req)
    {try{
        $admin_id      = $req->get('id');

        $req->validate([
        	'teacher_id'        => 'required|numeric|exists:users,id,type,T',
        ]);
        $model = new User();
        $where = array(
          'id'   => (int)$req->input('teacher_id'),
        );
        $model = $model::where($where);
        $status = Helper::disable($model,'is_rtl',1,0,true,false);
        
        return Helper::return([
          'status' => $status
        ]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function change_teacher_accept_register(Request $req)
    {try{
        $admin_id      = $req->get('id');

        $req->validate([
        	'teacher_id'        => 'required|numeric|exists:users,id,type,T',
        ]);
        $model = new User();
        $where = array(
          'id'   => (int)$req->input('teacher_id'),
        );
        $model = $model::where($where);
        $status = Helper::disable($model,'accept_register',1,0,true,false);
        
        return Helper::return([
          'status' => $status
        ]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function add_package(Request $req)
    {try{
        $admin_id      = $req->get('id');

        $req->validate([
        	'name'    	        => 'required|string|max:20',
        	'desc'     	        => 'required|string|max:100',
        	'students_limit'    => 'required|numeric|between:1,999999',
			'appointment_limit' => 'required|numeric|between:1,999999',
			'exams_limit'       => 'required|numeric|between:1,999999',
			'price'             => 'required|numeric|between:1,999999',
        ]);
        $my_arr = $req->all(['name','desc','students_limit','appointment_limit','exams_limit','price']);

        $package = new Package($my_arr);
        $package->save();
        return Helper::return([
        	'id'    => $package->id,
        	'created_at' => date('Y-m-d H:i:s')
        ]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function update_package(Request $req)
    {try{
        $admin_id      = $req->get('id');

        $req->validate([
        	'package_id'        => 'required|numeric|exists:packages,id',
        	'name'    	        => 'required|string|max:20',
        	'desc'     	        => 'required|string|max:100',
        	'students_limit'    => 'required|numeric|between:1,999999',
			'appointment_limit' => 'required|numeric|between:1,999999',
			'exams_limit'       => 'required|numeric|between:1,999999',
			'price'             => 'required|numeric|between:1,999999',
        ]);
        $my_arr = $req->all(['name','desc','students_limit','appointment_limit','exams_limit','price']);

        $package = new Package();
        $package::where('id',(int)$req->input('package_id'))->update($my_arr);
        return Helper::return([]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function change_package_status(Request $req)
    {try{
        $admin_id      = $req->get('id');

        $req->validate([
        	'package_id'        => 'required|numeric|exists:packages,id',
        ]);
        $model = new Package();
        $where = array(
          'id'   => (int)$req->input('package_id'),
        );
        $model = $model::where($where);
        $status = Helper::disable($model,'status','ON','OFF','ON','OFF');
        
        return Helper::return([
          'status' => $status
        ]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function change_package_image(Request $req)
    {try{
        $admin_id      = $req->get('id');

        $req->validate([
        	'package_id'      => 'required|numeric|exists:packages,id',
        	'image'           => 'required|image|mimes:jpeg,png,jpg|max:2000',
        ]);
        $image         = $req->file('image');

        $model = new Package();
        $where = array(
          'id'   => (int)$req->input('package_id'),
        );
        $model         = $model::where($where);
        $package_image = $model->first()->image;
        $url           = Helper::image($image,'add','packages');

        $model->update(['image' => $url]);
        Helper::delete_image($this->package_storage,$package_image);
        
         return Helper::return([
          'url'   => asset("{$this->package_storage}/{$url}")
        ]);     
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
}
