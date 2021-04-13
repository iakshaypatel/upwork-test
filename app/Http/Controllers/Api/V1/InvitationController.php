<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Infrastructure\AppConstant;
use App\Infrastructure\ServiceResponse;
use App\Models\User, App\Models\UserInvitation;
use Mail, Auth, Validator;

class InvitationController extends BaseController
{

    /**
     * Validate Login
     * Method POST
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendInvitation(Request $request)
    {
        $reqData = $request->all();
        $response = new ServiceResponse;
        $checkFields = array('email');
        $checkRequiredField = $this->checkRequestDataAPI($checkFields, $reqData);

        if ($checkRequiredField == 'SUCC100') {
            $email = $reqData['email'];

            //Check user is exists or not
            $userRecord = User::where('email',$email)->first();
            if(!empty($userRecord)){
                $response->Message ="Email address is already exists.";
            }else{
                $record = UserInvitation::where('email', $email)->first();

                if(!empty($record)){
                    $response->Message ="Invitation is already sent.";
                }else{
                    $authDetail = Auth::guard('api')->user();

                    $record = new UserInvitation;
                    $record->invited_by = $authDetail->id;
                    $record->email = $email;
                    $record->created_at = now();
                    $record->save();

                    $message = "Please pass this data in verify-email api " .$this->getEncryptID(AppConstant::$userInvitationID,$record->id);

                    Mail::send('emails/common', array('strTemplate' => $message), function($message) use ($record) {
                        $message->to($record->email);
                        $message->replyTo(FROM_EMAIL_ADDRESS, FROM_EMAIL_ADDRESS_NAME);
                        $message->subject(SITE_NAME.' - Invitation');
                    });

                    $response->Message ="Invitation has been sent successfully.";
                    $response->IsSuccess = true;
                }
            }
        } else {
            $response->Message = $checkRequiredField;
        }
        return $this->GetJsonResponse($response);
    }
}
