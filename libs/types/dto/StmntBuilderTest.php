<?php
    namespace PandaStore\Types;
    require_once __DIR__."/../../vendor/autoload.php";
    require_once __DIR__.'/StmntBuilder.php';

    use PHPUnit\Framework\TestCase;

    class StmntBuilderTest extends TestCase {
        public function testSelectStmnt() {
            $stmnt = StmntBuilder::select("idUsuario")
                        ->column("nombre")
                        ->from("Usuario")
                        ->where("email")->eq("luianglenlop@gmail.com")
                        ->and("passwd")->eq("pancha23")
                        ->build();
            $expected = [];
            $this->assertEquals($expected, $stmnt);
        }
    }