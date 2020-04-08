<?php
    require __DIR__. '/../vendor/autoload.php';
    include_once __DIR__."/../util.php";
    require_once __DIR__."/../libs/clients/SingupClient.php";
    require_once __DIR__."/../libs/util/PasswdChecker.php";

    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;
    use Monolog\Formatter\LineFormatter;

    $formatter = new LineFormatter(null, null, true, true);
    $handler = new StreamHandler(__DIR__."/../logs/".basename(__FILE__).".log", Logger::DEBUG);
    $handler->setFormatter($formatter);

    $log = new Logger(basename(__FILE__));
    $log->pushHandler($handler);

    $log->debug("starting debug");
    
    use PandaStore\Clients\SingupClient;
    use PandaStore\Util\PasswdChecker;
    
    if (isset($_POST['email']) && 
        isset($_POST["passwd"]) &&
        isset($_POST["givenName"])&&
        isset($_POST["surname"])) {

        $email = $_POST['email'];
        $rawPasswd = $_POST["passwd"];
        $givenName = $_POST["givenName"];
        $surname = $_POST["surname"];

        $log->debug("post data: ".json_encode($_POST, JSON_PRETTY_PRINT));

        $strongPasswd = PasswdChecker::checkPasswd($rawPasswd);
        $log->debug(json_encode($strongPasswd, JSON_PRETTY_PRINT));
        if ($strongPasswd["code"] > 0) {
            nusoap_base::setGlobalDebugLevel(9);
            $client = new SingupClient($SINGUP_SERVICE_URL);
            
            $response = $client->singup($_POST["email"], $_POST["passwd"], $_POST['givenName'], $_POST['surname']);
            $log->debug("response: ".json_encode($response, JSON_PRETTY_PRINT));
            $log->debug($client->getDebug());
        } else {
            $code = $strongPasswd["code"];
            $decideValue = $code > 0;
            $msg = $strongPasswd["msg"];
        }
    }

