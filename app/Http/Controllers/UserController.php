<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use Session;
use DB;

class UserController extends Controller
{
	public function editpassword(){
		
		if(Session::get('userid') != ''){

			$data = DB::select("select * from users where id =".Session::get('userid'))[0];
			
			if(!empty($data)){
				
				$user_data = array('user_name' => $data->user_name);
				return view('changepass', $user_data);
				
			} else {
				
				Session::flush();
				return redirect('/login');
			}			

		} else {
			
			return redirect('/login');
		}			
	}
	
	public function editprofile(){
		
		if(Session::get('userid') != ''){

			$data = DB::select("select * from users where id =".Session::get('userid'))[0];
			
			if(!empty($data)){
				
				$d = array();

				if(Session::has('userdata_2') == false){
// dd(11);
					$userdata_1 = array(
						'first_name' => $data->first_name,
						'last_name' => $data->last_name,
						'email' => $data->email,
						'dob' => date('m/d/Y', strtotime($data->dob)),
						'state' => $data->state,
						'city' => $data->city
					);					
					$userdata_2 = array(
						'user_name' => $data->user_name,
					);										
					Session::put("userdata_1", $userdata_1);
					Session::put("userdata_2", $userdata_2);
					$d = array_merge($userdata_1, $userdata_2);
					
				} else {
					// dd(12);
					$data_1 = Session::get("userdata_1");
					$data_2 = Session::get("userdata_2");
					$d = array_merge($data_1, $data_2);
				}
				return view('update', $d);
				
			} else {
				
				Session::flush();
				return redirect('/login');
			}			

		} else {
			
			return redirect('/login');
		}			
	}
	
	public function index(){
		// Session::flush();
		if(Session::get('userid') != ''){
		
			$isValidUser = (!empty(DB::select("select * from users where id =".Session::get('userid')))?true:false);

			if($isValidUser == true){

				$data = DB::select("select * from users where id =".Session::get('userid'))[0];
				$userdetail = '<table border="1" style="width:400px; border-collapse:collapse">';
				
				$userdetail.= '<tr>';
				$userdetail.= '<td>First Name</td>';
				$userdetail.= '<td>'.(!empty($data->first_name)?$data->first_name:'-').'</td>';
				$userdetail.= '</tr>';

				$userdetail.= '<tr>';
				$userdetail.= '<td>Last Name</td>';
				$userdetail.= '<td>'.(!empty($data->last_name)?$data->last_name:'-').'</td>';
				$userdetail.= '</tr>';

				$userdetail.= '<tr>';
				$userdetail.= '<td>Email</td>';
				$userdetail.= '<td>'.(!empty($data->email)?$data->email:'-').'</td>';
				$userdetail.= '</tr>';

				$userdetail.= '<tr>';
				$userdetail.= '<td>Date Of Birth</td>';
				$userdetail.= '<td>'.(!empty($data->dob)? date('d-m-Y',strtotime($data->dob)):'').'</td>';
				$userdetail.= '</tr>';

				$userdetail.= '<tr>';
				$userdetail.= '<td>State</td>';
				$userdetail.= '<td>'.(!empty($data->state)?$data->state:'').'</td>';
				$userdetail.= '</tr>';

				$userdetail.= '<tr>';
				$userdetail.= '<td>City</td>';
				$userdetail.= '<td>'.(!empty($data->city)?$data->city:'').'</td>';
				$userdetail.= '</tr>';
				
				$userdetail.= '<tr>';
				$userdetail.= '<td>User Name</td>';
				$userdetail.= '<td>'.(!empty($data->user_name)?$data->user_name:'').'</td>';
				$userdetail.= '</tr>';				
				
				$userdetail.= '</table>';
				
				$userdetail.= '<br/><a class="btn btn-lg btn-success" id="updateProfile" href="#" role="button">Update profile</a>&nbsp;&nbsp;&nbsp;<a class="btn btn-lg btn-success" id="updatePassword" href="#" role="button">Change password</a>';

				// dd($userdetail);
				$user_name = $data->user_name;
				return view('dashboard')->with(compact('userdetail', 'user_name'));
				
			} else {
				
				return redirect('/login');				
			}
		} else {
			
			return redirect('/login');
		}
	}
	
	public function logout(){
		if(Session::get('userid') != ''){
			
			Session::flush();			
		}		
		return redirect('/login');
	}
	public function login(){

		if(Session::get('userid') != ''){

			return redirect('/');
			
		} else {
			return view('login');
		}
	}
	
