<?php
    //namespace PandaStore\Services;
    require_once __DIR__."/../util.php";
    require_once __DIR__."/../libs/servers/DbServer.php";
    require_once __DIR__."/../libs/servers/ProductManagerServer.php";

    //use PandaStore\Servers\SingupServer;
    use nusoap_base;


    if (!isset($HTTP_RAW_POST_DATA)) {
        $HTTP_RAW_POST_DATA = file_get_contents('php://input');
    }

    //$SERVICE_URL = "$SERVICES_BASE_URL/".basename(__FILE__);
    nusoap_base::setGlobalDebugLevel(9);
    $singupService = new ProductManagerServer($SINGUP_SERVICE_URL);
    $singupService->service($HTTP_RAW_POST_DATA);
    $log->debug($singupService->getDebug());
