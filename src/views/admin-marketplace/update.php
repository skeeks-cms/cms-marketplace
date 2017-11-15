<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 24.06.2015
 */
/* @var $this yii\web\View */
/* @var string $packagistCode */
/* @var $packageModel PackageModel */
/*$packageModel = \skeeks\cms\marketplace\models\PackageModel::fetchByCode('skeeks/cms');*/
//exec("cd " . \Yii::getAlias('@root'). " && COMPOSER_HOME=.composer php composer.phar self-update && COMPOSER_HOME=.composer php composer.phar update -o > update.log 2>&1 &");

$cmd = "cd " . \Yii::getAlias('@root'). " && COMPOSER_HOME=.composer php composer.phar self-update && COMPOSER_HOME=.composer php composer.phar update -o > update.log 2>&1 3>error.log &";
$cmd = "cd " . \Yii::getAlias('@root'). " && php test.php > update.log 2>&1 3>error.log &";
$process = new Symfony\Component\Process\Process($cmd);
$process->run();

if (!$process->isSuccessful()) {
    throw new ProcessFailedException($process);
}

$pid = $process->getPid();


print_r($process->getStatus());
echo "<br />";
print_r($process->getCommandLine());
echo "<br />";
print_r($process->getTimeout());
echo "<br />";
var_dump($process->isRunning());
echo "<br />";

print_r($pid);
echo "<br />";
$process->wait(function ($type, $buffer) {
    if (Symfony\Component\Process\Process::ERR === $type) {
        echo 'ERR > '.$buffer;
    } else {
        echo 'OUT > '.$buffer;
    }
});


echo "<br />";

$p2 = new Symfony\Component\Process\Process('ps -lp 1' . $pid);
$p2->start();

$p2->wait(function ($type, $buffer) {
    if (Symfony\Component\Process\Process::ERR === $type) {
        echo 'ERR > '.$buffer;
    } else {
        echo 'OUT > '.$buffer;
    }
});

echo "<br />";

die;
?>

<div id="sx-search" style="margin-bottom: 10px;">
    <!--<p><b><a data-pjax="0" target="_blank" href="<? /*= $packageModel->url; */ ?>"><? /*=\Yii::t('skeeks/marketplace','{yii} Version',['yii' => 'SkeekS CMS'])*/ ?></a>: </b> <? /*= \Yii::$app->cms->descriptor->version; */ ?></p>-->
    <p><b><?= \Yii::t('skeeks/marketplace', '{yii} Version', ['yii' => 'Yii']) ?>: </b> <?= Yii::getVersion(); ?></p>
</div>
<hr/>
<p>
    Для обновления проекта воспользуйтесь мануалом:
    <a href="http://dev.cms.skeeks.com/docs/dev/ustanovka-nastroyka-konfigurirov/obnovlenie" target="_blank">
        как обновить SkeekS CMS?
    </a>
</p>
<p>
    Обратитесь к разработчикам CMS: <a href="http://skeeks.com/contacts" target="_blank">связь с разработчиками</a>.
</p>





