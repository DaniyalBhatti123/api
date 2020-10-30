<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


require 'vendor/autoload.php';
require 'config.php';
require 'controllers/common.php';
require 'controllers/users.php';

//including all models dynamicly
function my_autoloader($className) {
	$arr = preg_split('/(?=[A-Z])/', $className);
	$arr = array_slice($arr, 1);
	$path = false;
	$path = "models/" . $className . ".php";
	if (file_exists($path)) {
		require_once $path;
	}
}
spl_autoload_register("my_autoloader");


$app = new \Slim\App();

$app->post('/common/sign-up', 'UsersController::signUp');
$app->post('/common/login', 'UsersController::login');
$app->post('/common/verify-login', 'UsersController::verifyLogin');
$app->post('/common/logout', 'UsersController::logout');
$app->post('/common/change-password', 'UsersController::changePassword');
$app->post('/admin/make-approved', 'UsersController::makeApproved');
$app->post('/common/image-upload', 'UsersController::uploadFile');
$app->post('/common/update-profile', 'UsersController::updateProfile');
$app->post('/user/post-feedback', 'UsersController::postFeedback');
$app->post('/common/list-mechanic', 'UsersController::listMechanic');
$app->run();
 