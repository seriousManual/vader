<?php
    require_once 'lib/Compiler.php';

    class Compiler_JSON extends Compiler {
    
        public function compile( $tree ) {
            $tmp = array_shift( $tree );
            
            return $this->v_value( $tmp );
        }
    
        private function v_value( $tree ) {
            $keys = array_keys( $tree );
            $key = $keys[0];
            $value = array_shift( $tree );

            switch( (string)$key ) {
                case "V_OBJ":
                    return $this->v_object( $value );
                    break;
                case "V_ARR":
                    return $this->v_array( $value );
                    break;
                case "V_BOOL":
                    return $this->v_bool( $value );
                    break;
            }
            
            switch( (string) $value['symbol'] ) {
                case "V_NUMBER":
                    if ( strpos( $value['relevant'], "." ) !== false ) {
                        return floatval( $value['relevant'] );
                    } else {
                        return intval( $value['relevant'] );
                    }
                    break;
                case "V_STRING":
                    return $value['relevant'];
                    break;
            }

            return null;
        }
        
        private function v_object( $tree ) {
            $ret = Array();

            $cont = $tree['V_OBJ_CONTENT'];
            
            if ( count( $cont ) > 0 ) { //empty array?
                $pair = $this->v_pair( $cont['V_PAIR'] );
                
                $ret[ $pair['key'] ] = $pair['value'];
                
                if ( count( $cont['V_OBJ_MORECONTENT'] ) > 0 ) {
                    $ret = $this->v_obj_morecontent( $cont['V_OBJ_MORECONTENT'], $ret );
                }
            }
            
            return $ret;
        }
        
        private function v_pair( $tree ) {
            $key = $tree[0]['relevant'];
            $value = $this->v_value( $tree['V_VALUE'] );

            return Array( "key" => $key, "value" => $value );
        }
        
        private function v_obj_morecontent( $tree, $obj ) {
            $pair = $this->v_pair( $tree['V_PAIR'] );
            $obj[ $pair['key'] ] = $pair['value'];
            
            if ( count( $tree['V_OBJ_MORECONTENT'] ) > 0 ) {
                $obj = $this->v_obj_morecontent( $tree['V_OBJ_MORECONTENT'], $obj );
            }
            
            return $obj;
        }
        
        private function v_array( $tree ) {
            $return = Array();
            $content = $tree['V_ARR_CONTENT'];

            if ( count( $content ) > 0 ) {
                $return[] = $this->v_value( $content['V_VALUE'] );

                if ( count( $content['V_ARR_MORECONTENT'] ) > 0 ) {
                    $return = $this->v_array_morecontent( $content['V_ARR_MORECONTENT'], $return );
                }
            }

            return $return;
        }
        
        private function v_array_morecontent( $tree, $obj ) {
            $obj[] = $this->v_value( $tree['V_VALUE'] );
            
            if ( count( $tree['V_ARR_MORECONTENT'] ) > 0 ) {
                $obj = $this->v_array_morecontent( $tree['V_ARR_MORECONTENT'], $obj );
            }
            
            return $obj;
        }
        
        private function v_bool( $tree ) {
            $tmp = array_shift( $tree );

            if ( $tmp['relevant'] == "true" ) {
                return true;
            } else {
                return false;
            }
        }
    
    }