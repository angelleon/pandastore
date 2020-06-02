<?php
require_once __DIR__."/../libs/Url.php";
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

$POST_DATA = file_get_contents('php://input');
$log->debug($POST_DATA);

nusoap_base::setGlobalDebugLevel(9);
$loginServer = new LoginServer(LOGIN_SERVICE_URL);
$loginServer->service($POST_DATA);
$log->debug($loginServer->getDebug());


