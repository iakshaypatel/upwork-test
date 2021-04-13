<?php

namespace App\Infrastructure;
use Illuminate\Support\Facades\Storage;
use Image;

class AppConstant {

	public static $secret_iv = "kroonal123";
    public static $secret_key = "kroonal";
    public static $userImageFolderName = "user-images";
    public static $loading = "loading";
    public static $userID = 1;
    public static $userInvitationID = 1;

    //User image
    public static function getUserImage($imageName){
        return asset('storage/app/'.self::$userImageFolderName.'/'.$imageName);
    }

    public static function storeUserImage($image){
        $storeFile = $image->store(self::$userImageFolderName);
        $fileName = str_replace(self::$userImageFolderName."/", "", $storeFile);
        return $fileName;
    }

    public static function deleteUserImage($imageName){
        Storage::delete(self::$userImageFolderName.'/'.$imageName);
    }
}
