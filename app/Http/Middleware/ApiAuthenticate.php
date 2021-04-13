<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthManager;
use Illuminate\Auth\TokenGuard;
use App\Models\RoleApiPermission;

class ApiAuthenticate
{   
    protected $auth;
    
    public function __construct(Request $request, AuthManager $auth)
    {
        $this->HeaderSecKey = 'Authorization';
        $this->auth = $auth;

    }
    
    public function handle($request, Closure $next, $guard = 'api') 
    {
        if($this->auth->guard('api')->user())
        {
            return $next($request);
        }
        return response(['IsSuccess' => false,'Message' => UNAUTHORIZED_MESSAGE,'ErrorCode' => HTTP_UNAUTHORIZED],HTTP_UNAUTHORIZED);
    }
}