	public function registration(){
		// Session::forget('userdata_1');
		$data = array();
		$active = "tab_1";
		$user_detail = array();
		if(Session::get('userdata_1') == true){
			$user_detail = !empty(Session::get('userdata_1'))?Session::get('userdata_1'):'';
			//$user_data_2 = !empty(Session::get('userdata_2'))?Session::get('userdata_2'):'';
			if(!empty($user_detail)){				
				$user_detail['active_tab'] = "tab_2";
				$user_detail['user_name'] = '';
				$data = $user_detail;
			}
		} else {
			$data['first_name'] = '';
			$data['last_name'] = '';
			$data['email'] = '';
			$data['dob'] = '';
			$data['state'] = '';
			$data['city'] = '';
			$data['user_name'] = '';
			$data['active_tab'] = $active;
		}
		// dd($data);
		return view('register', $data);
	}
	
	public static function array_keys_exist($arr, $val_set){
		foreach($arr as $key => $val){
			if(!array_key_exists($val, $val_set)){				
				return false;
			}
		}
		return true;
	}
	
	public function changepass(Request $request){
		// dd(123);
		$tab_one = array('oldpass', 'pass', 'confirm_pass');
		$data = $request->post();
		$response = array('status' => false);
		if(self::array_keys_exist($tab_one, $data) == TRUE){
// dd(1);
			$validator = Validator::make($data,[
                'oldpass' => 'required',
				'pass' => 'min:6|required_with:confirm_pass|same:confirm_pass',
				'confirm_pass' => 'min:6'
            ], [
                'oldpass.required' => 'Old password is required',
                'pass.required' => 'Password is required',
                'confirm_pass.required' => 'Confirm password is required'
            ]);
			
			// dd($validator->fails());
			
			if ($validator->fails()) {
				// dd(2);
				$error_bag = $validator->messages()->first();
				$response = array('status' => false, 'message' => $error_bag);
			} else {
				// dd(1);
				
				//check old password is correct or not
				$check_old_pass = (!empty(DB::select("select id from users where pass ='".$data['oldpass']."' and id =".Session::get('userid')))?true:false);

				if($check_old_pass == TRUE){
					// dd(108);
					if($data['pass'] == $data['confirm_pass']){
						$user_data = array(
							'pass' => $data['pass']
						);
						// dd(1);
						// $r1 = 1;
						$iD = Session::get('userid');
						
						$r1 = DB::table('users')->where('id', $iD)->update($user_data);
						
						if($r1 == TRUE){
							// dd(111);
							$response = array('status' => true);
						} else {
							$response = array('status' => false);
						}
					} else {
						$error_bag = 'Please enter correct new passwords.';
						$response = array('status' => false, 'message' => $error_bag);
					}
					
				} else {
					$error_bag = "Please enter correct old password.";
					$response = array('status' => false, 'message' => $error_bag);
				}
			}
		}
		return $response;
	}
	
	public function update(Request $request){
		// dd(123);
		$tab_one = array('first_name', 'last_name', 'email', 'dob', 'state', 'city', 'user_name');
		$data = $request->post();
		$response = array('status' => false);
		if(self::array_keys_exist($tab_one, $data) == TRUE){
// dd(1);
			// $validatedData = $request->validate([
			$validator = Validator::make($data,[
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email',
                'dob' => 'required',
                'state' => 'required',
                'city' => 'required',
				'user_name' => 'required'
            ], [
                'first_name.required' => 'First name is required',
                'last_name.required' => 'Last name is required',
                'dob.required' => 'Date of birth is required',
                'state.required' => 'State is required',
                'city.required' => 'City is required',
                'user_name.required' => 'User name is required'
            ]);
			
			// dd($validator->fails());
			
			if ($validator->fails()) {
				// dd(2);
				$error_bag = $validator->messages()->first();
				$response = array('status' => false, 'message' => $error_bag);
			} else {
				// dd(1);
				$first_name = !empty($data['first_name'])?$data['first_name']:'';
				$last_name = !empty($data['last_name'])?$data['last_name']:'';
				$email = !empty($data['email'])?$data['email']:'';
				$dob = !empty($data['dob'])?date('d-m-Y',strtotime($data['dob'])):'';
				$state = !empty($data['state'])?$data['state']:'';
				$city = !empty($data['city'])?$data['city']:'';
				$user_name = !empty($data['user_name'])?$data['user_name']:'';

				$user_data = array(
					'first_name' => $first_name,
					'last_name' => $last_name,
					'email' => $email,
					'dob' => date('Y-m-d', strtotime($dob)),
					'state' => $state,
					'city' => $city,
					'user_name' => $user_name
				);
				// dd(1);
				// $r1 = 1;
				$iD = Session::get('userid');
				
				$r1 = DB::table('users')->where('id', $iD)->update($user_data);
				
				if($r1 == TRUE){
					// dd(111);
					unset($user_data['user_name']);
					$user_data['dob'] = date('m/d/Y', strtotime($dob));
					Session::put('userdata_1', $user_data);
					$user_data_2 = array('user_name' => $user_name);
					Session::put('userdata_2', $user_data_2);
					// Session::flush();
					$response = array('status' => true);
				}
			}
		}
		return $response;		
	}
	
