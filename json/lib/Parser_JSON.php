<?php

    require_once 'lib/Parser.php';
    
    class Parser_JSON extends Parser {
        
        protected function init() {
            /*
            start           -> value
            
            obj             -> obj_start        obj_content         obj_end
            obj_content     -> obj_pair         obj_morecontent                     | e
            obj_morecontent -> concat           obj_pair            obj_morecontent | e
            obj_pair        -> string           assignment          value

            arr             -> arr_start        arr_content         arr_end            
            arr_content     -> value            arr_morecontent                     | e
            arr_morecontent -> concat           value               arr_morecontent | e

            value           -> string | number | obj | arr | bool
            bool            -> true | false
            */
            $start =            new Symbol( "V_START" );
            $obj =              new Symbol( "V_OBJ" );
            $value =            new Symbol( "V_VALUE" );
            $obj_content =      new Symbol( "V_OBJ_CONTENT" );
            $obj_morecontent =  new Symbol( "V_OBJ_MORECONTENT" );
            $obj_pair =         new Symbol( "V_PAIR" );
            $arr =              new Symbol( "V_ARR" );
            $arr_content =      new Symbol( "V_ARR_CONTENT" );
            $arr_morecontent =  new Symbol( "V_ARR_MORECONTENT" );
            $bool =             new Symbol( "V_BOOL" );

            $start->consists              ( array( $value ) );
            $obj->consists                ( array( new Terminal( "V_OBJ_START" ),       $obj_content,                       new Terminal( "V_OBJ_END" ) ) );
            $obj_content->consists        ( array( $obj_pair,                           $obj_morecontent ) );
            $obj_content->consists        ( array( new Terminal("V_e" ) ) );
            $obj_morecontent->consists    ( array( new Terminal( "V_CONCAT" ),          $obj_pair,                          $obj_morecontent ) );
            $obj_morecontent->consists    ( array( new Terminal("V_e" ) ) );
            $obj_pair->consists           ( array( new Terminal( "V_STRING" ),          new Terminal( "V_ASSIGNMENT" ),     $value ) );
            $arr->consists                ( array( new Terminal( "V_ARR_START" ),       $arr_content,                       new Terminal( "V_ARR_END" ) ) );
            $arr_content->consists        ( array( $value,                              $arr_morecontent ) );
            $arr_content->consists        ( array( new Terminal( "V_e" ) ) );
            $arr_morecontent->consists    ( array( new Terminal( "V_CONCAT" ),          $value,                             $arr_morecontent ) );
            $arr_morecontent->consists    ( array( new Terminal( "V_e" ) ) );
            $value->consists              ( array( new Terminal( "V_STRING" ) ) );
            $value->consists              ( array( new Terminal( "V_NUMBER" ) ) );
            $value->consists              ( array( $bool ) );
            $value->consists              ( array( $obj ) );
            $value->consists              ( array( $arr ) );
            $bool->consists               ( array( new Terminal( "V_TRUE" ) ) );
            $bool->consists               ( array( new Terminal( "V_FALSE" ) ) );
            
            
            $this->registerSymbol( $start );
            $this->registerSymbol( $obj );
            $this->registerSymbol( $obj_content );
            $this->registerSymbol( $obj_morecontent );
            $this->registerSymbol( $obj_pair );
            $this->registerSymbol( $arr );
            $this->registerSymbol( $arr_content );
            $this->registerSymbol( $arr_morecontent );
            $this->registerSymbol( $value );
            $this->registerSymbol( $bool );
        }
        
    }