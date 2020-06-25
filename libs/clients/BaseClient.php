<?php
    namespace PandaStore\Clients;
    require_once __DIR__."/../vendor/autoload.php";
    use nusoap_client;

    class BaseClient extends nusoap_client {
        protected $pandaServiceUrl;
        protected $log;

        function __construct($pandaServiceUrl) {
            parent::__construct("$pandaServiceUrl?wsdl", "wsdl");
            $this->pandaServiceUrl = $pandaServiceUrl;
        }
    }