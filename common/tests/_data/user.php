<?php
$faker = \Faker\Factory::create();
$security = Yii::$app->getSecurity();
$result = [];

for ($i = 1; $i < 101; $i++) {
    $result[] = [
        "username" => $faker->userName,
        "email" => $faker->email,
        "auth_key" => $security->generateRandomString(),
        "password_hash" => $security->generatePasswordHash("password_$i"),
        "confirmed_at" => $faker->time,
    ];
}
return $result;