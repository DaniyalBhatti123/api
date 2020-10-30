<?php
class UsersController{

    /**
        * @api {post} /common/sign-up Sign Up API
        * @apiName Sign Up API
        * @apiGroup User and Mechanic
        *
        * @apiExample JSON-Body:
        *    {
        *       "name":"Mr Imran",
        *       "contact":"123456780",
        *       "password":"123",
        *       "role":"2",
        *       "email":"mr.imran@gmail.com",
        *       "address":"karachi Pakistan",
        *       "speciality":"denter master",
        *       "description":"I am a good Denter",
        *       "lat":"24.45657899",
        *       "lng":"67.09876754"
        *   }
        *
        * @apiSuccess {string} status Status of the request.
        * @apiSuccess {string} message Message corresponding to request.
    */
    public static function signUp($request, $response, $args){
        $postdata = CommonController::cleanXssArray($request->getParams());
        if(isset($postdata['name']) && !empty($postdata['name'])){
            if(isset($postdata['contact']) && !empty($postdata['contact'])){
                if(isset($postdata['email']) && !empty($postdata['email']) && filter_var($postdata['email'], FILTER_VALIDATE_EMAIL)){
                    if(isset($postdata['password']) && !empty($postdata['password'])){
                        if(isset($postdata['role']) && !empty($postdata['role']) && ($postdata['role']==2 || $postdata['role']==3)){
                            $isExist = Model::factory('users')->where('email',$postdata['email'])->find_array();
                            if(!$isExist){
                                $isContactExist = Model::factory('users')->where('contact_number',$postdata['contact'])->find_array();
                                if(!$isContactExist){
                                    $ip = $_SERVER["REMOTE_ADDR"];
                                    $data = Model::factory('users')->create();
                                    $data->name             = $postdata['name'];
                                    $data->contact_number   = $postdata['contact'];
                                    $data->email            = $postdata['email'];
                                    $data->role_id          = $postdata['role'];
                                    $data->password         = md5($postdata['password']);
                                    $data->ip               = $ip;
                                    $data->lat              = CommonController::getLocation($ip)['lat'];
                                    $data->lng              = CommonController::getLocation($ip)['lng'];
                                    $data->specialty        = ($postdata['role']==2)? $postdata['speciality'] : 'None';
                                    $data->description      = ($postdata['role']==2)? $postdata['description'] : 'None';
                                    $data->created_at       = date('Y-m-d');
                                    if($data->save()){
                                        CommonController::sendResponseWithCode(200,'OK','Successfully Registerd',$response);
                                    } else  CommonController::sendResponseWithCode(403,'FAIL','Internal Server Error',$response);
                                } else CommonController::sendResponseWithCode(200, 'FAIL', 'this contact is registered',$response);
                            } else CommonController::sendResponseWithCode(200, 'FAIL', 'this email is alerady registred',$response);
                        } else CommonController::sendResponseWithCode(200, 'FAIL', 'role is require it can (2 or 3) 2 for user and 3 for mechanic!',$response);
                    } else CommonController::sendResponseWithCode(200, 'FAIL', 'password is missing!',$response);
                } else CommonController::sendResponseWithCode(200, 'FAIL', 'email missing or invalid!',$response);
            } else CommonController::sendResponseWithCode(200, 'FAIL', 'contact is missing!',$response);
        } else CommonController::sendResponseWithCode(200, 'FAIL', 'name is missing!',$response);
    }

    /**
        * @api {post} /common/login Login API
        * @apiName Login API
        * @apiGroup User and Mechanic
        *
        * @apiExample JSON-Body:
        *   {
        *       "email":"mr.imran@gmail.com",
        *       "password":"123"
        *   }
        *
        * @apiSuccess {string} status Status of the request.
        * @apiSuccess {string} message Message corresponding to request.
    */
    public static function login($request,$response,$args){
        $postdata = CommonController::cleanXssArray($request->getParams());
        if(isset($postdata['email']) && !empty($postdata['email'])){
            if(isset($postdata['password']) && !empty($postdata['password'])){
                $result = Model::factory('users')
                ->where('email',$postdata['email'])
                ->where('password',md5($postdata['password']))
                ->find_array();
                if($result){
                    $token      =   CommonController::generateToken();
                    $session    =   Model::factory('session')->create();
                    $session->user_id = $result[0]['id'];
                    $session->token = $token;
                    if($session->save()){
                        CommonController::sendBodyResponseWithCode(200,'OK','session generated',array('token'=>$token),$response);
                    }else CommonController::sendResponseWithCode(403,'FAIL','Internal server error',$response);
                }else CommonController::sendResponseWithCode(500,'FAIL','Username or Password Invalid',$response);
            }else CommonController::sendResponseWithCode(500, 'FAIL', 'Please inter password',$response);
        }else CommonController::sendResponseWithCode(500, 'FAIL', 'Please inter username',$response);
    }

