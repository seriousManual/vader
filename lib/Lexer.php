<?php

    require_once 'lib/Parseelements.php';

    class LexerException extends Exception {}

    abstract class Lexer {
        private $symbols = Array();

        public function __construct() {
            $this->addSymbol( "[ \t\r\n]", "V_BLANK", "", true );
            //$this->addSymbol( "\/\*(.*)\*\/", "V_COMMENT_MULTIL", "", true );
            //$this->addSymbol( "\/\/(.*)", "V_COMMENT_SINGLEL", "", true );
            $this->init();
        }

        public function addSymbol( $pattern, $symbol, $relevant="", $nothing=false ) {
            $this->symbols[ $symbol ] = Array( "pattern" => $pattern, "relevant" => ( $relevant ? $relevant : $pattern ), "nothing" => $nothing );
        }

        public function parse( $string ) {
            $retVal = $this->pParse( $string );
            return $retVal;
        }

        private function pParse( $string ) {
            $m = Array();
            $result = Array();

            while( strlen( $string ) > 0 ) {
                $changedSth = false;
                foreach( $this->symbols as $k => $v ) {
                    $pattern = "/^" . $v[ 'pattern' ] . "/u";
                    if ( preg_match( $pattern, $string, $m ) ) {
                        if ( count($m) > 1 ) {
                            $r = $m[1];
                        } else {
                            $r = $m[0];
                        }

                        if ( !$v['nothing'] ) { //in case of blanks or comments........ ignore them.
                            $result[] = Array( "symbol" => $k, "relevant" => $r );
                        }

                        $string = preg_replace( "/^" . preg_quote( $m[0] ) . "/u", "", $string );
                        $changedSth = true;
                        break;
                    }
                }
                if ( !$changedSth ) {
                    throw new LexerException( "Syntax Error: " . $string[0] . " not defined" );
                }
            }
             return $result;
        }

        abstract protected function init();
    }