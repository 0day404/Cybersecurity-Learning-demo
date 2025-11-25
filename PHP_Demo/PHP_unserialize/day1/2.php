<?php
header("Content-type: text/html; charset=utf-8");


////__construct __destruct 魔术方法 创建调用__construct 2种销毁调用__destruct
//class Test{
//    public $name;
//    public $age;
//    public $sex;
//    // __construct：实例化对象时被调用.其作用是拿来初始化一些值。
//    public function __construct(){
//        echo "__construct 初始化魔术方法 被执行"."<br>";
//    }
//    // __destruct：当删除一个对象或对象操作终止时被调用。其最主要的作用是拿来做垃圾回收机制。
//    /*
//     * 当对象销毁时会调用此方法
//     * 一是用户主动销毁对象，二是当程序结束时由引擎自动销毁
//     */
//    function __destruct(){
//       echo "__destruct 类魔术方法执行完毕"."<br>";
//    }
//}

 //主动销毁
//$test = new Test();
//unset($test);
//echo '第一种执行完毕'.'<br>';
//echo '----------------------<br>';
//
//////程序结束自动销毁
//$test = new test("xiaodi",31, 'Test String');
//echo '第二种执行完毕'.'<br>';

//__sleep()：serialize之前被调用，可以指定要序列化的对象属性。
//class Test{
//    public $name;
//    public $age;
//    public $string;
//
//    // __construct：实例化对象时被调用.其作用是拿来初始化一些值。
//    public function __construct($name, $age, $string){
//        echo "__construct 初始化"."<br>";
//        $this->name = $name;
//        $this->age = $age;
//        $this->string = $string;
//    }
//
//    //  __sleep() ：serialize之前被调用，可以指定要序列化的对象属性
//    public function __sleep(){
//        echo "当在类外部使用serialize()时会调用这里的__sleep()方法<br>";
//        // 例如指定只需要 name 和 age 进行序列化，必须返回一个数值
//        return array('name', 'age','string');
//    }
//}
//
//$a = new Test("xiaodi",31, 'good teacher');
//echo serialize($a);


//__wakeup：反序列化恢复对象之前调用该方法
//class Test{
//    public $sex;
//    public $name;
//    public $age;
//
//    public function __construct($name, $age, $sex){
//        echo "__construct被调用!<br>";
//    }
//
//    public function __wakeup(){
//        echo "当在类外部使用unserialize()时会调用这里的__wakeup()方法<br>";
//    }
//}
//
//$person = new Test('xiaodi',31,'男');
//$a = serialize($person);
//unserialize($a);


//__INVOKE():将对象当做函数来使用时执行此方法，通常不推荐这样做。
//class Test{
//    // _invoke()：以调用函数的方式调用一个对象时，__invoke() 方法会被自动调用
//    public function __invoke($param1, $param2, $param3)
//{
//        echo "这是一个对象<br>";
//        var_dump($param1,$param2,$param3);
//    }
//}
//
//$a  = new Test();
////将对象当做函数调用 触发__invoke魔术方法
//$a('xiaodi', 31, '男');


//__toString()：如果一个对象类中存在__toString魔术方法，这个对象类被当做字符串进行处理时，就会触发__toString魔术方法
//class Test
//{
//    public $variable = 'good is string';
//
//    public function good(){
//        echo $this->variable . '<br />';
//    }
//
//    // 在对象当做字符串的时候会被调用
//    public function __toString(){
//        return '__toString魔术方法被执行！';
//    }
//}
//
//$a = new Test();
////输出调用
//echo $a;


//__CALL 魔术方法 调用某个方法， 若方法存在，则直接调用；若不存在，则会去调用__call函数。
//class Test{
//
//    public function good($number,$string){
//        echo '存在good方法'.'<br>';
//        echo $number.'---------'.$string.'<br>';
//    }
//
//    // 当调用类中不存在的方法时，就会调用__call();
//    public function __call($method,$args){
//        echo '不存在'.$method.'方法'.'<br>';
//        var_dump($args);
//    }
//}

//$a = new Test();
//$a->good(1,'xiaodisec');
//不存在xiaodi方法 触发__call魔术方法
//$b = new Test();
//$b->xiaodi(899,'no');



//__get() 魔术方法 读取一个对象的属性时，若属性存在，则直接返回属性值；若不存在，则会调用__get函数
//class Test {
//    public $n='xiaodisec';
//
//    public $xiaodi='gay';
//    // __get()：访问不存在的成员变量时调用
//    public function __get($name){
//        echo '__get 不存在成员变量'.$name.'<br>';
//    }
//}
//
//$a = new Test();
//// 存在成员变量n，所以不调用__get
////echo $a->n;
////echo '<br>';
//// 不存在成员变量spaceman，所以调用__get
//echo $a->xiaodi;


//__set()魔术方法 设置一个对象的属性时， 若属性存在，则直接赋值；若不存在，则会调用__set函数。
//class Test{
//    public $noway=0;
//
//    // __set()：设置对象不存在的属性或无法访问(私有)的属性时调用
//    /* __set($name, $value)
//     * 用来为私有成员属性设置的值
//     * 第一个参数为你要为设置值的属性名，第二个参数是要给属性设置的值，没有返回值。
//     * */
//
//    public function __set($name,$value){
//        echo '__set 不存在成员变量 '.$name.'<br>';
//        echo '即将设置的值 '.$value."<br>";
//        $this->noway=$value;
//    }
//
//    public function Get(){
//        echo $this->noway;
//    }
//}
//
//$a = new Test();
// 访问noway属性时调用，并设置值为899
//$a->noway  = 123;
//// 经过__set方法的设置noway的值为899
//$a->Get();
//echo '<br>';
// 设置对象不存在的属性xiaodi
//$a->xiaodi = 31;
//// 经过__set方法的设置noway的值为31
//$a->Get();



//__isset(): 检测对象的某个属性是否存在时执行此函数。当对不可访问属性调用 isset() 或 empty() 时，__isset() 会被调用
//class Person{
//    public $sex; //公共的
//    private $name; //私有的
//    private $age; //私有的
//
//    public function __construct($name, $age, $sex){
//        $this->name = $name;
//        $this->age = $age;
//        $this->sex = $sex;
//    }
//
//    // __isset()：当对不可访问属性调用 isset() 或 empty() 时，__isset() 会被调用。
//    public function __isset($content){
//        //echo "当在类外部使用isset()函数测定私有成员 {$content} 时，自动调用<br>";
//        echo "__isset被执行了","<br>";
//        return isset($this->$content);
//    }
//}
//
//$person = new Person("xiaodi", 31,'男');
//// public 成员
//echo ($person->sex),"<br>";
// //private 成员
////isset($person->name);
//empty($person->name);


//__unset()：在不可访问的属性上使用unset()时触发 销毁对象的某个属性时执行此函数
//class Person{
//    public $sex;
//    private $name;
//    private $age;
//
//    public function __construct($name, $age, $sex){
//        $this->name = $name;
//        $this->age = $age;
//        $this->sex = $sex;
//    }
//
//    // __unset()：销毁对象的某个属性时执行此函数
//    public function __unset($content) {
//        echo "当在类外部使用unset()函数来删除私有成员 {$content} 时自动调用的<br>";
//        //echo isset($this->$content)."<br>";
//    }
//}
//
//$person = new Person("xiaodi", 31,"男"); // 初始赋值
//unset($person->sex);//不调用 属性共有
//unset($person->name);//调用 属性私有 触发__unset
//unset($person->age);//调用 属性私有 触发__unset






?>