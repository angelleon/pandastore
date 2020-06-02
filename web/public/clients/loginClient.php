<?php
    require __DIR__. '/../../libs/vendor/autoload.php';
    require_once __DIR__."/../../libs/Url.php";
    require_once __DIR__."/../../libs/view/View.php";
    require_once __DIR__."/../../libs/clients/LoginClient.php";

    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;
    use Monolog\Formatter\LineFormatter;

    use PandaStore\View;

    $formatter = new LineFormatter(null, null, true, true);
    $handler = new StreamHandler(__DIR__."/../../logs/".basename(__FILE__).".log", Logger::DEBUG);
    $handler->setFormatter($formatter);

    $log = new Logger(basename(__FILE__));
    $log->pushHandler($handler);
    
    use PandaStore\Clients\LoginClient;

    //$_POST["email"] = "panda_store";
    //$_POST["passwd"] = "pandastore";
    
    if (isset($_POST['email']) && isset($_POST["passwd"])) {
        nusoap_base::setGlobalDebugLevel(9);
        $client = new LoginClient(LOGIN_SERVICE_URL);
        $log->debug("");
        $response = $client->checkLogin($_POST["email"], $_POST["passwd"]);
        if (is_array($response) && array_key_exists("code", $response) && array_key_exists("msg", $response)) {
            $log->debug("Response: " . json_encode($response, JSON_PRETTY_PRINT));
            $code = $response["code"];
            $decideValue = $code == 0;
            $msg = $response["msg"];
        } else {
            $code = -500;
            $msg = "login service unavailable";
            $decideValue = false;
        }
        View\renderRedirectForm($decideValue, WEB_BASE_URL."/dashboard.php", WEB_BASE_URL."/login.php", $code, $msg);
    }

