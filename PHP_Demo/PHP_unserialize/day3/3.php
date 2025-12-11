<?php
class test{
    private $a;
    protected $b;
    public function __construct(){
        $this->a = 'abc';
    }
    public function __destruct(){
        echo $this->a;
    }
}


print_r(serialize(new test()));
//print_r(urlencode(serialize(new test())));

//unserialize($_GET['x']);

//unserialize('O:4:"test":1:{s:1:"a";s:3:"abc";}');


