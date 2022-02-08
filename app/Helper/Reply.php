<?php

    namespace App\Helper;

    use Illuminate\Contracts\Validation\Validator;

    class Reply
    {

        /** Return success response
         * @param $message
         * @return array
         */
        public static function success($message) {
            return [
                "success" => true,
                "message" => Reply::getTranslated($message)
            ];
        }

        public static function successData($data) {
            return [
                "success" => true,
                "data" =>   $data
            ];
        }

        public static function successWithData($message, $data) {
            $response = Reply::success($message);

            return array_merge($response, ["data"   =>  $data]);
        }

        /** Return error response
         * @param $message
         * @return array
         */
        public static function error($message, $error_name = null, $errorData = []) {
            return [
                "success" => false,
                "error_name" => $error_name,
                "data" => $errorData,
                "message" => Reply::getTranslated($message)
            ];
        }

        /** Return validation errors
         * @param \Illuminate\Validation\Validator|Validator $validator
         * @return array
         */
        public static function formErrors($validator) {
            return [
                "success" => false,
                "errors" => $validator->getMessageBag()->toArray()
            ];
        }

        /** Response with redirect action. This is meant for ajax responses and is not meant for direct redirecting
         * to the page
         * @param $url string to redirect to
         * @param null $message Optional message
         * @return array
         */
        public static function redirect($url, $message = null) {
            if ($message) {
                return [
                    "success" => true,
                    "message" => $message,
                    "action" => "redirect",
                    "url" => $url
                ];
            }
            else {
                return [
                    "success" => true,
                    "action" => "redirect",
                    "url" => $url
                ];
            }
        }

        private static function getTranslated($message) {
            $trans = trans($message);

            if ($trans == $message) {
                return $message;
            }
            else {
                return $trans;
            }
        }

        public static function dataOnly($data) {
            return $data;
        }

        public static function redirectWithError($url, $message = null) {
            if ($message) {
                return [
                    "success" => false,
                    "message" => $message,
                    "action" => "redirect",
                    "url" => $url
                ];
            }
            else {
                return [
                    "success" => false,
                    "action" => "redirect",
                    "url" => $url
                ];
            }
        }

    }
