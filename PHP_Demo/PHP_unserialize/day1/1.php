<?php
header("Content-type: text/html; charset=utf-8");


class user{
    public $name='xiaodi';
    public $sex='man';
    public $age=32;
}

$demo=new user();
$s=serialize($demo);//序列化
echo $s;
//O:4:"user":3:{s:4:"name";s:6:"xiaodi";s:3:"sex";s:3:"man";s:3:"age";i:32;}



$u=unserialize($s);//反序列化
var_dump($u);