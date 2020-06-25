<?php
//namespace PandaStore\Servers;
require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../Url.php";
require_once __DIR__ . "/BaseServer.php";
require_once __DIR__ . "/../types/sql/SqlBuilder.php";

//use PDOException;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use Illuminate\Database\Capsule\Manager as Capsule;
//use Illuminate\Support\Facades\DB;
//use Illuminate\Database\Capsule\Manager as DB;

use PandaStore\Servers\BaseServer;
use Pandastore\Types\Sql\SqlBuilder;

// log init
$formatter = new LineFormatter("[%channel%][%level_name%] : %message% %context%\n", null, true, true); // allows witting LF in log entries
$handler = new StreamHandler(__DIR__ . "/../../logs/" . basename(__FILE__) . ".log", Logger::DEBUG);
$handler->setFormatter($formatter);
$log = new Logger(basename(__FILE__));
$log->pushHandler($handler);
$log->debug("Starting DbServer");

const DB_DRIVER = "mysql";
const DB_HOST = '25.87.105.20';
//const DB_HOST = 'localhost';
const DB_PORT = 3306;
const DB_USER = 'panda_store';
const DB_PASSWD = 'panda_store';
const DB_NAME = 'PandaStore';

const DB_CONN_PARAMS = [
    'driver' => DB_DRIVER,
    'host' => DB_HOST,
    'port' => DB_PORT,
    'database' => DB_NAME,
    'username' => DB_USER,
    'password' => DB_PASSWD,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
];

function getConnParams($stmnt, &$log) {
    $connParams = DB_CONN_PARAMS;

    if (array_key_exists("database", $stmnt)) {
        $log->debug("database argument exists in request");
        $log->debug(json_encode($stmnt["database"], JSON_PRETTY_PRINT));
        if (!is_null($stmnt["database"])
            && strcmp($stmnt["database"], "") != 0) {
            $log->debug("Setting up database connection parameter");
            $connParams["database"] = $stmnt["database"];
        }
    }
    return $connParams;
}

function openDB($params, &$db)
{
    global $log;
    // DB conenction
    $log->debug("Initializing database connection");
    $db = new Capsule();
    $log->debug("Adding connection to eloquent with parameters: " . json_encode($params, JSON_PRETTY_PRINT));
    $db->addConnection($params);
    $log->debug("Setting connection as global");
    $db->setAsGlobal();
    $log->debug("Booting eloquent");
    $db->bootEloquent();
    $log->debug("Connected to database");
}

function DbSelect($selectStmnt)
{
    if (is_null($selectStmnt)) {
        return ["code" => -1, "msg" => "statement can not be null"];
    }
    global $log;

    $log->debug("starting execution of DbSelect");
    $log->debug("array recived: " . json_encode($selectStmnt, JSON_PRETTY_PRINT));

    $query = "";
    $values = [];
    SqlBuilder::select($selectStmnt, $query, $values);

    // query execution
    $log->debug("query: " . $query);
    $log->debug("values" . json_encode($values, JSON_PRETTY_PRINT));

    $connParams = getConnParams($selectStmnt, $log);
    $db = null;
    openDB($connParams, $db);
    if (is_null($db)) {
        return ["code" => -1, "msg" => "Can not connect to database instance"];
    }
    $log->debug("executing select");
    try {
        $queryResult = $db->getConnection("default")->select($query, $values);
        $code = 0;
    } catch (PDOException $ex) {
        $code = -1;
        $queryResult = $ex->getMessage();
        $log->error("Sql select executed with error: " . $queryResult);
    }
    $log->debug("select executed with result: " . json_encode($queryResult, JSON_PRETTY_PRINT));
    return ["code" => $code, "msg" => json_encode($queryResult)];
}

