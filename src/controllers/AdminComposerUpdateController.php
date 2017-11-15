<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010-2014 SkeekS (Sx)
 * @date 06.02.2015
 * @since 1.0.0
 */

namespace skeeks\cms\marketplace\controllers;

use skeeks\cms\backend\BackendAction;
use skeeks\cms\components\marketplace\models\PackageModel;
use skeeks\cms\helpers\UrlHelper;
use skeeks\cms\models\Comment;
use skeeks\cms\modules\admin\actions\AdminAction;
use skeeks\cms\modules\admin\controllers\AdminController;
use skeeks\sx\helpers\ResponseHelper;
use Yii;
use skeeks\cms\models\User;
use skeeks\cms\models\searchs\User as UserSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;

/**
 * Class AdminMarketplaceController
 * @package skeeks\cms\marketplace\controllers
 */
class AdminComposerUpdateController extends AdminController
{
    public $defaultAction = 'update';

    public function init()
    {
        $this->name = \Yii::t('skeeks/marketplace', 'Updated platforms');
        parent::init();
    }

    public function actions()
    {
        return [
            "update" => [
                "class" => BackendAction::className(),
                "name" => \Yii::t('skeeks/marketplace', 'Updated platforms'),
                "callback" => [$this, 'actionUpdate'],
            ],
        ];
    }

    protected function getFileUpdateSuccessResult() {
        return \Yii::getAlias('@runtime/skeeks-update/success.log');
    }

    protected function getFileUpdateErrorResult() {
        return \Yii::getAlias('@runtime/skeeks-update/error.log');
    }

    protected function getFileUpdateDir() {
        return \Yii::getAlias('@runtime/skeeks-update');
    }

    public function actionUpdate()
    {
        $rr = new ResponseHelper();

        FileHelper::createDirectory($this->getFileUpdateDir());

        $fileUpdateSuccessResult = $this->getFileUpdateSuccessResult();
        $fileUpdateErrorResult = $this->getFileUpdateErrorResult();

        if ($rr->isRequestAjaxPost) {

            $cmd = "COMPOSER_HOME=.composer php composer.phar self-update && COMPOSER_HOME=.composer php composer.phar update -o --no-interaction >{$fileUpdateSuccessResult} 2>&1 3>{$fileUpdateErrorResult} &";
    
            $process = new \Symfony\Component\Process\Process($cmd, \Yii::getAlias('@root'));
            $process->run();
            
            $rr->success = true;
            $rr->message = 'Процесс обновления запущен';
            return $rr;
        }
        
        $lastResultSuccess = '';
        if (file_exists($fileUpdateSuccessResult)) {
            $file = fopen($fileUpdateSuccessResult, "r");
            if ($file && filesize($fileUpdateSuccessResult)) {
                $lastResultSuccess = fread($file, filesize($fileUpdateSuccessResult));
            }
            fclose($file);
        }

        $lastResultError = '';
        if (file_exists($fileUpdateErrorResult)) {
            $file = fopen($fileUpdateErrorResult, "r");
            if ($file && filesize($fileUpdateErrorResult)) {
                $lastResultError = fread($file, filesize($fileUpdateErrorResult));
            }
            fclose($file);
        }

        return $this->render($this->action->id, [
            'fileUpdateResult'      => $fileUpdateSuccessResult,
            'fileUpdateErrorResult' => $fileUpdateErrorResult,
            'lastSuccessResult' => $lastResultSuccess,
            'lastErrorResult' => $lastResultError,
        ]);
    }

    public function actionStep()
    {
        $rr = new ResponseHelper();

        if ($rr->isRequestAjaxPost) {

            $filePathSuccess = $this->getFileUpdateSuccessResult();
            $filePathError = $this->getFileUpdateErrorResult();

            $contentSuccess = '';
            $contentError = '';

            if (file_exists($filePathSuccess)) {
                $file = fopen($filePathSuccess, "r");
                if ($file && filesize($filePathSuccess)) {
                    $contentSuccess = fread($file, filesize($filePathSuccess));
                }
                fclose($file);
            }

            if (file_exists($filePathError)) {
                $file = fopen($filePathError, "r");
                if ($file && filesize($filePathError)) {
                    $contentError = fread($file, filesize($filePathError));
                }
                fclose($file);
            }


            $rr->success = true;
            $rr->data = [
                'stop' => false,

                'successContent' => $contentSuccess,
                'errorContent' => $contentError
            ];
        }

        return $rr;
    }
}
