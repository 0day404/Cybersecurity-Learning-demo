<?php
$a = new SoapClient(null,array('location'=>'http://127.0.0.1:2222/aaa', 'uri'=>'http://127.0.0.1:2222'));
$b = serialize($a);
echo $b;
?>