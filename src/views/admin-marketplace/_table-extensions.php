<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 29.06.2015
 */
/* @var $this yii\web\View */
/* @var $models \skeeks\cms\models\CmsExtension[] */

/* @var $message string */

use \skeeks\cms\marketplace\models\PackageModel;
use \skeeks\cms\marketplace\models\CmsExtension;

$self = $this;
?>

<? if ($message) : ?>
    <?
    \yii\bootstrap\Alert::begin([
        'options' => [
            'class' => 'alert-info',
        ]
    ]);
    ?>
    <?= $message; ?>
    <? \yii\bootstrap\Alert::end(); ?>

<? endif; ?>
<? if ($models) : ?>
    <?= \skeeks\cms\modules\admin\widgets\GridView::widget([
        'dataProvider' => (new \yii\data\ArrayDataProvider([
            'allModels' => $models,
            'pagination' => [
                'defaultPageSize' => 200
            ]
        ])),
        //'layout' => "{summary}\n{items}\n{pager}",
        'columns' =>
            [
                [
                    'class' => \yii\grid\DataColumn::className(),
                    'value' => function (CmsExtension $model) use ($self) {
                        return $self->render('_image-column', [
                            'model' => $model
                        ]);

                    },
                    'format' => 'raw'
                ],

                [
                    'class' => \yii\grid\DataColumn::className(),
                    'value' => function (CmsExtension $model) use ($self) {
                        return $model->version;
                    },

                    'format' => 'raw',
                    'attribute' => 'version'
                ],
            ]
    ]) ?>
<? endif; ?>
