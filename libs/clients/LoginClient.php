<?php
    namespace PandaStore\Clients;

    require_once __DIR__."/../Url.php";
    require_once __DIR__."/BaseClient.php";

    class LoginClient extends BaseClient {
        private $loginServiceUrl;
        function __construct($loginServiceUrl) {
            parent::__construct($loginServiceUrl);
            $this->loginServiceUrl = $loginServiceUrl;
        }

        public function checkLogin($email, $passwd) {
            return $this->call("checkLogin", ["email" => $email, "passwd" => $passwd], $this->loginServiceUrl);
        }
    }