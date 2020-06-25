<?php
    namespace PandaStore\Types\Dto;
    require_once __DIR__."/../../vendor/autoload.php";
    require_once __DIR__."/InsertStmnt.php";

    use PHPUnit\Framework\TestCase;

    class SelectStmntTest extends TestCase {
        /**
         * @test
         */
        function insertStmntTest() {
            $userId = 1;
            $stmnt = (new InsertStmnt("STORE_DB"))->column("ID_USER")
            ->into("KART")
            ->values($userId)
            ->build();
            $expected = ["insertStmnt" => [
                "columns" => ["column" => ["ID_USER"]],
                "table" => "KART",
                "values" => ["value" => [1]],
                "database" => "STORE_DB"
                ]];
            $this->assertNotNull($stmnt);
            $this->assertIsArray($stmnt);
            $this->assertEquals($expected, $stmnt);
        }
    }