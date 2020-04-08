<?php
    namespace PandaStore\Servers;
    require_once __DIR__."/../../vendor/autoload.php";
    require_once __DIR__."/../clients/DbClient.php";
    require_once __DIR__."/../../util.php";

    use nusoap_server;
    use PandaStore\Clients\DbClient;

    class BaseServer extends nusoap_server {
        protected $pandaServiceUrl;
        protected $dbClient;
        protected $log;
        function __construct($pandaServiceUrl) {
            parent::__construct();
            $this->pandaServiceUrl = $pandaServiceUrl;
            global $DB_SERVICE_URL;
            $this->dbClient = new DbClient($DB_SERVICE_URL);

            $this->configureWSDL('WSDLTST', $pandaServiceUrl);
            $this->wsdl->schemaTargetNamespace = $pandaServiceUrl;



            $this->wsdl->addComplexType("stdMessage",
                "complexType", 
                "struct",
                "all",
                "",
                ["code" => ["name" => "code", "type" => "xsd:int"],
                    "msg"  => ["name" => "msg", "type" => "xsd:string"]
                ]
            );




        }

    }
