<?php 
    namespace PandaStore\Types\Dto;

    require __DIR__."/../../interfaces/Builder.php";

    use PandaStore\Interfaces\Builder;

    use Exception;
    use BadMethodCallException;

    class NotImplementedException extends BadMethodCallException {

    }

    class StmntInvalidSyntax extends Exception {}
    
    class DbStmnt implements Builder {
        protected $state;

        public function build() {
            throw new NoTImplementedException();
        }

        protected function checkState($stateFlags) {
            if (!($stateFlags & $this->state
                  || ($stateFlags >> 1) & $this->state
                 )
               ) {
                $value = ($stateFlags >> 1) & $this->state;
                throw new StmntInvalidSyntax("Invalid syntax\ncurrent state: $this->state, flags: $stateFlags, $value");
            } else if (($stateFlags >> 1) & $this->state) {
                $this->state <<= 1;
            }
        }
    }