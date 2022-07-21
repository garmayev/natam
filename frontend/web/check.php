<?php
$phone = "+7 (950) 397-55-24";
$symbols = preg_replace('/([\+\ \(\)\-])/', '', $phone);
var_dump($symbols);