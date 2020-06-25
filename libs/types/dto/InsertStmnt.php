<?php
    namespace PandaStore\Types\Dto;
    require_once __DIR__."/DbStmnt.php";

    class InsertStmnt extends DbStmnt {
        const COLUMN_LIST   =   0b1;
        const TABLE         =  0b10;
        const INSERT_VALUES = 0b100;

        public function __construct($database=null) {
            $this->state = InsertStmnt::COLUMN_LIST;
            $this->columns = [];
            $this->tables = [];
            $this->insert_values = [];
            $this->database = is_null($database) ? "" : $database;
        }

        public function build() {
            return ["insertStmnt" => [
                "columns" => ['column' => $this->columns],
                "tables" => ["table" => $this->tables],
                "values" => ["value" => $this->insert_values],
                "database" => $this->database
            ]];
        }

        public function column($column) {
            $this->checkState(self::COLUMN_LIST);
            $this->columns[] = $column;
            return $this;
        }

        public function into($table) {
            $this->checkState(self::TABLE);
            if (count($this->tables)) {
                throw new StmntInvalidSyntax("Can not insert in more than one table");
            }
            if (is_null($table)) {
                throw new StmntInvalidSyntax("Null value for table argument");
            }
            $this->tables[] = $table;
            return $this;
        }

        public function values($value) {
            $this->checkState(self::INSERT_VALUES);
            $this->insert_values[] = $value;
            return $this;
        }

        public function value($value) {
            //$this->checkState(self::INSERT_VALUES);
            $this->insert_values[] = $value;
            return $this;
        }
    }