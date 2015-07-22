<?php

    class ParseElement {
        private $name = "";

        public function __construct( $name ) {
            $this->name = $name;
        }

        public function getName() {
            return $this->name;
        }
    }

    class Symbol extends ParseElement {
        private $consistence = Array();

        public function consists( Array $parseElements ) {
            $this->consistence[] = $parseElements;
        }

        /**
         * @return array
         */
        public function getConsistence() {
            return $this->consistence;
        }
    }

    class Terminal extends ParseElement {
    }