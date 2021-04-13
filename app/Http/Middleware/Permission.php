<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\LuUserType, Auth;

class Permission
{   
    protected $auth;
    
    public function handle($request, Closure $next) 
    {
        if(Auth::user())
        {
            if(!($request->ajax())){

                $auth_details = Auth::user();
                $authID = $auth_details->id;
                $authType = $auth_details->lu_user_type_id;
                $userType = LuUserType::where('id',$authType)->first();
                $appPermission = json_decode($userType->permissions,true);
                
                view()->composer(['layouts/user_master','user/*'],function ($view) use($appPermission, $authType){
                    $view->with(['permissions' => $appPermission, 'authType' => $authType]);
                });
            }
        }
        return $next($request);
        
    }
}