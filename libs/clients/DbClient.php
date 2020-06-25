<?php
    namespace PandaStore\Clients;

    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__."/../Url.php";
    require_once __DIR__."/BaseClient.php";

    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;
    use Monolog\Formatter\LineFormatter;

    $formatter = new LineFormatter(null, null, true, true);
    $handler = new StreamHandler(__DIR__."/../../logs/".basename(__FILE__).".log", Logger::DEBUG);
    $handler->setFormatter($formatter);

    $log = new Logger(basename(__FILE__));
    $log->pushHandler($handler);


    class DbClient extends BaseClient {
        function __construct($dbServiceUrl) {
            parent::__construct($dbServiceUrl);
            $formatter = new LineFormatter(null, null, true, true);
            $handler = new StreamHandler(__DIR__."/../../logs/".basename(__FILE__).".log", Logger::DEBUG);
            $handler->setFormatter($formatter);
            $this->log = new Logger(basename(__FILE__));
            $this->log->pushHandler($handler);
        }

        public function select($selectStmnt) {
            global $log;
            if(is_null($selectStmnt)) {
                $log->critical("method called with null argument");
                return false;
            } else {
                $log->debug("Method called with not null argument");
            }
            //$log->debug("selectStmnt: " . json_encode($selectStmnt, JSON_PRETTY_PRINT));
            $ret = $this->call("DbSelect", $selectStmnt, "uri:$this->pandaServiceUrl");
            if (!is_array($ret)) {
                return ["code" => -1, "msg" => "DbService returned unexpected value when calling select"];
            }
            $log->debug("db client debug info: " . $this->getDebug());
            $log->debug("operation called with request: " . $this->request);
            if ($ret["code"]  != 0) {
                return $ret;
            }
            $ret["msg"] = json_decode($ret["msg"]);
            return $ret;
        }

        public function insert($statement) {
            $this->log->debug("Requesting Insert operation ----------------------------------------------------------------------------");
            $this->log->debug("Service url $this->pandaServiceUrl");
            $result = $this->call("DbInsert", $statement, "uri:$this->pandaServiceUrl");
            if (!is_array($result)) {
                return ["code" => -1, "msg" => "DbService returned unexpected value when calling insert"];
            }
            $this->log->debug("Operation exceuted with result: " . json_encode($result, JSON_PRETTY_PRINT));
            return $result;
        }

        public function update($statement) {
            $this->log->debug("Requesting Insert operation ----------------------------------------------------------------------------");
            $this->log->debug("Service url $this->pandaServiceUrl");
            $this->log->debug("Calling DbUpdate method with arguments: " . json_encode($statement, JSON_PRETTY_PRINT));
            $result = $this->call("DbUpdate", $statement, "uri:$this->pandaServiceUrl");
            if (!is_array($result)) {
                $msg = "DbService returned unexpected value when calling update: ".json_encode($result, JSON_PRETTY_PRINT);
                $this->log->error($msg);
                return ["code" => -1, "msg" => $msg];
            }
            $this->log->debug("Operation exceuted with result: " . json_encode($result, JSON_PRETTY_PRINT));
            return $result;
        }

        public function delete($statement) {
            $this->log->debug("Requesting Insert operation ----------------------------------------------------------------------------");
            $this->log->debug("Service url $this->pandaServiceUrl");
            $result = $this->call("DbDelete", $statement, "uri:$this->pandaServiceUrl");
            if (!is_array($result)) {
                $msg = "DbService returned unexpected value when calling delete: ".json_encode($result, JSON_PRETTY_PRINT);
                $this->log->error($msg);
                return ["code" => -1, "msg" => $msg];
            }
            $this->log->debug("Operation exceuted with result: " . json_encode($result, JSON_PRETTY_PRINT));
            return $result;
            
        }
    }
