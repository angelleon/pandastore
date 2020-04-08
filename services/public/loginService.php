<?php
require_once __DIR__."/../util.php";
require_once __DIR__."/../libs/servers/LoginServer.php";

//use PandaStore\Servers\LoginServer;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

$formatter = new LineFormatter(null, null, true, true);
$handler = new StreamHandler(__DIR__."/../logs/".basename(__FILE__).".log", Logger::DEBUG);
$handler->setFormatter($formatter);

$log = new Logger(basename(__FILE__));
$log->pushHandler($handler);

if (!isset($HTTP_RAW_POST_DATA)) {
    $HTTP_RAW_POST_DATA = file_get_contents('php://input');
}
$log->debug($HTTP_RAW_POST_DATA);

$SERVICE_URL = "$SERVICES_BASE_URL/".basename(__FILE__);
nusoap_base::setGlobalDebugLevel(9);
$dbService = new LoginServer($SERVICE_URL);
$dbService->service($HTTP_RAW_POST_DATA);
$log->debug($dbService->getDebug());


