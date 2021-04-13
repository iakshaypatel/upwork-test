<?php
define('BASE_NAME',env('APP_URL'));
define('API_DEBUG',false);
define('IS_LIVE',false);
define('API_VERSION','v1');
define('API_NAMESPACE_PREFIX','Api\V1');
define('USER_NAMESPACE_PREFIX','User\V1');
define('DEFAULT_IMG_NAME','kk-693378000.png');
define('SITE_NAME',env('APP_NAME'));
define('FROM_EMAIL_ADDRESS',env('MAIL_FROM_ADDRESS'));
define('FROM_EMAIL_ADDRESS_NAME',SITE_NAME);

// Api All HTTP Response Code 
define("HTTP_UNAUTHORIZED",401);
define("HTTP_FORBIDDEN",403);
define("HTTP_SUCCESS",200);
define("HTTP_INTERNAL_SERVER_ERROR",500);
define("HTTP_NO_DATA_FOUND",404);
define("UNAUTHORIZED_MESSAGE","Unauthorized");
define("FORBIDDEN_MESSAGE","Forbidden");
define('FCM_AUTHORIZATION_KEY', '');
define('FCM_URL','https://fcm.googleapis.com/fcm/send');
