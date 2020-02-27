<?php
    require_once __DIR__."/../vendor/autoload.php";

    include_once __DIR__."/../util.php";

    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;
    use Monolog\Formatter\LineFormatter;

    $formatter = new LineFormatter(null, null, true, true);
    $handler = new StreamHandler(__DIR__."/../logs/".basename(__FILE__).".log", Logger::DEBUG);
    $handler->setFormatter($formatter);

    $log = new Logger(basename(__FILE__));
    $log->pushHandler($handler);

    $SERVICE_URL = "$SERVICES_URL/".basename(__FILE__);

    nusoap_base::setGlobalDebugLevel(9);
    $server = new soap_server();
    $server->configureWSDL("WSDLTST", $SERVICE_URL);
    $server->wsdl->schemaTargetNamespace = $SERVICE_URL;

    $server->register("GET",
                      array("statement" => "tns:dbStatement"),
                      array("return" => "tns:stdMessage"),
                      $SERVICE_URL);

    $server->register("POST");

    $server->register("PATCH");

    $server->register("DELETE");

    function GET($statement) {
        global $log;
        global $server;
        $log->debug($server->getDebug());
        $log->debug($server->request);
        $DB_HOST = 'localhost';
        $DB_PORT = 3306;
        $DB_USER = 'panda_store';
        $DB_PASSWD = 'panda_store';
        $DB_NAME = 'PandaStore';
        $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASSWD, $DB_NAME, $DB_PORT);
        if ($conn->connect_errno) {
            //die('Can not connect to db');
            return new soapval('return', 'xsd:string', "can not connect to db");
        }
        //echo "$username $passwd<br>";
        $QUERY = "SELECT idUser FROM User WHERE username = ? AND passwd = ? LIMIT 1";
        $stmt = $conn->prepare($QUERY);
        $stmt->bind_param('ss', $username, $passwd);
        $status = $stmt->execute();
        if (!$status) {
            //die('can not execute sql statement');
            return new soapval('return', 'xsd:string', "can not execute sql statement");
        }
        $idUser = null;
        $stmt->bind_result($idUser);
        $stmt->store_result();
        if ($stmt->num_rows) {
            return new soapval('return', 'xsd:string', '1');
        }
        return new soapval('return', 'xsd:string', "0");

        return new soapval("return", "tns:stdMessage", array("code" => 0, "msg" => "called successfully"));
    }

    $server->wsdl->addComplexType("stdMessage",
                                  "complexType",
                                  "struct",
                                  "all",
                                  "",
                                  array("code" => array("name" => "code", "type" => "xsd:int"),
                                        "msg"  => array("name" => "msg", "type" => "xsd:string"))
                                 );

    // Valor que indica que statement se va a ejecutar (SELECT, INSERT, UPDATE, DELETE)
    // TODO: agregar restricciones al tipo
    $server->wsdl->addComplexType("tDbCode",
                                  "complexType",
                                  "struct",
                                  "all",
                                  "",
                                  array("code" => array("name" => "code", "type" => "xsd:int")));
                        
    $server->wsdl->addComplexType("tDbTables",
                                  "simpleType",
                                  "array",
                                  "sequence",
                                  "",
                                  array("table" => array("name" => "table", "type" => "xsd:string")
                                    )
                                );

    $server->wsdl->addComplexType("tDbColumns",
                                  "complexType",
                                  "struct",
                                  "all",
                                  "",
                                  array("column" => array("name" => "column", "type" => "xsd:string")));

    $server->wsdl->addComplexType("tDbConditionalOperators",
                                  "complexType",
                                  "struct",
                                  "all",
                                  "",
                                  array("operator" => array("name" => "operator", "type" => "xsd:string")));

    $server->wsdl->addComplexType("tDbConditionalValues",
                                  "complexType",
                                  "struct",
                                  "all",
                                  "",
                                  array("value" => array("name" => "value", "type" => "xsd:string")));

    // Tipo estandar para hacer cualquier clase de query
    // TODO: agragar los complextypes necesarios para completar esta definicion
    $server->wsdl->addcomplexType("dbStatement",
                                  "complexType",
                                  "struct",
                                  "all",
                                  "",
                                  array("instruction" => array("name" => "instruction", "type" => "tns:tDbCode"),
                                        "tables" => array("name" => "tables", "type" => "tns:tDbTables[]", "minOccurs" => 1, "maxOccurs" => "unbounded"),
                                        "columns" => array("name" => "columns", "type" => "tns:tDbColumns"),
                                        "conditionalColumns" => array("name" => "conditionalColumns", "type" => "tns:tDbColumns"),
                                        "conditionalOperators" => array("name" => "conditionalOperators", "type" => "tns:tDbConditionalOperators"),
                                        "conditionalValues" => array("name" => "conditionalValues", "type" => "tns:tDbConditionalValues")
                                    )
                                );

    if (!isset($HTTP_RAW_POST_DATA)) {
        $HTTP_RAW_POST_DATA = file_get_contents("php://input");
    }

    $server->service($HTTP_RAW_POST_DATA);

    /*


    alta baja productos
    registro
    login
    db
    mostrar productos

    */