<?php

namespace App\Http\Controllers;

use App\Infrastructure\AppConstant;

class CommonController extends Controller
{
    /**
     * Login API Response
     *
     * @param object $userDetail
     * @return array $response
     */
    public static function loginResponse($userDetail){
        $response = array(
            'id' => $userDetail->id,
            'name' => $userDetail->name,
            'user_name' => $userDetail->user_name,
            'email' => $userDetail->email,
            'registered_at' => $userDetail->registered_at,
            'image' => AppConstant::getUserImage($userDetail->image),
            'api_token' => $userDetail->api_token
        );
        return $response;
    }
}
