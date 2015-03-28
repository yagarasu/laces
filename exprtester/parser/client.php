<?php

    if(!isset($_POST['code'])) {
        http_response_code(400);
        die('Missing parameters. Please check your request.');
    }
    
    require '../../laces/Context.class.php';
    require 'isolatedParser/Expression.class.php';
    
    $code = $_POST['code'];
    
    $c = new Context();
    
    // test data
    $c->set('$num',123);
    $c->set('$str','hello hello');
    $c->set('#foo','<em>foooo</em>');
    
    $e = new Expression($code, $c);
    
    try {
        var_dump($e->parse());
        echo "\n" . 'Context: ' . var_export($c->getRawArray(), true);
        exit();
    } catch(Exception $e) {
        http_response_code(500);
        die('Exception. ' . $e->getMessage());
    }

?>