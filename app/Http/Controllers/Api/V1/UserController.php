<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseController;

use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use App\Infrastructure\ServiceResponse, App\Infrastructure\AppConstant;
use App\Models\User, App\Models\UserEducation, App\Models\UserWorkExperience, App\Models\UserLike, App\Models\UserView, App\Models\CenterCertificate;
use Auth;

class UserController extends BaseController
{
    /**
    * Update User profile
    * Method POST
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function createUserProfile(Request $request){
        $reqData = $request->all();
        $response = new ServiceResponse;
        $checkFields = array('name','weight','height','age');
        $checkRequiredField = $this->checkRequestData($checkFields,$reqData);

        if($checkRequiredField == 'SUCC100'){
            $name = $reqData['name'];
            $weight = $reqData['weight'];
            $height = $reqData['height'];
            $age = $reqData['age'];
            $email = $reqData['email'];
            // $device_token = $reqData['device_token'];
            
            $authDetail = Auth::guard('api')->user();
            $authID = $authDetail->id;
            $authDetail->name = $name;
            $authDetail->email = $email;
            $authDetail->weight = $weight;
            $authDetail->height = $height;
            if(!empty($reqData['user_type'])){
                $authDetail->lu_user_type_id = $reqData['user_type'];
            }else{
                $authDetail->lu_user_type_id = 5;
            }
            
            if(!empty($reqData['sub_user_type'])){
                $authDetail->lu_sub_user_type_id = $reqData['sub_user_type'];
            }

            if(!empty($reqData['about'])){
                $authDetail->about = $reqData['about'];
            }
            // $authDetail->device_token = $device_token;
            $authDetail->age = $age;
            if(!empty($reqData['image'])){
                $imageName = AppConstant::storeUserImage($reqData['image']);
                $authDetail->image = $imageName;
            }else{
                $authDetail->image = DEFAULT_IMG_NAME;
            }

            if(!empty($reqData['cover_image'])){
                $imageName = AppConstant::storeUserImage($reqData['cover_image']);
                $authDetail->cover_image = $imageName;
            }

            if(!empty($reqData['certificates'])){
                foreach($reqData['certificates'] as $certificate){
                    $galleryRecord = new CenterCertificate;
                    $galleryRecord->user_id = $authID;
                    $galleryRecord->name = /*!empty($certificate['name']) ? $certificate['name'] : */NULL;
                    $galleryRecord->image = AppConstant::storeCenterCertificate($certificate);
                    $galleryRecord->save();        
                }
            }

            $authDetail->is_profile_completed = 1;
            $authDetail->save();
            
            $response->Data = CommonController::loginResponse($authDetail);
            $response->IsSuccess = true;
            $response->Message ="Profile created successfully";
        }else{
            $response->Message = $checkRequiredField;
        }
        return $this->GetJsonResponse($response);
    }

    /**
    * Get Education
    * Method POST
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function getEducation(Request $request){
        $reqData = $request->all();
        $response = new ServiceResponse;
        
        $authDetail = Auth::guard('api')->user();
        $authID = $authDetail->id;

        $educationDetails = UserEducation::where('user_id',$authID)->get();
        if(!empty($educationDetails)):
            foreach($educationDetails as $key => $educationDetail):
                $educationDetails[$key]['degree'] = $this->convertNullToChar($educationDetail['degree']);
                $educationDetails[$key]['study_field'] = $this->convertNullToChar($educationDetail['study_field']);
                $educationDetails[$key]['start_date'] = $this->convertNullToChar($educationDetail['start_date']);
                $educationDetails[$key]['end_date'] = $this->convertNullToChar($educationDetail['end_date']);
                $educationDetails[$key]['grade'] = $this->convertNullToChar($educationDetail['grade']);
                $educationDetails[$key]['activities'] = $this->convertNullToChar($educationDetail['activities']);
                $educationDetails[$key]['description'] = $this->convertNullToChar($educationDetail['description']);
                $educationDetails[$key]['deleted_at'] = $this->convertNullToChar($educationDetail['deleted_at']);
            endforeach;
        endif;

        $response->Data = $educationDetails;
        $response->IsSuccess = true;
        return $this->GetJsonResponse($response);
    }

    /**
    * Store Education
    * Method POST
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function storeEducation(Request $request){
        $reqData = $request->all();
        $response = new ServiceResponse;
        $checkFields = array('school_name');
        $checkRequiredField = $this->checkRequestData($checkFields,$reqData);

        if($checkRequiredField == 'SUCC100'){
            $authDetail = Auth::guard('api')->user();
            $authID = $authDetail->id;

            $schoolName = $reqData['school_name'];
            $id = !empty($reqData['id']) ? $reqData['id'] : 0;

            $record = $id > 0 ? UserEducation::find($id) : new UserEducation;
            $record->user_id = $authID;
            $record->school_name = $schoolName;
            $record->degree = !empty($reqData['degree']) ? $reqData['degree'] : NULL;
            $record->study_field = !empty($reqData['study_field']) ? $reqData['study_field'] : NULL;
            $record->start_date = !empty($reqData['start_date']) ? sql_date($reqData['start_date']) : NULL;
            $record->end_date = !empty($reqData['end_date']) ? sql_date($reqData['end_date']) : NULL;
            $record->grade = !empty($reqData['grade']) ? $reqData['grade'] : NULL;
            $record->activities = !empty($reqData['activities']) ? $reqData['activities'] : NULL;
            $record->description = !empty($reqData['description']) ? $reqData['description'] : NULL;
            $record->save();
            
            $response->Message = $id > 0 ? "Your education details has been updated successfully" : "Your education details has been added successfully";
            $response->IsSuccess = true;
        }else{
            $response->Message = $checkRequiredField;
        }
        return $this->GetJsonResponse($response);
    }

    /**
    * Delete Education
    * Method POST
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function deleteEducation(Request $request){
        $reqData = $request->all();
        $response = new ServiceResponse;
        $checkFields = array('id');
        $checkRequiredField = $this->checkRequestData($checkFields,$reqData);

        if($checkRequiredField == 'SUCC100'){
            $id = $reqData['id'];
            $record = UserEducation::find($id);
            if(!empty($record)){
                $record->delete();
                $response->IsSuccess = true;  
                $response->Message = "Your education detail has been deleted successfully";
            }else{
                $response->Message = "Record not found";
            }
        }else{
            $response->Message = $checkRequiredField;
        }
        return $this->GetJsonResponse($response);
    }

    /**
    * Get Work Experience
    * Method POST
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function getWorkExperience(Request $request){
        $reqData = $request->all();
        $response = new ServiceResponse;
        
        $authDetail = Auth::guard('api')->user();
        $authID = $authDetail->id;

        $educationDetails = UserWorkExperience::where('user_id',$authID)->get();
        if(!empty($educationDetails)):
            foreach($educationDetails as $key => $educationDetail):
                $educationDetails[$key]['company_name'] = $this->convertNullToChar($educationDetail['company_name']);
                $educationDetails[$key]['address'] = $this->convertNullToChar($educationDetail['address']);
                $educationDetails[$key]['start_date'] = $this->convertNullToChar($educationDetail['start_date']);
                $educationDetails[$key]['end_date'] = $this->convertNullToChar($educationDetail['end_date']);
                $educationDetails[$key]['description'] = $this->convertNullToChar($educationDetail['description']);
                $educationDetails[$key]['deleted_at'] = $this->convertNullToChar($educationDetail['deleted_at']);
            endforeach;
        endif;

        $response->Data = $educationDetails;
        $response->IsSuccess = true;
        return $this->GetJsonResponse($response);
    }

    /**
    * Store Work Experience
    * Method POST
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function storeWorkExperience(Request $request){
        $reqData = $request->all();
        $response = new ServiceResponse;
        $checkFields = array('title');
        $checkRequiredField = $this->checkRequestData($checkFields,$reqData);

        if($checkRequiredField == 'SUCC100'){
            $authDetail = Auth::guard('api')->user();
            $authID = $authDetail->id;

            $title = $reqData['title'];
            $id = !empty($reqData['id']) ? $reqData['id'] : 0;

            $record = $id > 0 ? UserWorkExperience::find($id) : new UserWorkExperience;
            $record->user_id = $authID;
            $record->title = $title;
            $record->company_name = !empty($reqData['company_name']) ? $reqData['company_name'] : NULL;
            $record->address = !empty($reqData['address']) ? $reqData['address'] : NULL;
            $record->start_date = !empty($reqData['start_date']) ? sql_date($reqData['start_date']) : NULL;
            $record->end_date = !empty($reqData['end_date']) ? sql_date($reqData['end_date']) : NULL;
            $record->description = !empty($reqData['description']) ? $reqData['description'] : NULL;
            $record->save();
            
            $response->Message = $id > 0 ? "Your work experience details has been updated successfully" : "Your work experience details has been added successfully";
            $response->IsSuccess = true;
        }else{
            $response->Message = $checkRequiredField;
        }
        return $this->GetJsonResponse($response);
    }

    /**
    * Delete Work Education
    * Method POST
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function deleteWorkExperience(Request $request){
        $reqData = $request->all();
        $response = new ServiceResponse;
        $checkFields = array('id');
        $checkRequiredField = $this->checkRequestData($checkFields,$reqData);

        if($checkRequiredField == 'SUCC100'){
            $id = $reqData['id'];
            $record = UserWorkExperience::find($id);
            if(!empty($record)){
                $record->delete();
                $response->IsSuccess = true;  
                $response->Message = "Your education detail has been deleted successfully";
            }else{
                $response->Message = "Record not found";
            }
        }else{
            $response->Message = $checkRequiredField;
        }
        return $this->GetJsonResponse($response);
    }

    /**
    * Store Like User
    * Method POST
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function storeLikeUser(Request $request){
        $reqData = $request->all();
        $response = new ServiceResponse;
        $checkFields = array('user_id');
        $checkRequiredField = $this->checkRequestData($checkFields,$reqData);

        if($checkRequiredField == 'SUCC100'){
            $authDetail = Auth::guard('api')->user();
            $authID = $authDetail->id;
            $toUserID = $reqData['user_id'];

            $checkFeedRecord = UserLike::where(['to_user_id'=>$toUserID, 'user_id'=>$authID])->first();
            if(empty($checkFeedRecord)){
                $feedLikeRecord = new UserLike;
                $feedLikeRecord->to_user_id = $toUserID;
                $feedLikeRecord->user_id = $authID;
                $feedLikeRecord->save();

                $response->IsSuccess = true;
                $response->Message = "User like successfully.";
            }else{
                $response->Message = "User already like.";
            }
        }else{
            $response->Message = $checkRequiredField;
        }
        return $this->GetJsonResponse($response);
    }

    /**
    * Store View User
    * Method POST
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function storeViewUser(Request $request){
        $reqData = $request->all();
        $response = new ServiceResponse;
        $checkFields = array('user_id');
        $checkRequiredField = $this->checkRequestData($checkFields,$reqData);

        if($checkRequiredField == 'SUCC100'){
            $authDetail = Auth::guard('api')->user();
            $authID = $authDetail->id;
            $toUserID = $reqData['user_id'];

            $checkFeedRecord = UserView::where(['to_user_id'=>$toUserID, 'user_id'=>$authID])->first();
            if(empty($checkFeedRecord)){
                $feedLikeRecord = new UserView;
                $feedLikeRecord->to_user_id = $toUserID;
                $feedLikeRecord->user_id = $authID;
                $feedLikeRecord->save();

                $response->IsSuccess = true;
            }
        }else{
            $response->Message = $checkRequiredField;
        }
        return $this->GetJsonResponse($response);
    }

    /**
    * Store Unlike User
    * Method POST
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function storeUnlikeUser(Request $request){
        $reqData = $request->all();
        $response = new ServiceResponse;
        $checkFields = array('user_id');
        $checkRequiredField = $this->checkRequestData($checkFields,$reqData);

        if($checkRequiredField == 'SUCC100'){
            $authDetail = Auth::guard('api')->user();
            $authID = $authDetail->id;
            $toUserID = $reqData['user_id'];

            UserLike::where(['to_user_id'=>$toUserID, 'user_id'=>$authID])->delete();

            $response->IsSuccess = true;
            $response->Message = "User unlike successfully.";
        }else{
            $response->Message = $checkRequiredField;
        }
        return $this->GetJsonResponse($response);
    }
}
