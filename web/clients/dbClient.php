<?php
    require __DIR__."/../vendor/autoload.php";
    include_once __DIR__."/../util.php";

    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;
    use Monolog\Formatter\LineFormatter;

    $formatter = new LineFormatter(null, null, true, true);
    $handler = new StreamHandler(__DIR__."/../logs/".basename(__FILE__).".log", Logger::DEBUG);
    $handler->setFormatter($formatter);

    $log = new Logger(basename(__FILE__));
    $log->pushHandler($handler);

    $SERVICE_URL = "$SERVICES_URL/dbService.php";

    nusoap_base::setGlobalDebugLevel(9);

    $client = new nusoap_client("$SERVICE_URL?wsdl", "wsdl");
    $client->setDebugLevel(9);

    $result = $client->call("GET",
                            array("statement" => array("instruction" => array('code' => 0),
                                                       "tables" => array("table" => array("product", "users")),
                                                       "columns" => array("idUser", "dateModified"),
                                                       "conditionalColumns" => array("column" => "idUser"),
                                                       "conditionalOperators" => array("operator" => "="),
                                                       "conditionalValues" => array("value" => "1"))
                                ),
                            "uri:$SERVICE_URL"
                            );
    $dbg_info = $client->getDebug();
    $log->debug($client->wsdl->getDebug());
    $log->debug($dbg_info);
