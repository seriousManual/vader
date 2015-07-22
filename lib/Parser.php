<?php

    require_once 'lib/Parseelements.php';

    class ParserException extends Exception {}

    abstract class Parser {
        private $symbols = Array();
        private $depth = 0;
        private $parseTree = Array();
        private $debug = false;

        public function __construct() {
            $this->init();
        }

        public function registerSymbol(Symbol $symbol ) {
            $this->symbols[ $symbol->getName() ] = $symbol;
        }

        public function parse( $tokenStream, $debug=false ) {
            $this->debug = $debug;

            if ( !isset( $this->symbols[ "V_START" ] ) ) {
                throw new ParserException( "Missing Start Symbol!" );
            }
            if ( count( $tokenStream ) <= 0 ) { //parseband is through, still something on inputband????? breaking bad.
                throw new ParserException( "Invalid Tokenstream!" );
            }

            $mySymbol = $this->symbols[ "V_START" ];

            $this->step( $tokenStream, $mySymbol, $this->parseTree );

            if ( count( $tokenStream ) > 0 ) { //something left on the inputband? error!!!!
                throw new ParserException( "Invalid Token: " . $tokenStream[0]['symbol'] );
            }

            return true;
        }

        private function step( &$tokenStream, Symbol $symbol, &$parseTreeBranch ) {
            $returnValue = false;

            $this->depth++;
            $alternatives = $symbol->getConsistence();

            $altChangedSth = false;

            //iterating over alternatives
            foreach( $alternatives as $alt ) {
                //iterating over symbols and terminals an alternative consists of
                $alternativeMatched = true;

                $first = true;
                foreach( $alt as $parseElement ) {
                    if ( $this->debug ) $this->debugOutput( str_repeat( "&nbsp;&nbsp;&nbsp;", $this->depth ) . $this->depth . " " . $parseElement->getName() . " " . get_class( $parseElement ) );
                    if ( is_a( $parseElement, "Terminal" ) ) {
                        if ( $parseElement->getName() == "V_e" ) {
                            //alternative consists of epsilon, so alternative has matched, because alternative and therefore symbol has fullfilled it's destination
                            $alternativeMatched = true;
                            break;
                        } else {
							if ( count( $tokenStream ) == 0 ) {
								if ( $first ) {
									//having no more tokens, i say this alternative failes.but it's the first token to check, so let's see if epsilon is produced in another alternative
									$alternativeMatched = false;
									break;
								} else {
									//uh, I'm in the middle of something? breaking baaaaad.
									throw new ParserException( "I'm through with the Input-Band, i was expecting: " . $parseElement->getName() . ", not good!" );
								}
							}
                            //peek on tokenstream, $tokenStream[0] matches terminal->name?
                            if ( $parseElement->getName() == $tokenStream[0]['symbol'] ) {
                                if ( $this->debug ) $this->debugOutput( str_repeat( "&nbsp;&nbsp;&nbsp;", $this->depth ) . "&nbsp;&nbsp;&nbsp;FOUND! | " . $this->listTokenString( $tokenStream ) );
                                //shift it out
                                $parseTreeBranch[] = array_shift( $tokenStream );
                            } else {
                                if ( $this->debug ) $this->debugOutput( str_repeat( "&nbsp;&nbsp;&nbsp;", $this->depth ) . "&nbsp;&nbsp;&nbsp;not found! | " . $this->listTokenString( $tokenStream ) );
                                //hmm, is first?
                                if ( $first ) {
                                    //let's try another alternative
                                    $alternativeMatched = false;
                                    break;
                                } else {
                                    //i ran in this one...not return, breaking bad
                                    throw new ParserException( "Unexpected Token: " . $tokenStream[0]['symbol'] . ", Expected: " . $parseElement->getName() );
                                }
                            }
                        }
                    } else if ( is_a( $parseElement, "Symbol" ) ) {
                        //get symbol and pass it to new cycle of functioni
                        $mySymbol = $this->symbols[ $parseElement->getName() ];
                        $tmpParseTreeBranch = Array();
                        $retVal = $this->step( $tokenStream, $mySymbol, $tmpParseTreeBranch );

                        //got a symbol match or not? if yes, break alternative and switch to next one
                        if ( !$retVal ) {
                            if ( $first ) {
                                $alternativeMatched = false;
                                break;
                            } else {
                                //i ran in this one...not return, breaking bad
                                throw new ParserException( "Expected: " . $parseElement->getName() );
                            }
                        } else {
                            $parseTreeBranch[ $parseElement->getName() ] = $tmpParseTreeBranch;
                        }
                    }
                    $first = false;
                }
                if ( $alternativeMatched ) {
                    //current alternative produced a complete match. no need to check other alternatives
                    $returnValue = true;
                    break;
                }
            }
            $this->depth--;

            return $returnValue;
        }

        private function debugOutput( $string ) {

        }

        private function listTokenString( $tokenStream ) {
            $tmp = "";
            foreach( $tokenStream as $s ) {
                $tmp .= $s['symbol'] . " ";
            }
            return $tmp;
        }

        public function getParseTree() {
            return $this->parseTree;
        }


        abstract protected function init();
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
