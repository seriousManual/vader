<?php
    error_reporting(E_ALL);
    set_include_path(get_include_path() . PATH_SEPARATOR . realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..'));

    require_once 'json/lib/Lexer_JSON.php';
    require_once 'json/lib/Parser_JSON.php';
    require_once 'json/lib/Compiler_JSON.php';
    require_once 'lib/Lib.php';

    $mystring = '{"foo":"bar","baz":[{"baz":"bal","spam":1,"eggs":true},1,true,"asdf"]}';
    
    $p = new Profiler( true );

    $l = new Lexer_JSON();
    $tokens = $l->parse( $mystring );

    $p->addPoint("lex");

    $parser = new Parser_JSON();
    $ret = $parser->parse( $tokens );

    $p->addPoint("parse");

    if ( $ret ) {
        echo "<h2>Input</h2>";
        var_dumo($mystring);

        $tree = $parser->getParseTree();
        $compiler = new Compiler_JSON();

        $result = $compiler->compile( $tree );
        $p->addPoint("compile");

        echo "<h2>output</h2>";
        var_dumo( $result );
    }

    echo "<h2>timing</h2>";

    $p->printList();
    $p->printTotal();

    echo "<h2>timing native</h2>";

    $p2 = new Profiler( true );
    $blubb = json_encode( $mystring );
    $p2->addPoint( "json_decode" );
    $p2->printList();
    $p2->printTotal();