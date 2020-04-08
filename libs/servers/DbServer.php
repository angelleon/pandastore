<?php
    //namespace PandaStore\Servers;
    require_once __DIR__."/../../vendor/autoload.php";
    require_once __DIR__."/../../util.php";
    require_once __DIR__."/BaseServer.php";

    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;
    use Monolog\Formatter\LineFormatter;
    use Illuminate\Database\Capsule\Manager as Capsule; 
    use Illuminate\Database\Capsule\Manager as DB; 
    //use Illuminate\Support\Facades\DB;
    use nusoap_server;
    use soapval;
    use PandaStore\Servers\BaseServer;

    // log init
    $formatter = new LineFormatter(null, null, true, true); // allows witting LF in log entries
    $handler = new StreamHandler(__DIR__."/../../logs/".basename(__FILE__).".log", Logger::DEBUG);
    $handler->setFormatter($formatter);
    $log = new Logger(basename(__FILE__));
    $log->pushHandler($handler);

    function DbSelect($statement) {
        if (is_null($statement)) {
            return ["code" => -1, "msg" => "statement can not be null"];
        }
        global $log;
        $log->debug("starting execution of DbInsert");
        $log->debug("array recived: ".json_encode($statement, JSON_PRETTY_PRINT));

        // DB conenction
        $DB_DRIVER = "mysql";
        $DB_HOST = 'localhost';
        $DB_PORT = 3306;
        $DB_USER = 'panda_store';
        $DB_PASSWD = 'panda_store';
        $DB_NAME = 'PandaStore';
        $db = new Capsule();
        $db->addConnection([
            'driver' => $DB_DRIVER,
            'host' => $DB_HOST,
            'port' => $DB_PORT,
            'database' => $DB_NAME,
            'username' => $DB_USER,
            'password' => $DB_PASSWD,
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ]);
        $db->setAsGlobal();
        $log->debug("Initializing database connection");
        $db->bootEloquent();
        $log->debug("Connected to database");

        // query construction
        $query = "SELECT ";

        if (is_null($statement["columns"]["column"])) {
            return ["code" => -1, "msg" => "You must specify at least one column to select"];
        }
        if (is_array($statement["columns"]["column"])) {
            $columns = $statement["columns"]["column"];
            if (count($columns) == 0) {
                return ["code" => -1, "msg" => "You must specify at least one column to select"];
            }
            for ($i = 0; $i < count($columns); $i++) {
                $query .= $columns[$i];
                if ($i < count($columns) - 1) {
                    $query .= ', ';
                }
            }
        } else {
            $query .= $statement["columns"]["column"];
        }

        $query .= " FROM ";

        if (is_null($statement["tables"]["table"])) {
            return ["code" => -1, "msg" => "You must specify at least one table to insert into"];
        }
        if (is_array($statement["tables"]["table"])) {
            $tables = $statement["tables"]["table"];
            if (count($tables) < 1) {
                return ["code" => -1, "msg" => "You must specify at least one table to insert into"];
            } else if (count($tables) > 1) {
                return ["code" => -1, "msg" => "You must specify at most one table to insert into"];
            }
            $query .= $tables[0];
        } else {
            $query .= $statement["tables"]["table"];
        }

        if (is_null($statement["conditionColumns"]["column"])) {
            $query .= " ";
        } else if (is_array($statement["conditionColumns"]["column"])) {
            
            $columns = $statement["conditionColumns"]["column"];
            
        } else {
            $columns = [$statement["conditionColumns"]["column"]];
        }

        if (is_null($statement["conditionOperators"]["operator"])) {
        } else if (is_array($statement["conditionOperators"]["operator"])) {
            $operators = $statement["conditionOperators"]["operator"];
        } else {
            $operators = [$statement["conditionOperators"]["operator"]];
        }
        if (is_null($statement["conditionBoolOperators"]["boolOperator"])) {
        } else if (is_array($statement["conditionBoolOperators"]["boolOperator"])) {
            $boolOperators = $statement["conditionBoolOperators"]["boolOperator"];
        } else {
            $boolOperators = [$statement["conditionBoolOperators"]["boolOperator"]];
        }
        if (is_null($statement["conditionValues"]["value"])) {

        } else if (is_array($statement["conditionValues"]["value"])) {
            $values = $statement["conditionValues"]["value"];
        } else {
            $values = [$statement["conditionValues"]["value"]];
        }

        if (count($operators) != count($columns)) {
            return ["code" => -1, "msg" => "You must specify enought relational operators for where condition"];
        } else if (count($columns) > 1 && count($boolOperators) + 1 != count($columns)) {
            return ["code" => -1, "msg" => "You must specify enought boolean operators for where condition"];
        } else if (count($values) != count($columns)) {
            return ["code" => -1, "msg" => "You must specify enought values to bind for where condition"];
        }

        $query .= " WHERE ";
        for ($i = 0; $i < count($columns); $i++) {
            $query .= $columns[$i] . " ";
            $query .= $operators[$i] . " ";
            $query .= "? ";
            if ($i < count($columns) - 1) {
                $query .= $boolOperators[$i] . " ";
            }
        }

        if (is_null($statement["limitCount"])) {

        } else {
            $query .= " LIMIT ";
            if (!is_null($statement["limitOffset"])) {
                $query .= $statement["limitOffset"] . ", ";
            }
            $query .= $statement["limitCount"];
        }


        
        // query execution
        $log->debug("query: " . $query);
        $log->debug("executing select");
        $status = DB::select($query, $values);
        //return ["code" => ($status ? 0 : 1), "msg" => "$status"];
        return ["code" => 0, "msg" => json_encode($status, JSON_PRETTY_PRINT)];
    }

    function DbInsert($statement) {
        global $log;
        $log->debug("starting execution of DbInsert");
        $log->debug("array recived: ".json_encode($statement, JSON_PRETTY_PRINT));

        // DB conenction
        $DB_DRIVER = "mysql";
        $DB_HOST = 'localhost';
        $DB_PORT = 3306;
        $DB_USER = 'panda_store';
        $DB_PASSWD = 'panda_store';
        $DB_NAME = 'PandaStore';
        $db = new Capsule();
        $db->addConnection([
            'driver' => $DB_DRIVER,
            'host' => $DB_HOST,
            'port' => $DB_PORT,
            'database' => $DB_NAME,
            'username' => $DB_USER,
            'password' => $DB_PASSWD,
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ]);
        $db->setAsGlobal();
        $db->bootEloquent();

        // query construction
        $query = "INSERT INTO ";
        if (is_null($statement["tables"]["table"])) {
            return ["code" => -1, "msg" => "You must specify at least one table to insert into"];
        }
        if (is_array($statement["tables"]["table"])) {
            $tables = $statement["tables"]["table"];
            if (count($tables) < 1) {
                return ["code" => -1, "msg" => "You must specify at least one table to insert into"];
            } else if (count($tables) > 1) {
                return ["code" => -1, "msg" => "You must specify at most one table to insert into"];
            }
            $query .= $tables[0];
        } else {
            $query .= $statement["tables"]["table"];
        }

        if (is_null($statement["columns"]["column"])) {
            $query .= " ";
        } else if (is_array($statement["columns"]["column"])) {
            $columns = $statement["columns"]["column"];
            $query .= "(";
            for ($i = 0; $i < count($columns); $i++) {
                $query .= $columns[$i];
                if ($i < count($columns) - 1) {
                    $query .= ", ";
                }
            }
            $query .= ") ";
        } else {
            $query .= "(" . $statement["columns"]["column"] . ") ";
        }

        $query .= "VALUES ( ";
        $insert_values = [];

        if (is_null($statement["conditionValues"]["value"])) {
            return ["code" => -1, "msg" => "You must specify at least one value to insert"];
        }
        if (is_array($statement["conditionValues"]["value"])) {
            $values = $statement["conditionValues"]["value"];
            if (count($values) == 0) {
                return ["code" => -1, "msg" => "You must specify at least one value to insert"];
            }
            //$query .= $statement["conditionValues"]["value"];
            for ($i = 0; $i < count($values); $i++) {
                $query .= "? ";
                $insert_values[] = $values[$i];
                if ($i < count($values) - 1) {
                    $query .= ", ";
                }
            }
        } else {
            $query .= "? ";
            $insert_values[] = $statement["conditionValues"]["value"];
        }
        $query .= ")";
        
        $status = DB::insert($query, $insert_values);
        $log->debug($query);
        return ["code" => ($status ? 0 : 1), "msg" => "$status"];
    }
    function DbUpdate($statement) {

    }
    function DbDelete() {

    }

    class DbServer extends BaseServer {
        private $dbServiceUrl;
        function __construct($dbServiceUrl) {
            parent::__construct($dbServiceUrl);
            //parent::setGlobalDebugLevel(9);
            //$this->setDebugLevel(9);
            $formatter = new LineFormatter(null, null, true, true);
            $handler = new StreamHandler(__DIR__."/../../logs/".basename(__FILE__).".log", Logger::DEBUG);
            $handler->setFormatter($formatter);
            $this->log = new Logger(basename(__FILE__));
            $this->log->pushHandler($handler);
            $this->dbServiceUrl = $dbServiceUrl;
            $this->configureWSDL("WSDLTST", $dbServiceUrl);
            $this->wsdl->schemaTargetNamespace = $dbServiceUrl;
            $this->wsdl->addComplexType("stdMessage",
                "complexType",
                "struct",
                "all", 
                "",
                ["code" => ["name" => "code", "type" => "xsd:int"], 
                "msg"  => ["name" => "msg", "type" => "xsd:string"]
                ]
            );
            $this->wsdl->addComplexType("tDbTables",
                "complexType",
                "array",
                "all",
                "",
                ["table" => ["name" => "table",
                    "type" => "xsd:string", "minOccurs" => 1,
                    "maxOccurs" => "unbounded"]
                ]
            );
            $this->wsdl->addComplexType("tDbColumns",
                "complexType",
                "struct",
                "all",
                "",
                ["column" => ["name" => "column", "type" => "xsd:string",
                    "minOccurs" => 1, "maxOccurs" => "unbounded"]
                ]
            );
            $this->wsdl->addComplexType("tDbConditionOperators",
                "complexType",
                "struct",
                "all",
                "",
                ["operator" => ["name" => "operator", "type" => "xsd:string", 
                    "minOccurs" => 0, "maxOccurs" => "unbounded"]
                ]
            );
            $this->wsdl->addComplexType("tDbValues",
                "complexType",
                "struct",
                "all",
                "",
                ["value" => ["name" => "value", "type" => "xsd:string", 
                    "minOccurs" => 0, "maxOccurs" => "unbounded"]
                ]
            );
            $this->wsdl->addComplexType("tDbConditionBoolOperators",
                "complexType",
                "struct",
                "all",
                "",
                ["boolOperator" => ["name" => "boolOperator", "type" => "xsd:string", 
                    "minOccurs" => 0, "maxOccurs" => "unbounded"]
                ]
            );  
            /*
            $this->wsdl->addcomplexType("dbStatement",
                "complexType",
                "struct",
                "all",
                "",
                ["columns" => ["name" => "columns",
                        "type" => "tns:tDbColumns"],
                    "tables" => ["name" => "tables", 
                        "type" => "tns:tDbTables"],
                    "conditionColumns" => ["name" => "conditionColumns", 
                        "type" => "tns:tDbColumns"],
                    "conditionOperators" => ["name" => "conditionOperators",
                         "type" => "tns:tDbConditionOperators"],
                    "conditionValues" => ["name" => "conditionValues",
                        "type" => "tns:tDbValues"],
                    "conditionBoolOperators" => ["name" => "conditonBoolOperators",
                        "type" => "tns:tDbConditionBoolOperators"],
                    "limitCount" => ["name" => "limitCount", "type" => "xsd:int"],
                    "limitOffset" => ["name" => "limitOffset", "type" => "xsd:int"]
                ]
            );
            */
            $this->wsdl->addComplexType("dbSelectStmnt",
                "complexType",
                "struct",
                "all",
                "",
                ["distinct" => ["name" => "distinct", "type" => "xsd:boolean"],
                    "columns" => ["name" => "columns", "type" => "tns:tDbColumns"],
                    "tables" => ["name" => "tables", "type" => "tns:tDbTables"],
                    "conditionColumns" => ["name" => "conditionColumns", "type" => "tns:tDbColumns"],
                    "conditionOperators" => ["name" => "conditionOperators", "type" => "tns:tDbConditionOperators"],
                    "conditionValues" => ["name" => "conditionValues", "type" => "tns:tDbValues"],
                    "conditionBoolOperators" => ["name" => "conditionBoolOperators", "tns:conditionBoolOperators"],
                    "limit" => ["name" => "limit", "type" => "xsd:int"],
                    "offset" => ["name" => "offset", "type" => "xsd:int"]]);
            $this->wsdl->addComplexType("dbInsertStmnt",
                "complexType",
                "struct",
                "all",
                "",
                ["table" => ["name" => "table", "type" => "xsd:string"],
                 "columns" => ["name" => "columns", "type" => "tns:tDbColumns"],
                 "values" => ["name" => "values", "type" => "tns:tDbConditionValues"]]);
            $this->wsdl->addComplexType("dbUpdateStmnt",
                "complexType",
                "struct",
                "all",
                "",
                ["table" => ["name" => "table", "type" => "xsd:string"],
                 "columns" => ["name" => "columns", "type" => "tns:tDbColumns"],
                 "values" => ["name" => "values", "types" => "tns:tDbValues"],
                 "conditionColumns" => ["name" => "conditionColumns", "type" => "tns:tDbColumns"],
                 "conditionOperators" => ["name" => "conditionOperators", "type" => "tns:tDbConditionOperators"],
                 "conditionValues" => ["name" => "conditionValues", "type" => "tns:tDbValues"],
                 "conditionBoolOperators" => ["name" => "conditionBoolOperators", "type" => "tns:tdbConditionBoolOperators"]]);
            $this->wsdl->addComplexType("dbDeleteStmnt",
                "complexType",
                "struct",
                "all",
                "",
                ["table" => ["name" => "table", "type" => "xsd:string"],
                "conditionColumns" => ["name" => "conditionColumns", "type" => "tns:tDbColumns"],
                "conditionOperators" => ["name" => "conditionOperators", "type" => "tns:tDbConditionOperators"],
                "conditionValues" => ["name" => "conditionValues", "type" => "tns:tDbValues"],
                "conditionBoolOperators" => ["name" => "conditionBoolOperators", "type" => "tns:tdbConditionBoolOperators"]]);

            $this->register('DbSelect',
                ["statement" => "tns:dbSelectStmnt"],
                ["return" => "tns:stdMessage"],
                $dbServiceUrl
            );
            $this->register("DbInsert",
                ["statement" => "tns:dbInsertStmnt"],
                ["return" => "tns:stdMessage"],
                $dbServiceUrl
            );
            $this->register("DbUpdate",
                ["statement" => "tns:dbUpdateStmnt"],
                ["return" => "tns:stdMessage"],
                $dbServiceUrl
            );
            $this->register("DbDelete",
                ["statement" => "tns:dbDeleteStmnt"],
                ["return" => "tns:stdMessage"],
                $dbServiceUrl
            );

            $this->log->debug("starting server debug -----------------------------------------------------");
            $this->log->debug($this->getDebug());
            $this->log->debug($this->wsdl->getDebug());
        }
        
        public function service($http_raw_post_data) {
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