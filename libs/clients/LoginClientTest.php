<?php
    namespace PandaStore\Clients;
    require_once __DIR__."/../Url.php";
    require_once __DIR__."/../util/Encrypter.php";
    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__."/LoginClient.php";

    use PHPUnit\Framework\TestCase;
    use PandaStore\Util\Encrypter;

    class LoginClientTest extends TestCase {
        /**
         * @test
         */
        function callMethodTest() {
            $client = new LoginClient(LOGIN_SERVICE_URL);
            $resp = $client->checkLogin("luianglenlop@gmail.com", Encrypter::encrypt("panda_store"));
            $this->assertIsArray($resp);
            $this->assertArrayHasKey("code", $resp);
            $this->assertArrayHasKey("msg", $resp);
            $this->assertEquals(0, $resp["code"]);
        }
    }