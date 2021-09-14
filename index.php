<?php

include './helper.php';

use App\Services\Gittigidiyor as Gittigidiyor;

$gittigidiyor = new Gittigidiyor("123","example","deneme","12345");

print_r($gittigidiyor->getCities());