	public function performvalidation(Request $request){
		// dd(123);
		$tab_one = array('first_name', 'last_name', 'email', 'dob', 'state', 'city');
		$tab_two = array('user_name', 'pass', 'confirm_pass');
		$data = $request->post();
		$response = array('status' => false);
		if(self::array_keys_exist($tab_one, $data) == TRUE){
// dd(1);
			// $validatedData = $request->validate([
			$validator = Validator::make($data,[
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email|unique:users',
                'dob' => 'required',
                'state' => 'required',
                'city' => 'required'
            ], [
                'first_name.required' => 'First name is required',
                'last_name.required' => 'Last name is required',
                'dob.required' => 'Date of birth is required',
                'state.required' => 'State is required',
                'city.required' => 'City is required'
            ]);
			
			// dd($validator->fails());
			
			if ($validator->fails()) {
				// dd(2);
				$error_bag = $validator->messages()->first();
				$response = array('status' => false, 'message' => $error_bag);
			} else {
				// dd(1);
				$first_name = !empty($data['first_name'])?$data['first_name']:'';
				$last_name = !empty($data['last_name'])?$data['last_name']:'';
				$email = !empty($data['email'])?$data['email']:'';
				$dob = !empty($data['dob'])?date('d-m-Y',strtotime($data['dob'])):'';
				$state = !empty($data['state'])?$data['state']:'';
				$city = !empty($data['city'])?$data['city']:'';

				$user_data = array(
					'first_name' => $first_name,
					'last_name' => $last_name,
					'email' => $email,
					'dob' => date('Y-m-d', strtotime($dob)),
					'state' => $state,
					'city' => $city
				);
				Session::put('userdata_1', $user_data);
				$response = array('status' => true);
			}
		}
		else if(self::array_keys_exist($tab_two, $data) == TRUE){
// dd(0);			
			$validator = Validator::make($data,[
                'user_name' => 'required',
				'pass' => 'min:6|required_with:confirm_pass|same:confirm_pass',
				'confirm_pass' => 'min:6'
            ], [
                'user_name.required' => 'User name is required',
                'pass.required' => 'Password is required',
                'confirm_pass.required' => 'Confirm password is required'
            ]);
			
			// dd($validatedData);
			
			if ($validator->fails()) {
				// dd(2);
				$error_bag = $validator->messages()->first();
				$response = array('status' => false, 'message' => $error_bag);
				
			} else {
				// dd(1);
				$user_name = !empty($data['user_name'])?$data['user_name']:'';
				$pass = !empty($data['pass'])?$data['pass']:'';

				$user_data = array(
					'user_name' => $user_name,
					'pass' => $pass
				);
				
				Session::put('userdata_2', $user_data);
				$user_data = array_merge(Session::get('userdata_1'), Session::get('userdata_2'));
				$r1 = DB::table('users')->insert($user_data);

				if($r1 == TRUE){
					Session::flush();
					$response = array('status' => true);
				}
			}
		}
		return $response;
	}
	
	public function doLogin(Request $request){

		$data = $request->post();
		// dd($data);
		if(Session::get('userid') != ''){

			return redirect('/');
			
		} else {						
			$email = (!empty($data['email'])?$data['email']:'');
			// $email = base64_encode($email); 

			$password = (!empty($data['password'])?$data['password']:'');
			// $password = base64_encode($password);

			$data = User::where('email','=',$email)->where('pass','=',$password)->first();
			if(!empty($data)){
				
				// Session::put('name',base64_encode($data['name']));
				// Session::put('email',$data['email']);
				Session::put('userid',$data['id']);
				return redirect('/');
			}
			else {
				return redirect('/login');
			}
		}
	}
}
