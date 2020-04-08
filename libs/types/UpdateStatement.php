<?php
    namespace PandaStore\Types;
    require_once __DIR__."/../interfaces/Builder.php";

    use PandaStore\Interfaces\Builder;

    class UpdateStatement implements Builder {
        function __construct() {

        }

        public function table($tablename) {
            $this->tablename = $tablename;
        }

        public function column($column) {
            $this->columnList[] = $column;
        }

        public function build() {
        
        }
    }