<?php
    require_once __DIR__."/../libs/vendor/autoload.php";
    require_once __DIR__."/../libs/Url.php";
    require_once __DIR__."/../libs/servers/DbServer.php";

    //use PandaStore\Servers\DbServer;

    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;
    use Monolog\Formatter\LineFormatter;

    $formatter = new LineFormatter(null, null, true, true);
    $handler = new StreamHandler(__DIR__."/../logs/".basename(__FILE__).".log", Logger::DEBUG);
    $handler->setFormatter($formatter);

    $log = new Logger(basename(__FILE__));
    $log->pushHandler($handler);

    $POST_DATA = file_get_contents('php://input');
    $log->debug("POST_DATA: " . $POST_DATA);    

    //nusoap_base::setGlobalDebugLevel(9);
    $dbService = new DbServer(DB_SERVICE_URL);
    $dbService->service($POST_DATA);
    //$log->debug($dbService->getDebug());

