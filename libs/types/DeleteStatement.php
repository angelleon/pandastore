<?php
    namespace PandaStore\Types;
    require_once __DIR__."/../interfaces/Builder.php";

    use PandaStore\Interfaces\Builder;

    class DeleteStatement implements Builder {
        public function __construct()
        {
            
        }
        public function build() {

        }
        
        public function column($column) {
            $this->columnList[] = $column;
        }
    }