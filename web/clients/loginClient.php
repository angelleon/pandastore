<?php
    require __DIR__. '/../vendor/autoload.php';
    //print_r($_REQUEST);
    // echo __DIR__ . '/../vendor/autoload.php';

    include_once __DIR__."/../util.php";

    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;

    $log = new Logger(basename(__FILE__));
    $log->pushHandler(new StreamHandler(__DIR__."/../logs/".basename(__FILE__).".log", Logger::DEBUG));

    if (session_status() == PHP_SESSION_ACTIVE
        || session_status() == PHP_SESSION_DISABLED) {
        header("Location: $SERVER_BASE_URL");
        exit();
    }

    class Encrypter {
        function encrypt(string $s) {
            return hash("sha3-265", $s);
        }
    }

    //$SERVER_URL = "$SERVICES_URL/loginService.php";
    //$cliente = new nusoap_client("$SERVER_URL?wsdl", 'wsdl');
    //$result = $cliente->call(
    //    "checkLogin",
    //    array("email" => "luianglenlop@gmail.com",
    //          //"passwd" => hash("sha3-256", "pandastore")
    //          "passwd" => "pandastore"
    //        ),
    //    "uri:$SERVER_URL"
    //);
    //$debug_str = $cliente->getDebug();
    //echo 'debug info'.$debug_str;
    //print_r($result);
    //echo "Respuesta: $result";
    //exit();
    //echo "bfif";
    $_POST["email"] = "luianglenlop@gmail.com";
    $_POST["passwd"] = 'pandastore';
    if (session_status() == PHP_SESSION_NONE
        && isset($_POST['email']) 
        && isset($_POST['passwd'])) {
        
        $SERVER_URL = "$SERVICES_URL/loginService.php";
        nusoap_base::setGlobalDebugLevel(9);
        $cliente = new nusoap_client("$SERVER_URL?wsdl", 'wsdl');
        // $result = $cliente->call(
        //     "checkLogin",
        //     array("email" => "".$_POST['email'],
        //           "passwd" => "".Encrypter::encrypt($_POST['passwd'])),
        //     "uri:$SERVER_URL"
        // );
        $result = $cliente->call(
            "hw",
            array("name" => "panchita"),
            "uri:$SERVER_URL"
        );
        $log->debug($cliente->getDebug());
        $log->debug($cliente->response);
        $log->debug(json_encode($result));
        exit();
        if ($result) {
            //echo "bgif";
            session_start();
            header("Location: $SERVER_BASE_URL/store.php");
        } else {
            //echo "bgel"; 
            ?>
            <form name="redirectForm" action="<?php echo $SERVER_BASE_URL; ?>/login.php" method="POST">
                <input type="hidden" name="error" value="1">
            </form>
            <script type="text/javascript">
                document.redirectForm.submit();
            </script>
        <?php
        }
        //echo "eif";
    }
    //echo "aif";
    
    /*
    Soy un programador con complejo de motor de busqueda

    habilidades
    Uso avanzado de sistemas unix-like (linux, freebsd); administracion de servicios, permisos, usuarios y paquetes de software; backups
    Desarrollo para sistemas embebidos/iot (raspberry pi)
    Edicion de imagen raster (photoshop-like), imagen vectorial (illustrator-like)
    Desarrollo movil (android)
    Uso de CVS en el desarrollo de software (git, github)

    tecnologias
    lenguajes:
Python
Rust
SQL (PL/SQL, mysql)
Shell (bash)
C/C++
Javascript
Php
Java

    framework, librerias, herramientas:
Flask, sqlalchemy, JWT, rest (web, backend, python)
Laravel (web, backend, php)
Qt5 (desktop, GUI, C++, python)
SDL (desktop, GUI, C/C++, rust)
*/





