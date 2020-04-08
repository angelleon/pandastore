<?php
    namespace PandaStore\Util;
    class PasswdChecker {
        static function checkPasswd($rawPasswd) {
            if (preg_match('/[0-9]/', $rawPasswd) != 1) {
                return ["code" => -1, "msg" => "Password must contain at least one number"];
            } else if (preg_match('/[a-z]/', $rawPasswd) != 1) {
                return ["code" => -2, "msg" => "Password must contain at least one lowercase letter"];
            } else if (preg_match('/[A-Z]/', $rawPasswd) != 1) {
                return ["code" => -3, "msg" => "Password must contain at least one upeprcase letter"];
            } else if (preg_match('/[ !"#$%&\'()*+,\-.\/:;<=>?@[\]^_`{|}~]/', $rawPasswd) != 1 &&
                preg_match('/\\\/', $rawPasswd) != 1) {
                return ["code" => -4, "msg" => "Password must contain at least one of ! \"#$%&'()*+,-./:;<=>?@[]^_`{|}~\\"];
            } else if (preg_match('/.{8,32}/', $rawPasswd) != 1) {
                return ["code" => -4, "msg" => "Password must have between 8 and 32 characters long"];
            } else if (preg_match('/[^ !"#$%&\'()*+,\-.\/:;<=>?@[\]^_`{|}~\\a-zA-Z0-9]/', $rawPasswd) == 1) {
                return ["code" => -4, "msg" => "Password contains not permitted characters"];
            }
            return ["code" => 1, "msg" => ""];
        }
    }