<?php

    class CommonController {
        public static function cleanXssArray($array) {
            foreach ($array as $key => $value) {
                $array[$key] = strip_tags($value);
            }
            return $array;
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
    }

?>