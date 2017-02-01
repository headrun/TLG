<?php

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class UsersController extends \BaseController {
        
    
    
        public function changepassword(){
            if(Auth::check() && (Session::get('userType')=='ADMIN')){
                $currentPage  =  "ChangePassword_LI";
		$mainMenu     =  "SETTINGS_MENU_MAIN";
		
                $users_data= User::where('franchisee_id','=',Session::get('franchiseId'))->get();
                
                $viewData = array (
					'users_data',
					'currentPage',
					'mainMenu',
					 
			);
			return View::make ( 'pages.users.changepassword', compact ( $viewData ) );
            }else{
                return Redirect::action('VaultController@logout');
            }
        }
        
        public function updatepassword(){
            if(Auth::check() && (Session::get('userType')=='ADMIN')){
                $inputs=Input::all();
                $user=User::find($inputs['user_id']);
                $user->password=Hash::make($inputs['password']);
                $user->save();
                return Response::json(array('status'=>'success'));
            }else{
                return Response::json(array('status'=>'failure')); 
            }
        }

        public static function addAdminUser(){
			if(Auth::check() && Session::get('userType')==='SUPER_ADMIN'){
				$inputs=Input::all();
				$AdminUser=User::createNewAdminUser($inputs);
				if($AdminUser){
					return Response::json(array('status'=>'success'));
				}
				return Response::json(array('status'=>'failure'));
			}
			return Response::json(array('status'=>'failure'));
		}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


}
