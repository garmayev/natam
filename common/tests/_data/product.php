<?php
    $faker = Faker\Factory::create();
    return [
        [
            "id" => 6,
            "title" => "Аргон",
            "description" => "Ar - 99,993%",
            "price" => 2200,
            "value" => 6.4,
            "thumbs" => "/img/uploads/Аргон.png",
            "isset" => 0,
            "visible" => 1,
            "category_id" => 1,
        ],
        [
            "id" => 7,
            "title" => "Аргон высокой чистоты",
            "description" => "Ar - 99,998%",
            "price" => 3500,
            "value" => 6.4,
            "thumbs" => "/img/product-1.png",
            "isset" => 0,
            "visible" => 1,
            "category_id" => 1
        ],
        [
            "id" => 8,
            "title" => "Азот газообразный",
            "description" => "",
            "price" => 900,
            "value" => 6,
            "thumbs" => "",
            "isset" => 0,
            "visible" => 1,
            "category_id" => 2
        ],
        [
            "id" => 9,
            "title" => "Азот жидкий",
            "description" => "",
            "price" => 150,
            "value" => 1,
            "thumbs" => "",
            "isset" => 0,
            "visible" => 1,
            "category_id" => 2
        ],
        [
            "id" => 10,
            "title" => "Газовая смесь с аргоном",
            "description" => "Газовая смесь с аргоном в баллонах используются для сварочных работ в качестве защитной средый, улучшающей качество сварки относительно чистой углекислоты",
            "price" => 1800,
            "value" => 6.3,
            "thumbs" => "",
            "isset" => 0,
            "visible" => 1,
            "category_id" => 3
        ],
        [
            "id" => 11,
            "title" => "Газовая смесь с кислородом",
            "description" => "Газовая смесь с кислородом в баллонах используются как упаковочная среда для упаковки свежего мяса и мясных продуктов.",
            "price" => 1800,
            "value" => 6.3,
            "thumbs" => "",
            "isset" => 0,
            "visible" => 1,
            "category_id" => 3
        ],
        [
            "id" => 12,
            "title" => "Газовая смесь с азотом",
            "description" => "Газовая смесь с азотом в баллонах используются в качестве среды при вакумной упаковке пищевых продуктов, для увеличения сроков годности путем вытеснения кислорода и ингибированием процессов окисления (разложения)",
            "price" => 1800,
            "value" => 6.3,
            "thumbs" => "",
            "isset" => 0,
            "visible" => 1,
            "category_id" => 3
        ],
        [
            "id" => 13,
            "title" => "Кислород газообразный",
            "description" => "",
            "price" => 800,
            "value" => 24,
            "thumbs" => "",
            "isset" => 0,
            "visible" => 1,
            "category_id" => 5
        ],
        [
            "id" => 15,
            "title" => "Ацетилен",
            "description" => "",
            "price" => 7500,
            "value" => 5,
            "thumbs" => "",
            "isset" => 0,
            "visible" => 1,
            "category_id" => 6
        ],
        [
            "id" => 16,
            "title" => "Гелий",
            "description" => "",
            "price" => 28000,
            "value" => 5.7,
            "thumbs" => "",
            "isset" => 0,
            "visible" => 1,
            "category_id" => 7
        ],
        [
            "id" => 17,
            "title" => "Пропан",
            "description" => "",
            "price" => 1100,
            "value" => 50,
            "thumbs" => "",
            "isset" => 0,
            "visible" => 1,
            "category_id" => 8
        ],
        [
            "id" => 18,
            "title" => "Пропан",
            "description" => "",
            "price" => 700,
            "value" => 27,
            "thumbs" => "",
            "isset" => 0,
            "visible" => 1,
            "category_id" => 8
        ],
        [
            "id" => 19,
            "title" => "Пропан",
            "description" => "",
            "price" => 350,
            "value" => 12,
            "thumbs" => "",
            "isset" => 0,
            "visible" => 1,
            "category_id" => 8
        ],
        [
            "id" => 20,
            "title" => "Пропан",
            "description" => "",
            "price" => 150,
            "value" => 5,
            "thumbs" => "",
            "isset" => 0,
            "visible" => 1,
            "category_id" => 8
        ],
        [
            "title" => $faker->title,
            "description" => $faker->text,
            "price" => $faker->numberBetween(1, 50) * 100,
            "value" => $faker->randomFloat(),
            "thumbs" => "",
            "isset" => 0,
            "visible" => 1,
            "category_id" => \common\models\Category::findOne($faker->numberBetween(1, 11))
        ],
        [
            "title" => $faker->title,
            "description" => $faker->text,
            "price" => $faker->numberBetween(1, 50) * 100,
            "value" => $faker->randomFloat(),
            "thumbs" => "",
            "isset" => 0,
            "visible" => 1,
            "category_id" => \common\models\Category::findOne($faker->numberBetween(1, 11))
        ],
        [
            "title" => $faker->title,
            "description" => $faker->text,
            "price" => $faker->numberBetween(1, 50) * 100,
            "value" => $faker->randomFloat(),
            "thumbs" => "",
            "isset" => 0,
            "visible" => 1,
            "category_id" => \common\models\Category::findOne($faker->numberBetween(1, 11))
        ],
        [
            "title" => $faker->title,
            "description" => $faker->text,
            "price" => $faker->numberBetween(1, 50) * 100,
            "value" => $faker->randomFloat(),
            "thumbs" => "",
            "isset" => 0,
            "visible" => 1,
            "category_id" => \common\models\Category::findOne($faker->numberBetween(1, 11))
        ],
        [
            "title" => $faker->title,
            "description" => $faker->text,
            "price" => $faker->numberBetween(1, 50) * 100,
            "value" => $faker->randomFloat(),
            "thumbs" => "",
            "isset" => 0,
            "visible" => 1,
            "category_id" => \common\models\Category::findOne($faker->numberBetween(1, 11))
        ],
        [
            "title" => $faker->title,
            "description" => $faker->text,
            "price" => $faker->numberBetween(1, 50) * 100,
            "value" => $faker->randomFloat(),
            "thumbs" => "",
            "isset" => 0,
            "visible" => 1,
            "category_id" => \common\models\Category::findOne($faker->numberBetween(1, 11))
        ],
        [
            "title" => $faker->title,
            "description" => $faker->text,
            "price" => $faker->numberBetween(1, 50) * 100,
            "value" => $faker->randomFloat(),
            "thumbs" => "",
            "isset" => 0,
            "visible" => 1,
            "category_id" => \common\models\Category::findOne($faker->numberBetween(1, 11))
        ],
    ];
