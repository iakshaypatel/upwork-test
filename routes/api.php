<?php


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/


Route::group(['prefix' => API_VERSION, 'namespace' => API_NAMESPACE_PREFIX],function(){

    //Login
    Route::post('login','LoginController@postLogin');

    //Register
    Route::post('verify-email','RegisterController@postVerifyEmail');
    Route::post('register','RegisterController@postRegister');

    Route::group(array('middleware' => 'auth.api'),function(){

        //Invitation
        Route::post('send-invitation','InvitationController@sendInvitation');

        //Profile
        Route::post('profile','ProfileController@postProfile');
    });
});