<?php
    require_once __DIR__.'/BaseClient.php';
    require_once __DIR__.'/../vendor/autoload.php';
    require_once __DIR__.'/../Url.php';

    use PHPUnit\Framework\TestCase;
    use PandaStore\Clients\BaseClient;

    $TEST_SERVICE_URL = SERVICES_BASE_URL."/testService.php";

    class BaseClientTest extends TestCase {
        /**
         * @test
         */
        public function instanceTest() {
            global $TEST_SERVICE_URL;
            $instance = new BaseClient($TEST_SERVICE_URL);
            $this->assertNotNUll($instance);
        }
    }