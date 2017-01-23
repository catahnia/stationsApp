<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Transaction;


class UserController extends Controller
{
	public function register () {
		try{
			$result = User::insert(['username' => $_GET['username'],
				'password' => $_GET['password'], 'email' => $_GET['email']]);
		} catch (\Exception $e) {
			return  \Response::json(array(
			'error' => true,
			'result' => 0,
			'status_code' => 400));

		}

		if ($result==1) {
			return  \Response::json(array(
			'error' => false,
			'result' => $result,
			'status_code' => 200));
		}
		
	}

	public function login() {
		$user = User::where([['username', '=', $_GET['username']],
							['password', '=', $_GET['password']],])->get();

		if($user != null) {
			return  \Response::json(array(
			'error' => false,
			'user' => $user,
			'status_code' => 200));
		}
	}

	public function order() {
		try{
			$result = Transaction::insert(['users_id' => $_POST['id'],
				'pricedata_priceDataId' => $_POST['priceDataId'], 'quantity' => $_POST['quantity']]);
		} catch (\Exception $e) {
			return  \Response::json(array(
			'error' => true,
			'result' => 0,
			'status_code' => 400));

		}
		if ($result==1) {
			return redirect('/')->with('status', 'Order Made!');

			/* \Response::json(array(
			'error' => false,
			'result' => $result,
			'status_code' => 200));*/
		}

	}
    
}
