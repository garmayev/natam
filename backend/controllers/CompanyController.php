<?php

namespace backend\controllers;

use common\models\Client;
use common\models\Company;
use common\models\search\CompanySearch;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CompanyController implements the CRUD actions for Company model.
 */
class CompanyController extends Controller
{
	/**
	 * {@inheritdoc}
	 */
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow' => true,
						'roles' => ['@'],
					],
				],
				'denyCallback' => function () {
					Url::remember(Url::current());
					return $this->redirect(['user/security/login']);
				}
			],
		];
	}

	/**
     * Lists all Company models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new CompanySearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

		if ( !Yii::$app->user->can('employee') ) {
			$client = Client::findOne(['user_id' => Yii::$app->user->id]);
			return $this->redirect(['view', 'id' => $client->company_id]);
		}

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Company model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Company model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Company();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Company model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Company model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
		Url::remember(Yii::$app->request->referrer);
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

	public function actionJoin($id, $client_id = null)
	{
		$company = Company::findOne($id);
		if ( Yii::$app->user->can('employee') ) {
			if (isset($client_id)) {
				$client = Client::findOne($client_id);
				$company->join($client);
				return $this->redirect(['view', 'id' => $id]);
			}
			return $this->render('join', [
				'model' => $company
			]);
		} else {
			throw new ForbiddenHttpException(Yii::t("app", 'Sorry, You don`t have permission to this command'));
		}
	}

	public function actionUnjoin($id, $client_id) {
		$client = Client::findOne($client_id);
		$client->company_id = null;
		if ($client->save()) {
			return $this->redirect(['view', 'id' => $id]);
		}
	}

	public function actionAnalyze($id)
	{
		$model = $this->findModel($id);
		if ( Yii::$app->request->isPost ) {
			$model->analyze();
		}
		return $this->render('analyze', [
			'model' => $model
		]);
	}

    /**
     * Finds the Company model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Company the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Company::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
