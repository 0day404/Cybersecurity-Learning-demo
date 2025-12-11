<?php

//__wakeup：反序列化恢复对象之前调用该方法
//CVE-2016-7124 __wakeup绕过
class Test{
    public $sex;
    public $name;
    public $age;

    public function __construct($name, $age, $sex){
        echo "__construct被调用!<br>";
    }

    public function __wakeup(){
        echo "<br>__wakeup()被调用<br>";
    }

    public function __destruct(){
        echo "<br>__destruct()被调用<br>";
    }

 //

}
//$t =new Test("xiaodi",30,"man");
//print_r(serialize($t));
unserialize($_GET['x']);
?>