    /**
        * @api {post} /common/verify-login Verify Login API
        * @apiName Verify Login API
        * @apiGroup User and Mechanic
        *
        * @apiHeader {string} token Token
        *
        * @apiSuccess {string} status Status of the request.
        * @apiSuccess {string} message Message corresponding to request.
    */
    public static function verifyLogin($request,$response,$args){
        $userId = CommonController::checkAuthHeaders($request);
        if($userId!='FAIL'){
            $user = Model::factory('users')
            ->table_alias('u')
            ->join('orvba_roles', 'u.role_id=r.id', 'r')
            ->where('u.id',$userId)
            ->find_array();
            if($user){
                unset($user[0]['password']);
                CommonController::sendBodyResponseWithCode(200,'OK','User found',$user,$response);
            }else CommonController::sendResponseWithCode(500,'FAIL','no user found',$response);
        }else CommonController::sendResponseWithCode(500, 'FAIL', 'token not found',$response);
    }

    /**
        * @api {post} /common/logout Logout API
        * @apiName Logout API
        * @apiGroup User and Mechanic
        *
        * @apiHeader {string} token Token
        *
        * @apiSuccess {string} status Status of the request.
        * @apiSuccess {string} message Message corresponding to request.
    */
    public static function logout($request,$response,$args){
        $userId = CommonController::checkAuthHeaders($request);
        $headers = $request->getHeaders();
        $auth_token = $headers['HTTP_TOKEN'];
        $token = $auth_token[0];
        if($userId!='FAIL'){
            $getsess = Model::factory('session')
            ->where('token',$token)
            ->find_one();
            if($getsess){
                $deleted = $getsess->delete();
                if($deleted) CommonController::sendResponseWithCode(200,'OK','user logout successfuly',$response);
                else CommonController::sendResponseWithCode(500,'FAIL','some temporary problem',$response);
            } else CommonController::sendResponseWithCode(500,'FAIL','session not found',$response);
        } else CommonController::sendResponseWithCode(500,'FAIL','token not found',$response);
    }


    /**
        * @api {post} /common/change-password Change Password API
        * @apiName Change Password API
        * @apiGroup User and Mechanic
        *
        * @apiExample JSON-Body:
        *   {
        *       "oldPassword":"123",
        *       "newPassword":"444"
        *   }
        *
        * @apiHeader {string} token Token
        *
        * @apiSuccess {string} status Status of the request.
        * @apiSuccess {string} message Message corresponding to request.
    */
    public static function changePassword($request,$response,$args){
        $userId = CommonController::checkAuthHeaders($request);
        if($userId!='FAIL'){
            $postdata = CommonController::cleanXssArray($request->getParams());
            if(isset($postdata['oldPassword']) && !empty($postdata['oldPassword'])){
                if(isset($postdata['newPassword']) && !empty($postdata['newPassword'])){
                    $old = md5($postdata['oldPassword']);
                    $new = md5($postdata['newPassword']);
                    $isFound = Model::factory('users')->where('id',$userId)->where('password',$old)->find_one();
                    if($isFound){
                        $isFound->password = $new;
                        $updated = $isFound->save();
                        if($updated) CommonController::sendResponseWithCode(200, 'OK', 'password updated successfuly',$response);
                        else CommonController::sendResponseWithCode(500, 'FAIL', 'some temporary problem',$response);
                    }else CommonController::sendResponseWithCode(500, 'FAIL', 'old password does not match',$response);
                }else CommonController::sendResponseWithCode(500, 'FAIL', 'Please enter new password',$response);
            }else CommonController::sendResponseWithCode(500, 'FAIL', 'Please enter old password',$response);
        }else CommonController::sendResponseWithCode(500, 'FAIL', 'token not found',$response);
    }

