<?php

    function var_dumo( $str ) {
        echo var_dumpo( $str );
    }
    function var_dumpo( $str ) {
        return "<pre>" . ( print_r( $str, true ) ) . "</pre>";
    }
    
    class Profiler {
        private $timePoints = Array();
        
        public function __construct( $start = false ) {
            $this->addPoint( "start ");
        }
        
        public function addPoint( $name ) {
            if ( in_array( $name, $this->timePoints ) ) {
                throw new Exception( "null" );
            }
            $this->timePoints[ $name ] = microtime( true );
        }
        
        public function getList() {
            $c = 0;
            $prev = "";
            $tmp = Array();
            foreach( $this->timePoints as $k => $point ) {
                if ( $c > 0 ) {
                    $tmp[ $k ] = sprintf( "%.15f", ($point - $prev) );
                }
                $prev = $point;
                $c++;
            }
            return $tmp;
        }
        
        public function printList() {
            foreach( $this->getList() as $k => $v ) {
                echo $k . " => " . $v . " seconds<br>";
            }
        }
        
        public function getTotal() {
            if ( count( $this->timePoints ) >= 2 ) {
                reset( $this->timePoints );
                $start = current( $this->timePoints );
                $end = end( $this->timePoints );
                return sprintf( "%.15f", ($end - $start) );
            }
            return 0;
        }
        
        public function printTotal() {
            if ( $ret = $this->getTotal() ) {
                echo "Overall Time: " . $ret . " seconds";
            }
        }
    }