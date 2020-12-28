<?php
    class userController {
        public static function login ($request,$response,$args){
            $postData = $request->getParams();

            $user = Model::Factory('user')->where('email',$postData['email'])->where('password',$postData['password'])->find_one();
            if($user){
                CommonController::sendResponseWithCode(200,'OK','Login successfully',$response);
            } else CommonController::sendResponseWithCode(200,'FAIL','Username or Password Invalid',$response);
        }
    }
?>