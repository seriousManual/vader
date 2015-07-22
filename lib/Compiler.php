<?php

    class CompilerException extends Exception {}

    abstract class Compiler {

        
        abstract public function compile( $tree );
    }