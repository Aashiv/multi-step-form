<?php

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\User;
use Session;
use DB;

class UserController extends Controller
{
	public function index(){
		
		if(Session::get('userid') != ''){
		
			$isValidUser = (!empty(DB::select("select * from users where id =".Session::get('userid')))?true:false);

			if($isValidUser == true){

				$data['project_list'] = DB::select("select * from users where iUserId =".Session::get('userid'));
				echo "logged in";
				exit;
				return view('welcome', $data);			
				
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
			
			return view('htmlpdf::login');
		}
	}

	public function doLogin(Request $request){

		$data = Request::post();
		
		if(Session::get('userid') != ''){

			return redirect('/');
			
		} else {						
			$email = (!empty($data['email'])?$data['email']:'');
			$email = base64_encode($email); 

			$password = (!empty($data['password'])?$data['password']:'');
			$password = base64_encode($password);

			$data = User::where('email','=',$email)->where('password','=',$password)->first();
			if(!empty($data)){
				
				Session::put('name',base64_encode($data['name']));
				Session::put('email',$data['email']);
				Session::put('userid',$data['id']);
				return redirect('/');
			}
			else {
				return redirect('/login');
			}
		}
	}	

	public function doNewProject(Request $request){

		$data = Request::post();
		
		if($data['addproject']){

			$name = (!empty($data['name'])?$data['name']:'');
			$userid = Session::get('userid');
			
			$r = DB::select('select * from workspaces where iUserid="'.$userid.'" and vName="'.$name.'"');
			if(count($r) == 0){

				$r1 = DB::table('workspaces')->insert([
					'vName' => $name,
					'iUserid' => $userid,
					'tDocname' => $name
				]);

				if($r1 == TRUE){

					$iworkspaceid = DB::getPdo()->lastInsertId();
					Session::put('workspaces', $iworkspaceid);
					return redirect('/doworkspace/'.$iworkspaceid);
				}
			} else {
				Session::put('message','Name is already exist. Enter another one.');
				return redirect('/');
			}
		}
	}
	
	public function removeTable(Request $request){
		$data = Request::post();
		if(isset($data['context']) && $data['context']=='removetable'){
			
		} else {
			$response['status']='false';
			$response['error']='invalid request.';
			$response['data']=array();
		}
		echo json_encode($response);
	}
	
	public function createTableRequest(Request $request){
		
		$data = Request::post();
		if(isset($data['context']) && $data['context']=='createtable'){
			
			//no of columns
			$table = (!empty($data['table'])?$data['table']:'');
			$iworkspaceid = Session::get('workspaces');

			$table_start = htmlentities('<table style="width:100%;">');
			$table_end = htmlentities('</table>');

			$row_start = htmlentities('<tr>');
			$row_end = htmlentities('</tr>');

			$width_in_percentage = '';
			$width_in_pixel = '';

			$column_start = '';
			$column_end = htmlentities('</td>');

			//create table using workspaces
			$r = DB::table('tables')->insert([
				'iworkspaceid' => $iworkspaceid,
				'tTagstart' => $table_start,
				'tTagend' => $table_end
			]);

			if($r == TRUE){
				
				$iTableid = DB::getPdo()->lastInsertId();
				for($a=0;$a<1;++$a){
					
					$r2 = DB::table('rows')->insert([
						'iTableid' => $iTableid,
						'iWorkspaceid' => $iworkspaceid,
						'tTagstart' => $row_start,
						'tTagend' => $row_end
					]);

					if($r2 == TRUE){

						$iRowid = DB::getPdo()->lastInsertId();
						$no_of_columns = $table;
						$width_in_percentage = (100/$no_of_columns);
						$width_in_percentage = number_format((float)$width_in_percentage, 2, '.', '');  // Outputs -> 105.00
						$width_in_percentage .= "%";
						$width_in_pixel = (1080/$no_of_columns);
						$width_in_pixel = number_format((float)$width_in_pixel, 2, '.', '');
						$width_in_pixel .= "px";
						$column_start = htmlentities('<td style="width:'.$width_in_percentage.'; width:'.$width_in_pixel.'">');

						for($b=0;$b<$no_of_columns;++$b){

							$r3 = DB::table('columns')->insert([
								'iRowid' => $iRowid,
								'iTableid' => $iTableid,
								'iworkspaceid' => $iworkspaceid,
								'tTagstart' => $column_start,
								'tTagend' => $column_end
							]);
						}
					}
				}
				$response['status']='true';
				$response['error']='table create successful.';
				$response['data']=array('id'=>$iTableid);

			} else {
				$response['status']='false';
				$response['error']='could not create table.';
				$response['data']=array();
			}
		} else {
			$response['status']='false';
			$response['error']='invalid request.';
			$response['data']=array();
		}
		echo json_encode($response);
	}
	public function dashboard($iworkspaceid){

		if(Session::get('userid') != ''){
		
			Session::put('workspaces',$iworkspaceid);
			
			$workspaceName = DB::select('select vName from workspaces where id='.$iworkspaceid)[0]->vName;
			/* Start Load Table Tree View */
			$tableArr = DB::select('select id from tables where iWorkspaceid='.$iworkspaceid);

			$data = '';
			if(count($tableArr) > 0){
				
				$data.= '<ul class="nav nav-sidebar">';

				foreach($tableArr as $row2){
					
					$iTableid = $row2->id;
					
					
					$rowArr = DB::select('select id from rows where iTableid="'.$iTableid.'"');

					if(count($rowArr) > 0){
						$data.= '<li><a href="javascript:void(0);">Table-'.$iTableid.'<span class="sr-only">(current)</span></a></li>';
						
						$data.= '<ul class="nav nav-sidebar" style="padding-left: 50px; background-color: #ccc;">';
						
						foreach($rowArr as $row3){
							
							$iRowid = $row3->id;						
							
							$columnArr = DB::select('select id from columns where iRowid="'.$iRowid.'"');

							if(count($columnArr) > 0){
								$data.= '<li><a href="javascript:void(0);">Row-'.$iRowid.'<span class="sr-only">(current)</span></a></li>';
								
								$data.= '<ul class="nav nav-sidebar" style="padding-left: 70px; background-color: #ccc;">';
				
								foreach($columnArr as $row4){
								
									$iColumnid = $row4->id;

									$data.= '<li><a href="javascript:void(0);" onclick="getHtml('.$iTableid.','.$iRowid.','.$iColumnid.')">Column-'.$iColumnid.'<span class="sr-only">(current)</span></a></li>';
								}

								$data.= '</ul>';
							}
							
						}
						
						$data.= '</ul>';
					}
				}
				
				$data.= '</ul>';  
			}
			
			return view('htmlpdf::dashboard',array('sidebar'=>$data, 'iWorkspaceid'=>$iworkspaceid, 'workspaceName'=> $workspaceName));
		} else {
			
			return redirect('/login');
		}		
	}
}
