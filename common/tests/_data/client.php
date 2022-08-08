<?php
$faker = \Faker\Factory::create();
$result = [];
for ($i = 1; $i < 101; $i++) {
    $result[] = [
        "name" => $faker->name,
        "phone" => $faker->phoneNumber,
        "email" => $faker->email,
        "company" => $faker->company,
        "chat_id" => $faker->numberBetween(100000, 10000000),
        "user_id" => $i
    ];
}
return $result;