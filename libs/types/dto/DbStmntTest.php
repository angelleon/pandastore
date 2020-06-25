<?php
    namespace PandaStore\Types\Dto;
    require __DIR__."/../../vendor/autoload.php";
    require __DIR__."/DbStmnt.php";

    use PHPUnit\Framework\TestCase;

    class DbStmntTest extends TestCase {
        /**
         * @test
         */
        public function testBuildNotImplemented() {
            $this->expectException(NotImplementedException::class);
            $stmnt = (new DbStmnt())->build();
        }

    }