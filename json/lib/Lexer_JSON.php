<?php

    require_once 'lib/Lexer.php';

    class Lexer_JSON extends Lexer {
    
        protected function init() {
            $this->addSymbol( "{", "V_OBJ_START" );
            $this->addSymbol( "}", "V_OBJ_END" );
            $this->addSymbol( "\[", "V_ARR_START" );
            $this->addSymbol( "\]", "V_ARR_END" );
            
            $this->addSymbol( ":", "V_ASSIGNMENT" );
            $this->addSymbol( ",", "V_CONCAT" );
            
            $this->addSymbol( '"([\s\d\S\\\\\\"\\b\\f\\n\\r\\t]*?)"', 'V_STRING' );
            $this->addSymbol( "(-?(?:0|(?:[1-9]\d*))(?:\.\d+)?((?:e|E)(?:\+|-)?\d+)?)", "V_NUMBER" );
            $this->addSymbol( "true", "V_TRUE" );
            $this->addSymbol( "false", "V_FALSE" );
        }
    
    }