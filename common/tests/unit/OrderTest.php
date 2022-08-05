<?php

namespace common\tests;

use common\models\Order;
use Yii;

class OrderTest extends \Codeception\Test\Unit
{
	// tests
	public function testCreate()
	{
		$data = [
			'Order' => [
				'products' => [
					0 => [
						'product_id' => '17',
						'product_count' => '1'
					]
				],
			],
		];
		$order = new Order();
		Yii::error($data);
		$order->load($data);
		$this->assertEquals(1, count($order->products));
		$this->assertTrue($order->save());
	}
}