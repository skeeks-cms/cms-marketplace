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


//echo filemtime(\Yii::getAlias('@root/update1.log'));die;
//$cmd = "php test.php >update1.log 2>&1 3>error.log &";

//$cmd = "COMPOSER_HOME=.composer php composer.phar self-update && COMPOSER_HOME=.composer php composer.phar update -o --no-interaction >update1.log 2>&1 3>error.log &";
/*$process = new Symfony\Component\Process\Process($cmd, \Yii::getAlias('@root'));
$process->run();
die;*/
$composerLockFile = \Yii::getAlias('@root/composer.lock');
?>

<div id="sx-search" style="margin-bottom: 10px;">
    <!--<p><b><a data-pjax="0" target="_blank" href="<? /*= $packageModel->url; */ ?>"><? /*=\Yii::t('skeeks/marketplace','{yii} Version',['yii' => 'SkeekS CMS'])*/ ?></a>: </b> <? /*= \Yii::$app->cms->descriptor->version; */ ?></p>-->
    <p><b><?= \Yii::t('skeeks/marketplace', '{yii} Version', ['yii' => 'Yii']) ?>: </b> <?= Yii::getVersion(); ?></p>
    <p><b>SkeekS CMS: </b> <?= Yii::$app->cms->version; ?></p>
    <? if (file_exists($composerLockFile)) : ?>
        <p><b>Время последнего обновления: </b> <?= \Yii::$app->formatter->asDatetime(filemtime($composerLockFile)); ?></p>
    <? else : ?>
        Файл <b><?= $composerLockFile; ?></b> не найден, данные о последнем обновлении неизвестны.
    <? endif; ?>
</div>
<hr/>
<div class="row">
    <div class="col-md-12 text-center sx-update-wrapper">
        <div class=" btn btn-primary btn-lg sx-btn-run">
            <?= \Yii::t('skeeks/marketplace', 'Start update'); ?>
        </div>
    </div>

    <div class="col-md-12">
        <label>Лог обновления</label>
        <pre class="sx-console sx-result-success">
            <?= $lastSuccessResult; ?>
        </pre>
        <label>Лог ошибок обновления</label>
        <pre class="sx-console sx-result-error">
            <?= $lastErrorResult; ?>
        </pre>
    </div>
    </div>
</div>

<div class="col-md-12">
    <p>
        Для обновления проекта вручную воспользуйтесь мануалом:
        <a href="https://docs.cms.skeeks.com/en/latest/overview.html#update" target="_blank">
            как обновить SkeekS CMS?
        </a>
    </p>
    <p>
        <a href="https://skeeks.com/contacts" target="_blank">Связь с разработчиками</a>.
    </p>
</div>
<?

$this->registerCss(<<<CSS
.sx-console {
    background: black;  
    color: white;
    max-height: 500px;
    height: auto;
}
CSS
);
$js = \yii\helpers\Json::encode([
    'backend' => \yii\helpers\Url::to(['update']),
    'backendStep' => \yii\helpers\Url::to(['step']),
    'isRunningUpdate' => $isRunningUpdate,
]);
$this->registerJs(<<<JS
(function(sx, $, _)
{
    sx.classes.CmsUpdate = sx.classes.Component.extend({
    
        _init: function()
        {
            this.stepTimeout = this.get('stepTimeout', 4000);
            
            this.bind('startUpdate', function(e, data) {
                  $('.sx-console').empty();
            });  
            
            this.bind('beforeStep', function(e, data) {
                  
            });  
        },
        
        runStep: function() {
            
            var self = this;
            
            this.trigger('beforeStep');
            
            _.delay(function() {
                
                var AjaxQuery = sx.ajax.preparePostQuery(self.get('backendStep'));
                
                new sx.classes.AjaxHandlerNoLoader(AjaxQuery);
                var Handler = new sx.classes.AjaxHandlerStandartRespose(AjaxQuery);
                
                Handler.bind('success', function(e, result) {
                    self.trigger('successStep', result);
                    
                    var data = result.data;

                    if (data.successContent) {
                        $('.sx-result-success').empty().append(data.successContent);
                    }
                    if (data.errorContent) {
                        $('.sx-result-error').empty().append(data.errorContent);
                    }
                    
                    if (data.stop === true) {
                        self.trigger('stopUpdate');
                    } else {
                        self.runStep();
                    }
                });
                
                Handler.bind('error', function() {
                    self.trigger('errorStep');
                    /*self.trigger('stopUpdate');*/
                    self.runStep();
                });
                
                AjaxQuery.execute();
                
            }, this.stepTimeout);  
            
        },
        
        _onDomReady: function()
        {
            var self = this;
            
            $(".sx-btn-run").on('click', function() {
                self.run();
                return false;
            });
            
            this.Blocker = new sx.classes.Blocker($('.sx-update-wrapper'));
            
            this.bind('startUpdate', function(e, data) {
                  self.Blocker.block();
            });
            
            this.bind('stopUpdate', function(e, data) {
                  self.Blocker.unblock();
            });
            
            if (this.get('isRunningUpdate')) {
                this.trigger('startUpdate');
                this.runStep();
            }
        },
        
        run: function()
        {
            var self = this;
            
            this.trigger('startUpdate');
            
            this.AjaxQuery = sx.ajax.preparePostQuery(this.get('backend'));
            
            new sx.classes.AjaxHandlerNoLoader(this.AjaxQuery);
            var Handler = new sx.classes.AjaxHandlerStandartRespose(this.AjaxQuery);
            
            Handler.bind('success', function(e, data) {
                self.runStep();
            });
            
            Handler.bind('error', function() {
                self.trigger('stopUpdate');
            });
            
            this.AjaxQuery.execute();
        }
    });
    
    sx.Updated = new sx.classes.CmsUpdate({$js});
})(sx, sx.$, sx._);

JS
);
?>