    /**
        * @api {post} /admin/make-approved Make Approve API
        * @apiName Make Approve API
        * @apiGroup Admin
        *
        * @apiExample JSON-Body:
        *   {
        *       "userId":"4"
        *   }
        *
        * @apiHeader {string} token Token
        *
        * @apiSuccess {string} status Status of the request.
        * @apiSuccess {string} message Message corresponding to request.
    */
    public static function makeApproved($request,$response,$args){
        $userId = CommonController::checkAuthHeaders($request);
        if($userId!='FAIL'){
            if($userId==1){
                $postdata = CommonController::cleanXssArray($request->getParams());
                if(isset($postdata['userId']) && !empty($postdata['userId'])){
                    $isExist = Model::factory('users')->where('id',$postdata['userId'])->find_one();
                    if($isExist){
                        $isExist->is_approved = '1';
                        if($isExist->save()) CommonController::sendResponseWithCode(200, 'OK', 'Mechanice Approved successfully',$response);
                        else CommonController::sendResponseWithCode(500, 'FAIL', 'Internal server Error',$response);
                    }else CommonController::sendResponseWithCode(500, 'FAIL', 'There is no user with given userId',$response);
                } else CommonController::sendResponseWithCode(500, 'FAIL', 'userId is missing',$response);
            } else CommonController::sendResponseWithCode(500, 'FAIL', 'you are not allowed to perform this action',$response);
        }else CommonController::sendResponseWithCode(500, 'FAIL', 'token not found',$response);
    }

    /**
        * @api {post} /common/image-upload Image Upload API
        * @apiName Image Upload API
        * @apiGroup User and Mechanic
        * 
        * @apiParam {file} imageFile Image File
        *
        * @apiSuccess {string} status Status of the request.
        * @apiSuccess {string} message Message corresponding to request.
    */
    public static function uploadFile($request,$response){
        $newFileName = rand(1111,9999).time() . '.' . strtolower(pathinfo($_FILES["imageFile"]["name"], PATHINFO_EXTENSION));
        $target_dir = "cdn/images/";
        $target_file = $target_dir . $newFileName;
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        
        if (file_exists($target_file)) {
            CommonController::sendResponseWithCode(500,'FAIL','Sorry, file already exists.',$response);
        }else if($_FILES["imageFile"]["size"] > 10000000){
            CommonController::sendResponseWithCode(500,'FAIL','Sorry, your file is too large.',$response);
        }else if($imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "jpg"){
            CommonController::sendResponseWithCode(500,'FAIL','Sorry, only image file is allowed.',$response);
        }
        else {
            if (move_uploaded_file($_FILES["imageFile"]["tmp_name"], $target_file)) {
                CommonController::sendBodyResponseWithCode(200,'OK','Image Uploaded Successfuly',array("url"=>"cdn/images/".$newFileName),$response);
            } else CommonController::sendResponseWithCode(500,'FAIL','Sorry, there was an error uploading your file',$response);
        }
    }

