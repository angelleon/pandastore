<?php
    namespace PandaStore\Types;
    require_once __DIR__."/../interfaces/Builder.php";
    
    use PandaStore\Interfaces\Builder;

    class InsertStatement implements Builder {
        public function column($column) {
            return $this;
        }

        public function build() {
            
        }
    }