function DbInsert($insertStmnt)
{
    global $log;
    $log->debug("starting execution of DbInsert");
    $log->debug("array recived: " . json_encode($insertStmnt, JSON_PRETTY_PRINT));

    $query = "";
    $values = [];

    $connParams = getConnParams($insertStmnt, $log);
    
    $db = null;
    openDB($connParams, $db);
    if (is_null($db)) {
        return ["code" => -1, "msg" => "Can not connect to database instance"];
    }
    $log->debug("executing insert");
    
    SqlBuilder::insert($insertStmnt, $query, $values);
    $log->debug("query: " . $query);
    $log->debug("values" . json_encode($values, JSON_PRETTY_PRINT));
    $log->debug("Executing query");
    try {
        $queryResult = $db->getConnection("default")->select($query, $values);
        $code = 0;
    } catch (PDOException $ex) {
        $log->error($ex->getMessage());
        $code = -1;
        $queryResult = "Error executing insert statement";
    }
    $log->debug("insert executed with result: " . json_encode($queryResult, JSON_PRETTY_PRINT));
    return ["code" => $code, "msg" => json_encode($queryResult)];
}

function DbUpdate($updateStmnt)
{
    //return ["code" => -1, "msg" => true];
    global $log;
    $log->debug("starting execution of DbInsert");
    $log->debug("array recived: " . json_encode($updateStmnt, JSON_PRETTY_PRINT));

    $query = "";
    $values = [];

    $connParams = getConnParams($updateStmnt, $log);
    
    $db = null;
    openDB($connParams, $db);
    if (is_null($db)) {
        return ["code" => -1, "msg" => "Can not connect to database instance"];
    }
    $log->debug("executing insert");
    
    SqlBuilder::update($updateStmnt, $query, $values);
    $log->debug("query: " . $query);
    $log->debug("values" . json_encode($values, JSON_PRETTY_PRINT));
    $log->debug("Executing query");
    try {
        $queryResult = $db->getConnection("default")->select($query, $values);
        $code = 0;
    } catch (PDOException $ex) {
        $log->error($ex->getMessage());
        $code = -1;
        $queryResult = "Error executing update statement";
    }
    $log->debug("insert executed with result: " . json_encode($queryResult, JSON_PRETTY_PRINT));
    return ["code" => $code, "msg" => json_encode($queryResult)];
}

function DbDelete($deleteStmnt)
{
    global $log;
    if (is_null($deleteStmnt)) {
        $msg = "Method called with null arguments";
        $log->error($msg);
        return ["code" => -1, "msg" => $msg];
    }
    $log->debug("starting execution of DbInsert");
    $log->debug("array recived: " . json_encode($deleteStmnt, JSON_PRETTY_PRINT));

    $query = "";
    $values = [];

    $connParams = getConnParams($deleteStmnt, $log);
    
    $db = null;
    openDB($connParams, $db);
    if (is_null($db)) {
        return ["code" => -1, "msg" => "Can not connect to database instance"];
    }
    $log->debug("executing insert");
    
    SqlBuilder::delete($deleteStmnt, $query, $values);
    $log->debug("query: " . $query);
    $log->debug("values" . json_encode($values, JSON_PRETTY_PRINT));
    $log->debug("Executing query");
    try {
        $queryResult = $db->getConnection("default")->select($query, $values);
        $code = 0;
    } catch (PDOException $ex) {
        $log->error($ex->getMessage());
        $code = -1;
        $queryResult = "Error executing delete statement";
    }
    $log->debug("insert executed with result: " . json_encode($queryResult, JSON_PRETTY_PRINT));
    return ["code" => $code, "msg" => json_encode($queryResult)];
}

