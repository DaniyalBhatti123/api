<?php 
    
    class CommonController {

        public static function cleanXssArray($array) {
            foreach ($array as $key => $value) {
                $array[$key] = strip_tags($value);
            }
            return $array;
        }

        public static function getLocation($ip){
            $latlng[] = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$ip));
            return array('lat'=>$latlng[0]['geoplugin_latitude'],'lng'=>$latlng[0]['geoplugin_longitude']);
        }

        public static function cleanName($string) {
            return !preg_match('/[^A-Za-z]/', $string);
        }
        
        public static function sendResponseWithCode($code, $status, $message, $response){
           return $response ->withStatus($code)
               ->withHeader('Content-Type', 'application/json')
               ->write(json_encode((object) array(
                       "status"   => $status,
                       "message"    => $message
               )));
       }

        public static function sendBodyResponseWithCode($code, $status, $message, $body, $response) {
           return $response->withStatus($code)
               ->withHeader('Content-Type', 'application/json')
               ->write(json_encode((object) array(
                       "status"   => $status,
                       "message"    => $message,
                       "data"    => $body
               )));
        }

        public static function checkAuthHeaders($request) {
            $headers = $request->getHeaders();
            $auth_token = $headers['HTTP_TOKEN'];
            if($auth_token) {
                $auth_token = $auth_token[0];
                $authRes = Model::factory('session')
                ->where('token', $auth_token)
                ->find_array();
                if($authRes) return $authRes[0]['user_id'];
                else return 'FAIL';
            } else return 'FAIL';
        }

        public function generateToken() {
            $characters = 'ZYXWVUTSRQPONMLKJIHGFEDCBAabcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ9876543210zyxwvutsrqponmlkjihgfedcba';
            $string = '';
            $max = strlen($characters) - 1;
            for ($i = 0; $i < 268; $i++) {
                $string .= $characters[mt_rand(0, $max)];
            }
            return $string;
        }


    }
?>