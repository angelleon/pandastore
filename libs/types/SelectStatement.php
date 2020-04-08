<?php
    namespace PandaStore\Types;
    require_once __DIR__."../interfaces/Builder.php";
    use PandaStore\Interfaces\Builder;

    class SelectStatement implements Builder {
        private const INITIAL_STATE = 0;
        function __construct() {
            $this->state = SelectStatement::INITIAL_STATE;
        }

        public function build() {

        }

        public function distinct() {
            $this->distinctModifier = true;
            return $this;
        }

        public function column($column) {
            $this->columns[] = $column;
            return $this;
        }

        public function table($table) {
            $this->tables[] = $table;
            return $this;
        }

        private function conditionalColumn($column) {
            $this->conditionalColumns[] = $column;
            return $this;
        }

        public function where($column) {
            return $this->conditionalColumn($column);
        }

        public function and($column) {
            return $this->conditionalColumn($column);
        }

        public function or($column) {
            return $this->conditionalColumn($column);
        }
    }