<?php
    require __DIR__ . '/../vendor/autoload.php';

    include_once __DIR__.'/../util.php';

    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;
    $log = new Logger(basename(__FILE__));
    $log->pushHandler(new StreamHandler(__DIR__."/../logs/".basename(__FILE__).".log", Logger::DEBUG));

    $SERVICE_URL = "$SERVICES_URL/".basename(__FILE__);

    nusoap_base::setGlobalDebugLevel(9);
    // echo "service_name $SERVICE_URL";
    $server = new soap_server();
    //$server->setDebugLevel(9);
    $server->configureWSDL('WSDLTST', $SERVICE_URL);
    $server->wsdl->schemaTargetNamespace = $SERVICE_URL;

    // servicios
    //$server->register('checkPasswd',
    //                  array('passwd' => 'xsd:string'),
    //                  array('return' => 'xsd:string'), 
    //                  $SERVICE_URL
    //                 );
//
    //function checkPasswd($passwd) {
    //    
    //}

    $server->register('checkLogin',
                      array('email' => 'xsd:string',
                            'passwd' => 'xsd:string'),
                      array('return' => 'xsd:string'),
                      $SERVICE_URL
    );

    $server->wsdl->addComplexType("stdMessage",
                                  "complexType",
                                  "struct",
                                  "all",
                                  "",
                                  array("code" => array("name" => "code", "type" => "xsd:int"),
                                        "msg"  => array("name" => "msg", "type" => "xsd:string"))
                                 );

    $server->register("hw",
                      array("name" => "xsd:string"),
                      array("return" => "tns:stdMessage"),
                      $SERVICE_URL
                     );

    function hw($name) {
        global $log;
        global $server;
        $val = new soapval("return", "tns:stdMessage", array("code" => 0, "msg" => "".$name." says hello world"), "tns");
        $log->debug($val->getDebug());
        $log->debug($server->getDebug());
        return $val;
    }

    function checkLogin($username, $passwd) {
        //return new soapval("return", "xsd:string", false);
        
    }

    if (!isset($HTTP_RAW_POST_DATA)) {
        $HTTP_RAW_POST_DATA = file_get_contents('php://input');
    }

    $server->service($HTTP_RAW_POST_DATA);
    //echo hw("panchita");

    // $passwd = 'pandastore';
    // $passwd = hash('sha3-256', $passwd);
    // print_r(checkLogin('luianglenlop@gmail.com', $passwd)->serialize());
    // echo "hash: ".$passwd;
    // echo $SERVICE_URL;

/*
    normas iso
    calidad, organizaciones
    orientadas a creacion de bienes o serv
    iso internacional, suiza,
    12207 
        procesos de ciclo de vida de softw
        proporciona estructura comun
        primarios
        soporte
        organizacionales
    9126
        funccionalidad
        fiabilidad
        usabilidad
        eficiencia
        mantenibilidad
        portabilidad
    14598
        evaluacion de productos de softw
        vison general
        planif y gest
        procesos desarrollo
    25000
        gestion
        modelo
        medicion
        requisitos
        evaluacion
    29110
        pymes
        proyectos de softw
        vision general
        marco de trabajo taxonomia
        guia de evaluacion
        especs perfil
        guia admon ing

        planificacion
        evaluacion del plan
        eval y control del proy
        cierre del proy */

        ?>
