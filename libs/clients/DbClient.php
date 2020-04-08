<?php
    namespace PandaStore\Clients;

    require_once __DIR__."/../../vendor/autoload.php";
    require_once __DIR__."/../../url.php";
    require_once __DIR__."/BaseClient.php";



    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;
    use Monolog\Formatter\LineFormatter;

    $SERVICE_URL = "$SERVICES_BASE_URL/dbService.php";

    class DbClient extends BaseClient {

        function __construct($dbServiceUrl) {
            parent::__construct($dbServiceUrl);
            $formatter = new LineFormatter(null, null, true, true);
            $handler = new StreamHandler(__DIR__."/../../logs/".basename(__FILE__).".log", Logger::DEBUG);
            $handler->setFormatter($formatter);
            $this->log = new Logger(basename(__FILE__));
            $this->log->pushHandler($handler);
        }

        public function select($statement) {
            return  $this->call("DbSelect", $statement, "uri:$this->pandaServiceUrl");
        }

        public function insert($statement) {
            $this->log->debug("Requesting Insert operation ----------------------------------------------------------------------------");
            $this->log->debug("Service url $this->pandaServiceUrl");
            return  $this->call("DbInsert", $statement, "uri:$this->pandaServiceUrl");
        }

        public function update($statement) {
            return  $this->call("DbUpdate", $statement, "uri:$this->pandaServiceUrl");
        }

        public function delete($statement) {
            return  $this->call("DbDelete", $statement, "uri:$this->pandaServiceUrl");
        }
    }
