<?php

namespace common\tests;

use common\models\Client;
use common\models\Order;
use unit\fixtures\CategoryFixture;
use unit\fixtures\ClientFixture;
use unit\fixtures\LocationFixture;
use unit\fixtures\ProductFixture;
use unit\fixtures\UserFixture;

class OrderTest extends \Codeception\Test\Unit
{
    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function _fixtures()
    {
        return [
            'locations' => LocationFixture::class,
            'users' => UserFixture::class,
            'clients' => ClientFixture::class,
            'categories' => CategoryFixture::class,
            'products' => ProductFixture::class,
        ];
    }

    private $data = [
        'Order' => [
            'status' => '1',
            'products' => [
                0 => [
                    'product_id' => '16',
                    'product_count' => '1',
                ]
            ],
            'comment' => '',
            'delivery_date' => '2022-08-31 09:00',
            'address' => 'Россия, Республика Бурятия, Улан-Удэ, 111-й микрорайон, 3/1',
            'location' => [
                'title' => 'Россия, Республика Бурятия, Улан-Удэ, 111-й микрорайон, 3/1',
                'latitude' => '51.77140930111293',
                'longitude' => '107.58506168286382',
            ],
            'delivery_city' => '1',
            'delivery_distance' => '15.994',
            'client' => [
                'name' => 'qwe',
                'phone' => '+7(950) 397 5524',
                'company' => '',
            ]
        ],
    ];

    public function testCreate()
    {
        $book1 = $this->tester->grabFixture('books','book1');
        $order = new Order();
        $order->load($this->data);
        $this->assertTrue($order->save());
    }

    public function testClient()
    {
        $order = new Order();
        $order->load($this->data);
        $client = Client::findOne(['phone' => '79503975524']);
        $this->assertEquals($order->client_id, $client->id);
    }
}