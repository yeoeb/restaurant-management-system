<?php

$base_url ='http://ciai.nptu.edu.tw/Database_Systems/cbb109110';
define('host','localhost');
define('dbUser','ciai_dbst');
define('dbPassword','000000');
define('dbName','cbb109110');
function conn(){
    $link = mysqli_connect(host,dbUser,dbPassword,dbName);
    return $link;
}
?>