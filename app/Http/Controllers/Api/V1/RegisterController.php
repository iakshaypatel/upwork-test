<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseController;
use App\Infrastructure\ServiceResponse;
use App\Infrastructure\AppConstant;
use Illuminate\Http\Request;
use App\Models\User, App\Models\UserInvitation;
use Mail, Validator;

class RegisterController extends BaseController
{

    /**
     * Validate Login
     * Method POST
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postVerifyEmail(Request $request)
    {
        $reqData = $request->all();
        $response = new ServiceResponse;
        $checkFields = array('invitation_key');
        $checkRequiredField = $this->checkRequestDataAPI($checkFields,$reqData);

        if($checkRequiredField == 'SUCC100'){
            $invitationKey = $reqData['invitation_key'];

            $decryptID = $this->getEncryptDecryptValue('decrypt',$invitationKey);
            $invitationID = $this->getExplodeValue($decryptID,AppConstant::$userInvitationID);

            if($invitationID > 0){
                $invitationDetail = UserInvitation::find($invitationID);

                if(!empty($invitationDetail)){
                    $reqData['email'] = $invitationDetail['email'];
                    $validator = Validator::make($reqData, [
                        'email' => 'required|unique:users,email',
                        'user_name' => 'required|unique:users,user_name',
                    ]);
                   
                    if ($validator->fails()) {
                        $response->Message = $this->getValidationMessagesFormat($validator->messages());
                    }else{

                        if(!empty($reqData['code'])){
                            //Check verification code is valid
                            if($reqData['code'] == $invitationDetail->code){
                                $user = new User;
                                $user->invited_by = $invitationDetail->invited_by;
                                $user->name = $reqData['user_name'];
                                $user->user_name = $reqData['user_name'];
                                $user->email = $invitationDetail->email;
                                $user->password = bcrypt($reqData['password']);
                                $user->registered_at = now();
                                $user->save();

                                $response->IsSuccess = true;
                                $response->Message = "Please login";

                            }else{
                                $response->Message = "You have entered invalid verification code";
                            }
                        }else{
                            $code = rand(111111,999999);
                            $invitationDetail->code = $code;
                            $invitationDetail->save();

                            $message = "Your verification code is ".$code;

                            Mail::send('emails/common', array('strTemplate' => $message), function($message) use ($invitationDetail) {
                                $message->to($invitationDetail->email);
                                $message->replyTo(FROM_EMAIL_ADDRESS, FROM_EMAIL_ADDRESS_NAME);
                                $message->subject(SITE_NAME.' - Verification Code');
                            });

                            $response->IsSuccess = true;
                            $response->Message = "Please enter verification code";
                        }
                    }
                }else{
                    $response->Message = "Invitation details not found";
                }
            }else{
                $response->Message = "You have entered invalid invitation details";
            }
        }else{
            $response->Message = $checkRequiredField;
        }
        return $this->GetJsonResponse($response);
    }
}
