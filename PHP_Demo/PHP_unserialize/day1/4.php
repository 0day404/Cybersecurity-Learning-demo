<?php

$test='O:4:"user":3:{s:4:"name";s:6:"xiaodi";s:3:"sex";s:3:"man";s:3:"age";i:32;}';
$data=unserialize($test);
var_dump($data);
