<?php

namespace app\modules\ws\controllers;

use yii\web\Controller;

/**
 * Default controller for the `ws` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->redirect('/ws/v2');
    }

    public function actionSoap_Insert() {
        return $this->render('soap_insert');
    }

    public function actionSoap_view() {

        return $this->render('soap_view');
    }
}