    /**
        * @api {post} /common/update-profile Update Profile API
        * @apiName Update Profile API
        * @apiGroup User and Mechanic
        *
        * @apiExample JSON-Body:
        *   {
        *       "name":"Imran Bhai",
        *       "contact":"98918227342",
        *       "speciality":"Painter",
        *       "description":"I am a good painter",
        *       "profileImage":"cdn/images/63971603221353.png"
        *   }
        *
        * @apiHeader {string} token Token
        *
        * @apiSuccess {string} status Status of the request.
        * @apiSuccess {string} message Message corresponding to request.
    */
    public static function updateProfile($request,$response){
        $userId = CommonController::checkAuthHeaders($request);
        if($userId!='FAIL'){
            $postdata = CommonController::cleanXssArray($request->getParams());
            $getUser = Model::factory('users')->where('id',$userId)->find_one();
            if($getUser){
                $getUser->name              = isset($postdata['name'])? $postdata['name'] : $getUser->name;
                $getUser->contact_number    = isset($postdata['contact'])? $postdata['contact'] : $getUser->contact_number;
                $getUser->specialty         = isset($postdata['speciality'])? $postdata['speciality'] : $getUser->specialty;
                $getUser->description       = isset($postdata['description'])? $postdata['description'] : $getUser->description;
                $getUser->profile_image     = isset($postdata['profileImage'])? $postdata['profileImage'] : $getUser->profile_image;
                $getUser->updated_at     = date('Y-m-d');
                if($getUser->save()) CommonController::sendResponseWithCode(200,'OK','Profile Updated Successfully',$response);
                else CommonController::sendResponseWithCode(500,'FAIL','Internal server Error',$response);
            } else CommonController::sendResponseWithCode(500,'FAIL','No user found',$response);
        }else CommonController::sendResponseWithCode(500,'FAIL','token not found',$response);
    }

    
    /**
        * @api {post} /user/post-feedback Post Feedback API
        * @apiName Post Feedback API
        * @apiGroup User
        *
        * @apiExample JSON-Body:
        *   {
        *       "mechanicId":"22",
        *       "feedback":"3",
        *       "comment":"good work"
        *   }
        *
        * @apiHeader {string} token Token
        *
        * @apiSuccess {string} status Status of the request.
        * @apiSuccess {string} message Message corresponding to request.
    */
    public static function postFeedback($request,$response){
        $userId = CommonController::checkAuthHeaders($request);
        if($userId!='FAIL'){
            $postdata = CommonController::cleanXssArray($request->getParams());
            if(isset($postdata['mechanicId']) && !empty($postdata['mechanicId'])){
                if(isset($postdata['feedback']) && !empty($postdata['feedback'])){
                    $addFeed = Model::factory('feedback')->create();
                    $addFeed->user_id = $userId;
                    $addFeed->mech_id = $postdata['mechanicId'];
                    $addFeed->feedback = $postdata['feedback'];
                    $addFeed->comment = $postdata['comment'];
                    if($addFeed->save()){
                        CommonController::sendResponseWithCode(200,'OK','Feedback Submitted',$response);
                    } else CommonController::sendResponseWithCode(500,'FAIL','Internal Server Error',$response);
                }else CommonController::sendResponseWithCode(500,'FAIL','Feedback is Missing',$response);
            }else CommonController::sendResponseWithCode(500,'FAIL','mechanicId is Missing',$response);
        }else CommonController::sendResponseWithCode(500,'FAIL','token not found',$response);
    }

    /**
        * @api {post} /common/list-mechanic List Mechanic API
        * @apiName List Mechanic API
        * @apiGroup User and Admin
        *
        * @apiExample JSON-Body:
        *   {
        *       "limit":"10",
        *       "offset":"0",
        *       "name":"imran"
        *   }
        *
        * @apiHeader {string} token Token
        *
        * @apiSuccess {string} status Status of the request.
        * @apiSuccess {string} message Message corresponding to request.
    */
    public static function listMechanic($request,$response){
        $userId = CommonController::checkAuthHeaders($request);
        if($userId!='FAIL'){
            $postdata = CommonController::cleanXssArray($request->getParams());
            $limit  = isset($postdata['limit'])? $postdata['limit'] : 10;
            $offset = isset($postdata['offset'])? $postdata['offset'] : 0;
            $name = isset($postdata['name'])? $postdata['name'] : "";
            $count = count(Model::factory('users')->table_alias('u')->join('orvba_roles', 'u.role_id=r.id', 'r')->where('role_id',2)->where_like('name','%'.$name.'%')->find_array());
            $getUser = Model::factory('users')
            ->table_alias('u')
            ->join('orvba_roles', 'u.role_id=r.id', 'r')
            ->where('role_id',2)
            ->where_like('name','%'.$name.'%')
            ->limit($limit)
            ->offset($offset)
            ->find_array();
            for($i=0;$i<sizeof($getUser);$i++) unset($getUser[$i]['password']);
            if($getUser){
                CommonController::sendBodyResponseWithCode(200,'OK','Mechanic fetch successfully',array('total'=>$count,'list'=>$getUser),$response);
            }else CommonController::sendBodyResponseWithCode(200,'OK','No Record Found',array('total'=>0,'list'=>[]),$response);
        }else CommonController::sendResponseWithCode(500,'FAIL','token not found',$response);
    }
}
?>