class DbServer extends BaseServer
{
    private $dbServiceUrl;
    function __construct($dbServiceUrl)
    {
        parent::__construct($dbServiceUrl);
        //parent::setGlobalDebugLevel(9);
        //$this->setDebugLevel(9);
        $formatter = new LineFormatter("[%channel%][%level_name%] : %message% %context%\n", null, true, true);
        $handler = new StreamHandler(__DIR__ . "/../../logs/" . basename(__FILE__) . ".log", Logger::DEBUG);
        $handler->setFormatter($formatter);
        $this->log = new Logger(basename(__FILE__));
        $this->log->pushHandler($handler);
        $this->dbServiceUrl = $dbServiceUrl;
        $this->configureWSDL("WSDLTST", $dbServiceUrl);
        $this->wsdl->schemaTargetNamespace = $dbServiceUrl;
        $this->wsdl->addComplexType(
            "stdMessage",
            "complexType",
            "struct",
            "all",
            "",
            [
                "code" => ["name" => "code", "type" => "xsd:int"],
                "msg"  => ["name" => "msg", "type" => "xsd:string"]
            ]
        );
        $this->wsdl->addComplexType(
            "tDbTables",
            "complexType",
            "array",
            "all",
            "",
            ["table" => [
                "name" => "table",
                "type" => "xsd:string", "minOccurs" => 1,
                "maxOccurs" => "unbounded"
            ]]
        );
        $this->wsdl->addComplexType(
            "tDbColumns",
            "complexType",
            "struct",
            "all",
            "",
            ["column" => [
                "name" => "column", "type" => "xsd:string",
                "minOccurs" => 1, "maxOccurs" => "unbounded"
            ]]
        );
        $this->wsdl->addComplexType(
            "tDbConditionOperators",
            "complexType",
            "struct",
            "all",
            "",
            ["operator" => [
                "name" => "operator", "type" => "xsd:string",
                "minOccurs" => 0, "maxOccurs" => "unbounded"
            ]]
        );
        $this->wsdl->addComplexType(
            "tDbValues",
            "complexType",
            "struct",
            "all",
            "",
            ["value" => [
                "name" => "value", "type" => "xsd:string",
                "minOccurs" => 0, "maxOccurs" => "unbounded"
            ]]
        );
        $this->wsdl->addComplexType(
            "tDbConditionBoolOperators",
            "complexType",
            "struct",
            "all",
            "",
            ["boolOperator" => [
                "name" => "boolOperator", "type" => "xsd:string",
                "minOccurs" => 0, "maxOccurs" => "unbounded"
            ]]
        );

        $this->wsdl->addComplexType(
            "dbSelectStmnt",
            "complexType",
            "struct",
            "all",
            "",
            [
                "distinct" => ["name" => "distinct", "type" => "xsd:boolean"],
                "columns" => ["name" => "columns", "type" => "tns:tDbColumns"],
                "tables" => ["name" => "tables", "type" => "tns:tDbTables"],
                "conditionColumns" => ["name" => "conditionColumns", "type" => "tns:tDbColumns"],
                "conditionOperators" => ["name" => "conditionOperators", "type" => "tns:tDbConditionOperators"],
                "conditionValues" => ["name" => "conditionValues", "type" => "tns:tDbValues"],
                "conditionBoolOperators" => ["name" => "conditionBoolOperators", "type" => "tns:tDbConditionBoolOperators"],
                "limit" => ["name" => "limit", "type" => "xsd:int"],
                "offset" => ["name" => "offset", "type" => "xsd:int"],
                "database" => ["name" => "database", "type" => "xsd:string"]
            ]
        );
        $this->wsdl->addComplexType(
            "dbInsertStmnt",
            "complexType",
            "struct",
            "all",
            "",
            [
                "columns" => ["name" => "columns", "type" => "tns:tDbColumns"],
                "tables" => ["name" => "tables", "type" => "tns:tDbTables"],
                "values" => ["name" => "values", "type" => "tns:tDbValues"],
                "database" => ["name" => "database", "type" => "xsd:string"]
            ]
        );
        $this->wsdl->addComplexType(
            "dbUpdateStmnt",
            "complexType",
            "struct",
            "all",
            "",
            [
                "columns" => ["name" => "columns", "type" => "tns:tDbColumns"],
                "tables" => ["name" => "tables", "type" => "tns:tDbTables"],
                "values" => ["name" => "values", "type" => "tns:tDbValues"],
                "conditionColumns" => ["name" => "conditionColumns", "type" => "tns:tDbColumns"],
                "conditionOperators" => ["name" => "conditionOperators", "type" => "tns:tDbConditionOperators"],
                "conditionValues" => ["name" => "conditionValues", "type" => "tns:tDbValues"],
                "conditionBoolOperators" => ["name" => "conditionBoolOperators", "type" => "tns:tDbConditionBoolOperators"],
                "database" => ["name" => "database", "type" => "xsd:string"]
            ]
        );
        $this->wsdl->addComplexType(
            "dbDeleteStmnt",
            "complexType",
            "struct",
            "all",
            "",
            [
                "tables" => ["name" => "tables", "type" => "tns:tDbTables"],
                "conditionColumns" => ["name" => "conditionColumns", "type" => "tns:tDbColumns"],
                "conditionOperators" => ["name" => "conditionOperators", "type" => "tns:tDbConditionOperators"],
                "conditionValues" => ["name" => "conditionValues", "type" => "tns:tDbValues"],
                "conditionBoolOperators" => ["name" => "conditionBoolOperators", "type" => "tns:tDbConditionBoolOperators"],
                "database" => ["name" => "database", "type" => "xsd:string"]
            ]
        );

        $this->register(
            'DbSelect',
            ["selectStmnt" => "tns:dbSelectStmnt"],
            ["return" => "tns:stdMessage"],
            $dbServiceUrl
        );
        $this->register(
            "DbInsert",
            ["insertStmnt" => "tns:dbInsertStmnt"],
            ["return" => "tns:stdMessage"],
            $dbServiceUrl
        );
        $this->register(
            "DbUpdate",
            ["updateStmnt" => "tns:dbUpdateStmnt"],
            ["return" => "tns:stdMessage"],
            $dbServiceUrl
        );
        $this->register(
            "DbDelete",
            ["deleteStmnt" => "tns:dbDeleteStmnt"],
            ["return" => "tns:stdMessage"],
            $dbServiceUrl
        );

        $this->log->debug("starting server debug -----------------------------------------------------");
        $this->log->debug($this->getDebug());
        $this->log->debug($this->wsdl->getDebug());
    }

