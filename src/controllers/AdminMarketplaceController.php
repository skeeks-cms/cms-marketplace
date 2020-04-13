<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010-2014 SkeekS (Sx)
 * @date 06.02.2015
 * @since 1.0.0
 */

namespace skeeks\cms\marketplace\controllers;

use skeeks\cms\backend\BackendController;
use skeeks\cms\components\marketplace\models\PackageModel;
use skeeks\cms\models\Comment;
use skeeks\cms\modules\admin\actions\AdminAction;

/**
 * Class AdminMarketplaceController
 * @package skeeks\cms\marketplace\controllers
 */
class AdminMarketplaceController extends BackendController
{
    public function init()
    {
        $this->name = "Маркетплейс";
        $this->generateAccessActions = false;

        $this->accessCallback = function () {
            if (!\Yii::$app->cms->site->is_default) {
                return false;
            }
            return \Yii::$app->user->can($this->uniqueId);
        };

        parent::init();
    }

    public function actions()
    {
        return
            [
                "index" => [
                    "class" => AdminAction::className(),
                    "name"  => "Установленные",
                ],

                "catalog" => [
                    "class" => AdminAction::className(),
                    "name"  => "Каталог",
                ],

                "install" => [
                    "class"    => AdminAction::className(),
                    "name"     => "Установить/Удалить",
                    "callback" => [$this, 'actionInstall'],
                ],

                /*"update" => [
                    "class" => AdminAction::className(),
                    "name" => "Обновление платформы",
                ],  

                "test" => [
                    "class" => AdminAction::className(),
                    "name" => "test",
                ],*/
            ];
    }

    public function actionInstall()
    {
        $packageModel = null;

        if ($packagistCode = \Yii::$app->request->get('packagistCode')) {
            $packageModel = PackageModel::fetchByCode($packagistCode);
        }

        return $this->render($this->action->id, [
            'packagistCode' => $packagistCode,
            'packageModel'  => $packageModel,
        ]);
    }
}
