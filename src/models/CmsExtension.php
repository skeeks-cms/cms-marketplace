<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 25.06.2015
 */

namespace skeeks\cms\marketplace\models;

use skeeks\cms\marketplace\helpers\ComposerHelper;
use skeeks\cms\marketplace\models\PackageModel;
use skeeks\cms\helpers\FileHelper;
use skeeks\cms\helpers\UrlHelper;
use yii\base\Component;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * @property string $packagistUrl
 * @property ComposerHelper $composer
 * @property string $controllUrl
 * @property UrlHelper $adminUrl
 * @property string $changeLog
 * @property string $readme
 *
 * Class CmsExtension
 * @package skeeks\cms\models
 */
class CmsExtension extends Model
{
    public static $extensions = [];
    public static $coreExtensions = [];

    public $name = '';
    public $version = '';
    /**
     * @var array
     */
    public $alias = [];


    /**
     * @var PackageModel
     */
    public $marketplacePackage = null;

    /**
     * @param $name
     * @return static
     */
    static public function getInstance($name)
    {
        $extension = ArrayHelper::getValue(self::$extensions, $name);

        if (!$extension || (!$extension instanceof static)) {
            $data = ArrayHelper::getValue(\Yii::$app->extensions, $name);
            if (!$data) {
                return null;
            }

            $extension = new static($data);
            self::$extensions[$name] = $extension;
        }

        return $extension;
    }


    /**
     * @return static[];
     */
    static public function fetchAll()
    {
        $result = [];

        if (\Yii::$app->extensions) {
            foreach (\Yii::$app->extensions as $name => $extensionData) {
                $result[$name] = static::getInstance($name);
            }
        }

        return $result;
    }

    /**
     * Получение всех установленных расширений с данными из SkeekS маркетплейс
     * @return static[];
     */
    static public function fetchAllWhithMarketplace()
    {
        $result = self::fetchAll();

        $packages = PackageModel::fetchInstalls();

        foreach ($result as $name => $extension) {
            if ($model = ArrayHelper::getValue($packages, $name)) {
                $extension->marketplacePackage = $model;
            }
        }

        return $result;
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'name' => \Yii::t('app', 'Name'),
            'version' => \Yii::t('app', 'Installed version'),
            'alias' => \Yii::t('app', 'Aliases'),
        ]);
    }


    /**
     * Путь к файлу в этом расширении
     *
     * @param $filePath
     * @return null|string
     */
    public function getFilePath($filePath)
    {
        $composerFiles = [];
        foreach ($this->alias as $name => $path) {
            $composerFiles[] = $path . '/' . $filePath;
        }

        return FileHelper::getFirstExistingFileArray($composerFiles);
    }

    /**
     * @return string
     */
    public function getControllUrl()
    {
        return UrlHelper::construct('/cms/admin-marketplace/install', [
            'packagistCode' => $this->name
        ])->enableAdmin()->toString();
    }

    /**
     * @return UrlHelper
     */
    public function getAdminUrl()
    {
        return UrlHelper::construct('/cms/admin-marketplace/catalog', ['code' => $this->name])
            ->enableAdmin();
    }

    /**
     * @return string
     */
    public function getPackagistUrl()
    {
        return 'https://packagist.org/packages/' . $this->name;
    }
}