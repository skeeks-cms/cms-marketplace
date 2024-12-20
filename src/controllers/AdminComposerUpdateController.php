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
use skeeks\cms\backend\BackendController;
use skeeks\cms\components\marketplace\models\PackageModel;
use skeeks\cms\composer\update\PluginConfig;
use skeeks\cms\models\Comment;
use skeeks\cms\rbac\CmsManager;
use skeeks\sx\helpers\ResponseHelper;
use yii\helpers\FileHelper;

/**
 * @author Semenov Alexander <semenov@skeeks.com>
 */
class AdminComposerUpdateController extends BackendController
{
    public $defaultAction = 'update';

    public function init()
    {
        $this->name = \Yii::t('skeeks/marketplace', 'Updated platforms');
        $this->generateAccessActions = false;

        $this->generateAccessActions = false;
        $this->permissionName = CmsManager::PERMISSION_ROOT_ACCESS;

        parent::init();
    }

    public function actions()
    {
        return [
            "update" => [
                "class"    => BackendAction::className(),
                "name"     => \Yii::t('skeeks/marketplace', 'Updated platforms'),
                "callback" => [$this, 'actionUpdate'],
            ],
        ];
    }

    public function getTmpUpdateLockFilePath()
    {
        //TODO:: to fix it's
        //return \Yii::getAlias('@root/' . PluginConfig::UPDATE_LOCK_TMP_FILE);
        return \Yii::getAlias('@root/update.lock.tmp');
    }

    protected function getFileUpdateSuccessResult()
    {
        return \Yii::getAlias('@runtime/skeeks-update/success.log');
    }

    protected function getFileUpdateErrorResult()
    {
        return \Yii::getAlias('@runtime/skeeks-update/error.log');
    }

    protected function getFileUpdateDir()
    {
        return \Yii::getAlias('@runtime/skeeks-update');
    }

    /**
     * @return bool|int
     */
    public function lastUpdateTime()
    {
        $fileUpdateSuccessResult = $this->getFileUpdateSuccessResult();
        $fileUpdateErrorResult = $this->getFileUpdateErrorResult();

        $lastUpdateTime = 0;
        if (file_exists($fileUpdateSuccessResult)) {
            $lastUpdateTime = filemtime($fileUpdateSuccessResult);
        }

        $lastUpdateTimeError = 0;
        if (file_exists($fileUpdateErrorResult)) {
            $lastUpdateTimeError = filemtime($fileUpdateErrorResult);
        }

        if ($lastUpdateTimeError > $lastUpdateTime) {
            $lastUpdateTime = $lastUpdateTimeError;
        }

        return $lastUpdateTime;
    }

    public function passedUpdateTime()
    {
        return time() - $this->lastUpdateTime();
    }

    public function isRunningUpdate()
    {
        return file_exists($this->getTmpUpdateLockFilePath());
    }

    public function actionUpdate()
    {

        $rr = new ResponseHelper();

        FileHelper::createDirectory($this->getFileUpdateDir());

        $fileUpdateSuccessResult = $this->getFileUpdateSuccessResult();
        $fileUpdateErrorResult = $this->getFileUpdateErrorResult();

        if ($rr->isRequestAjaxPost) {

            $root = \Yii::getAlias('@root');
            $cmd = "cd {$root} && COMPOSER_HOME=.composer php composer.phar self-update --2";
            $cmd2 = "COMPOSER_HOME=.composer php composer.phar update -o --no-interaction >{$fileUpdateSuccessResult} 2>&1 3>{$fileUpdateErrorResult} &";
            $cmdfull = $cmd." && ".$cmd2;

            $response = [];
            $result = false;
            exec($cmdfull, $response, $result);

            //$process = new \Symfony\Component\Process\Process([\Yii::getAlias('@root'), "pwd"]);
            //$process = new \Symfony\Component\Process\Process(["COMPOSER_HOME=.composer", "/usr/bin/php", "composer.phar", "self-update", "--2"]);
            //$process = new \Symfony\Component\Process\Process(["chmod", "+x", "update.sh", "./update.sh"]);
            /*$process = new \Symfony\Component\Process\Process(["php yii"]);
            $process->setWorkingDirectory(\Yii::getAlias('@root'));
            $process->run();

            // executes after the command finishes
            if (!$process->isSuccessful()) {

                throw new ProcessFailedException($process);
            }
            
            var_dump($process->getOutput());
            die;*/

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
            'lastSuccessResult'     => $lastResultSuccess,
            'lastErrorResult'       => $lastResultError,
            'lastUpdateTime'        => $this->lastUpdateTime(),
            'isRunningUpdate'       => $this->isRunningUpdate(),
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
                'successContent' => $contentSuccess,
                'errorContent'   => $contentError,
            ];

            if ($this->isRunningUpdate()) {
                $rr->data['stop'] = false;
            } else {
                $rr->data['stop'] = true;
            }
        }

        return $rr;
    }
}
