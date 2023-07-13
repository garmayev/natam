<?php

namespace frontend\controllers;

use common\models\Category;
use common\models\Order;
use common\models\Post;
use common\models\Product;
use common\models\Service;
use kartik\mpdf\Pdf;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index', [
			"categoryProvider" => new ActiveDataProvider([
				"query" => Category::find()
					->select(['category.*', 'COUNT(p.id) AS product_count'])
					->leftJoin('product p', "category.id = p.category_id AND p.isset <> 1")
					->where(['category.main' => 1])
					->groupBy(["category.id"])
					->orderBy(["product_count" => SORT_DESC, "id" => SORT_DESC])
//					->orderBy(["id" => SORT_DESC])
			]),
        	"postProvider" => new ActiveDataProvider([
        		"query" => Post::find()
	        ]),
	        "productProvider" => new ActiveDataProvider([
	        	"query" => Product::find()->where(["visible" => 1])
	        ]),
	        "serviceProvider" => new ActiveDataProvider([
	        	"query" => Service::find()->where(["parent_id" => null])
	        ])
        ]);
    }

    public function actionAbout()
    {
		$this->view->title = Yii::t("app", "About company");
    	return $this->render("about");
    }

    public function actionContact()
    {
		$this->view->title = Yii::t("app", "Contact");
    	return $this->render("contact");
    }

    public function actionPrice()
    {
	    Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
	    $pdf = new Pdf([
		    'mode' => Pdf::MODE_UTF8, // leaner size using standard fonts
		    'destination' => Pdf::DEST_BROWSER,
		    'content' => $this->renderPartial('_price', [
		    	"productProvider" => new ActiveDataProvider([
		    		"query" => Product::find()
			    ])
		    ]),
		    'options' => [
			    // any mpdf options you wish to set
		    ],
		    'methods' => [
			    'SetTitle' => 'Прайслист',
			    'SetHeader' => [Yii::$app->name.'||Сгенерирован: ' . date("l, d M Y H:i:s")],
			    'SetFooter' => [Yii::$app->name.'||'],
			    'SetAuthor' => Yii::$app->name,
			    'SetCreator' => Yii::$app->name,
		    ]
	    ]);
	    return $pdf->render();

    }

	public function actionAddition()
	{
		$categories = Category::find()->where(['main' => 0])->all();
		$productProvider = new ActiveDataProvider([
			'query' => Product::find()->where(['category_id' => ArrayHelper::map($categories, 'id', 'id')]),
			'pagination' => false,
		]);
		return $this->render('addition', [
			'productProvider' => $productProvider
		]);
	}

    public function actionChangeMode($date) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $start = strtotime("midnight", $date / 1000);
        $finish = strtotime("+1 day", $start);
        Yii::error($start, $finish);
        return ArrayHelper::map(Order::find()->where(['between', 'delivery_date', $start, $finish])->andWhere(['>', 'status', Order::STATUS_COMPLETE])->all(), 'id', 'client_id', 'delivery_date');
    }
}
