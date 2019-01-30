<?php

require_once("../movian_repo.php");


/*
 * Test function
 */
function movian_repo_test($list) {

    // we use curl as callback function
    $mp = new MovianRepo( function($url) {
                              $ch =  curl_init($url);
                              curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // returns empty string n failure
                              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                              curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                              curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                              curl_setopt($ch, CURLOPT_TIMEOUT, 3);
                              curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
                              curl_setopt($ch, CURLOPT_USERAGENT,  MovianRepo::getUserAgent());
                              $result = curl_exec($ch);
                              return $result;
                           }
                         );

    return $mp->build($list);
}

header('Content-Type: application/json');
echo movian_repo_test(array("/czz/movian-plugin-zooqle"));
