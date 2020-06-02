<?php
    require_once __DIR__.'/../../libs/vendor/autoload.php';
    require_once __DIR__."/../../libs/Url.php";
    require_once __DIR__."/../../libs/view/View.php";
    require_once __DIR__."/../../libs/clients/SingupClient.php";
    require_once __DIR__."/../../libs/util/PasswdChecker.php";

    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;
    use Monolog\Formatter\LineFormatter;

    use PandaStore\Clients\SingupClient;
    use PandaStore\Util\PasswdChecker;
    use PandaStore\View;

    $formatter = new LineFormatter(null, null, true, true);
    $handler = new StreamHandler(__DIR__."/../../logs/".basename(__FILE__).".log", Logger::DEBUG);
    $handler->setFormatter($formatter);

    $log = new Logger(basename(__FILE__));
    $log->pushHandler($handler);

    $log->debug("starting debug");
    
    if ( isset($_POST['email'], $_POST["passwd"], $_POST["givenName"], $_POST["surname"])) {

        $email = $_POST['email'];
        $rawPasswd = $_POST["passwd"];
        $givenName = $_POST["givenName"];
        $surname = $_POST["surname"];

        $log->debug("post data: ".json_encode($_POST, JSON_PRETTY_PRINT));

        $strongPasswd = PasswdChecker::checkPasswd($rawPasswd);
        $log->debug(json_encode($strongPasswd, JSON_PRETTY_PRINT));
        if ($strongPasswd["code"] <= 0) {
            $code = $strongPasswd["code"];
            $decideValue = false;
            $msg = $strongPasswd["msg"];
            View\renderRedirectForm($decideValue, WEB_LOGIN_URL, WEB_SINGUP_URL, $code, $msg);
        }
        nusoap_base::setGlobalDebugLevel(9);
        $client = new SingupClient(SINGUP_SERVICE_URL);
        
        $response = $client->singup($_POST["email"], $_POST["passwd"], $_POST['givenName'], $_POST['surname']);
        $log->debug("response: ".json_encode($response, JSON_PRETTY_PRINT));
        $log->debug($client->getDebug());
    }

