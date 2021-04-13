<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Infrastructure\ServiceResponse;
use App\Infrastructure\AppConstant;
use Auth, Validator;

class ProfileController extends BaseController
{
    /**
     * Save Profile
     * Method POST
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postProfile(Request $request){
        $reqData = $request->all();
        $response = new ServiceResponse;
        $checkFields = array('name','email','user_name');
        $checkRequiredField = $this->checkRequestDataAPI($checkFields,$reqData);

        if($checkRequiredField == 'SUCC100'){
            $authDetail = Auth::guard('api')->user();
            $authID = $authDetail->id;

            $validator = Validator::make($reqData, [
                'name' => 'required',
                'email' => 'required|email:rfc|unique:users,email,'.$authID.',id',
                'user_name' => 'required|unique:users,user_name,'.$authID.',id',
                'avatar' => 'nullable|dimensions:width=256,height=256'
            ]);

            if ($validator->fails()) {
                $validatorArr = $validator->errors()->toArray();
                if(array_key_exists('avatar', $validatorArr)){
                    $validatorArr['avatar']['0'] = "The avatar has invalid image dimensions. Please upload 256*256 image.";
                }
                $response->Message = $this->getValidationMessagesFormat($validatorArr);
            }else{
                $authDetail->name = $reqData['name'];
                $authDetail->email = $reqData['email'];
                $authDetail->user_name = $reqData['user_name'];

                if(!empty($request->file('avatar'))){
                    if($authDetail->image != DEFAULT_IMG_NAME){
                        AppConstant::deleteUserImage($authDetail->image);   
                    }
                    $authDetail->image = AppConstant::storeUserImage($request->file('avatar'));
                }
                $authDetail->save();

                $response->IsSuccess = true;
                $response->Message = "Profile updated successfully.";
            }
        }else{
            $response->Message = $checkRequiredField;
        }
        return $this->GetJsonResponse($response);
    }
}
