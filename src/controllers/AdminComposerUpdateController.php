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

    protected function getFileUpdateResult() {
        return \Yii::getAlias('@runtime/skeeks-update/update.log');
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

        $fileUpdateResult = $this->getFileUpdateResult();
        $fileUpdateErrorResult = $this->getFileUpdateErrorResult();

        if ($rr->isRequestAjaxPost) {

            $cmd = "COMPOSER_HOME=.composer php composer.phar self-update && COMPOSER_HOME=.composer php composer.phar update -o --no-interaction >{$fileUpdateResult} 2>&1 3>{$fileUpdateErrorResult} &";
    
            $process = new \Symfony\Component\Process\Process($cmd, \Yii::getAlias('@root'));
            $process->run();
            
            $rr->success = true;
            $rr->message = 'Процесс обновления запущен';
            return $rr;
        }
        
        $lastResult = '';
        if (file_exists($fileUpdateResult)) {
            $file = fopen($fileUpdateResult, "r");
            $lastResult = fread($file, filesize($fileUpdateResult));
            fclose($file);
        }

        $lastResultError = '';
        if (file_exists($fileUpdateErrorResult)) {
            $file = fopen($fileUpdateErrorResult, "r");
            $lastResultError = fread($file, filesize($fileUpdateErrorResult));
            fclose($file);
        }

        return $this->render($this->action->id, [
            'fileUpdateResult'      => $fileUpdateResult,
            'fileUpdateErrorResult' => $fileUpdateErrorResult,
            'lastResult' => $lastResult,
            'lastResultError' => $lastResultError,
        ]);
    }

    public function actionStep()
    {
        $rr = new ResponseHelper();

        if ($rr->isRequestAjaxPost) {

            $filePath = $this->getFileUpdateResult();

            if (!file_exists($filePath)) {
                throw new \Exception("Read file error '{$filePath}'");
            }
            $file = fopen($filePath, "r");
            if (!$file) {
                throw new \Exception("Unable to open file: '{$filePath}'");
            }
            $content = fread($file, filesize($filePath));
            fclose($file);

            $rr->success = true;
            $rr->data = [
                'stop' => false,
                'content' => $content
            ];
        }

        return $rr;
    }
}
