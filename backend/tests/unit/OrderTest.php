<?php

namespace backend\tests;

use Codeception\Lib\Console\Output;
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
				'client' => [
					'name' => 'Гармаев Бато',
					'phone' => '79503975524',
				],
				'location' => [
					'title' => 'Россия, Республика Бурятия, Улан-Удэ, бульвар Карла Маркса, 11',
					'latitude' => 51.810137,
					'longitude' => 107.609116
				]
			],

		];
		$order = new Order();
		$output = new Output([]);
		$output->writeln('Load data to Order model', Output::OUTPUT_NORMAL);
		$this->assertTrue($order->load($data));
		$this->assertTrue($order->save());
		$output->writeln("{$order->client}", Output::OUTPUT_NORMAL);
	}
}