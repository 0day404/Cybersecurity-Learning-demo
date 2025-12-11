<?php
class Test
{
    public $sex = "man";
    private $name = "xiaodi";
    protected $age = "33";
}


$t=new Test();
print_r(serialize($t));
