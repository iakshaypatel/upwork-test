<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserInvitation extends Model
{

	public $timestamps = false;
	
    /**
    * Get Table Name
    **/
    public static function getTableName(){
        return ((new self)->getTable());
    }
}
