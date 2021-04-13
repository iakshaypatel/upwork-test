<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use App\Infrastructure\ServiceResponse;
use App\Models\User;
use Hash;

class LoginController extends BaseController
{

    /**
     * Validate Login
     * Method POST
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postLogin(Request $request)
    {
        $reqData = $request->all();
        $response = new ServiceResponse;
        $checkFields = array('email','password');
        $checkRequiredField = $this->checkRequestDataAPI($checkFields,$reqData);

        if($checkRequiredField == 'SUCC100'){
            $email = $reqData['email'];
            $password = $reqData['password'];
            $checkLoginParams = filter_var($request->input('email'), FILTER_VALIDATE_EMAIL ) ? 'email' : 'user_name';
            $record = User::where($checkLoginParams,$email)->first();

            if(!empty($record)){
                if(Hash::check($password,$record->password)){
                    $record->api_token = $this->generateApiToken($record->email);
                    $record->save();

                    $response->info = CommonController::loginResponse($record);
                    $response->IsSuccess = true;
                }else{
                    $response->Message = 'ERR106';
                }
            }else{
                if($checkLoginParams == 'user_name'){
                    $response->Message = "You have entered invalid user name";
                }else{
                    $response->Message = "You have entered invalid email address";
                }
            }
        }else{
            $response->Message = $checkRequiredField;
        }
        return $this->GetJsonResponse($response);
    }
}
