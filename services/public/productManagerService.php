<?php
    //namespace PandaStore\Services;
    require_once __DIR__."/../libs/Url.php";
    require_once __DIR__."/../libs/servers/DbServer.php";
    require_once __DIR__."/../libs/servers/ProductManagerServer.php";

    //use PandaStore\Servers\SingupServer;
    use nusoap_base;

    $POST_DATA = file_get_contents('php://input');
    nusoap_base::setGlobalDebugLevel(9);
    $singupService = new ProductManagerServer(SINGUP_SERVICE_URL);
    $singupService->service($POST_DATA);
    $log->debug($singupService->getDebug());