    public function service($http_raw_post_data)
    {
        $this->log->debug('Serving request ----------------------------------------------------------------------');
        $this->log->debug($http_raw_post_data);
        $this->log->debug("gotten response from parent class");
        parent::service($http_raw_post_data);
    }
}

    //$dbServer = new DbServer("http://localhost/panda-store/services/dbService.php");

    /*


    alta baja productos
    registro
    login
    db
    mostrar productos

    */
    /*
    esb
    es un
    que gestiona la cominicacion entre multi'les servicios web principalmente
    se enefoca en resolver el problema que surge cuando los servicions web dentro de una organizacion
    se incrementan exponencialmente debido al uso de n cantidad de aplicaciones y por lo que se
    necesitaria desarrollar conectores que permitan la comunicacion entre las mismas

    ventajas
    trabaja sobre cualquier protocolo y se encarga de traducir de un lenguaje a otro las diferentes peticiones
    al usar los esb los servicios no interactuan directamente
    sino que la comunicacion es a traves del conector de tal maera que el bus proporcionaria la virtualizacion de los servicios
    el esb identifica y establece rutas de los mensajes entre los diferentes servicios
    los protocolos usualmente utilizados son aquellos de la capa de transporte 
    y los estilos de interaccion etc, http , ftp, smtp

    herramientas para esb dentro del mercado
    open esb java
    oracle esb
    oracle service bus
    apalogic service bus
    microsoft biz talk server
    windows azure service bus
    ibm web sphere
    ibm web integration bus
    jboss fuse
    spring integration 
    phoenix service bus implementation C#
    apache service mix
    wso2 esb
    

    wso2 esb
    */