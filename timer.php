<?php

date_default_timezone_set("Asia/Kuala_Lumpur");

while(1) {

    $str = trim(fgets(STDIN));
    if (strlen($str) === 0) continue;

    file_put_contents("/tmp/debug", "'$str'\n", FILE_APPEND);
    $str = explode(" ", $str);

    $output = array();
    $status = NULL;
    $username = trim(rawurldecode($str[0]));
    $password = trim(rawurldecode($str[1]));

    $auth = "/usr/lib64/squid/ncsa_auth /etc/squid/users";

/*
    $cmd="echo '$username $password' | $auth";
    exec($cmd, $output, $status);

    if ( $output[0] !== "OK") {

        echo "ERR\n";
        continue;

    } else {


        echo "OK\n";
        continue;

    }
*/

    echo "OK\n";
    continue;



}


?>

