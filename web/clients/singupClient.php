<?php
    require __DIR__ . '/../vendor/autoload.php';

    include_once __DIR__.'/../util.php';

    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;
    $log = new Logger(basename(__FILE__));

    if (!isset($HTTP_RAW_POST_DATA)) {
        $http_RAW_POST_DATA = file_get_contents("php://input");
    }

    $log->debug($HTTP_RAW_POST_DATA);
    

    $SERVICE_URL = "$SERVICES_URL/singupService.php";

    nusoap_base::setGlobalDebugLevel(9);

    $has_errors = !isset($_POST["givenName"]) 
                  && !isset($_POST["surename"])
                  && !isset($_POST["email"])
                  && !isset($_POST["passwd"]);



    if (session_status() == PHP_SESSION_NONE && !$has_errors) {
        $cleint = new nusoap_client("$SERVICE_URL?wsdl", "wsdl");

        $result = $cleint->call("singup",
                                array("givenName" => $_POST["givenName"],
                                      "surename" => $_POST["surename"],
                                      "email" => $_POST["email"],
                                      "passwd" => $_POST["passwd"]),
                                "uri:$SERVICE_URL");

    } else if (session_status() == PHP_SESSION_ACTIVE) {
        header("Location: $SERVER_URL/dashboard.php");
    } else if ($has_errors) {

    }