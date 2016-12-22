<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 15.04.2016
 */
namespace skeeks\cms\marketplace;

use yii\base\Component;
use yii\web\Application;
use Yii;

use skeeks\yii2\curl\Curl;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * @property string $url;
 * @property string $baseUrl;
 *
 * Class CmsMarketplaceComponent
 * @package skeeks\cms\agent
 */
class CmsMarketplaceComponent extends Component
{
    const RESPONSE_FORMAT_JSON = 'json';

    public $schema          = "http";
    public $host            = "api.cms.skeeks.com";
    public $version         = "v1";

    public $responseFormat  = self::RESPONSE_FORMAT_JSON;

    /**
     * Базовый путь к апи, без версии
     *
     * Пример http://api.cms.skeeks.com/v1/
     *
     * @return string
     */
    public function getUrl()
    {
        if ($this->version)
        {
            return $this->baseUrl . $this->version . "/";
        }

        return $this->baseUrl;
    }

    /**
     * Базовый путь к апи, с версией
     *
     * Пример http://cms.skeeks.com/
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->schema . "://" . $this->host . "/";
    }

    /**
     * @param $route
     * @return string
     */
    public function getRequestUrl($route)
    {
        $data = [];
        $url = $this->url;

        if (is_string($route))
        {
            $url = $this->url . $route;
        } else if (is_array($route))
        {
            $route = $route[0];
            if (isset($route[1]))
            {
                $data = $route[1];
            }
            $url = $this->url . $route;

            if (!$data || !is_array($data))
            {
                $data = [];
            }

            $data = array_merge($data, [
                'sx-serverName' => \Yii::$app->request ? \Yii::$app->request->serverName : "",
                'sx-version'    => (\Yii::$app->cms && \Yii::$app->cms->descriptor) ? \Yii::$app->cms->descriptor->version : "",
                'sx-email'      => (\Yii::$app->cms && \Yii::$app->cms->adminEmail) ? \Yii::$app->cms->adminEmail : "",
            ]);

            if ($data)
            {
                $url .= '?' . http_build_query($data);
            }
        }

        return $url;
    }

    /**
     * @param string $method
     * @param string|array $route
     * @return Curl
     * @throws \yii\base\Exception
     */
    public function request($method, $route)
    {
        $curl = new Curl();

        $curl->setOption(CURLOPT_HTTPHEADER, [
            'Accept: application/' . $this->responseFormat. '; q=1.0, */*; q=0.1'
        ]);

        $curl->setOption(CURLOPT_TIMEOUT, 2);

        try
        {
            $url = $this->getRequestUrl($route);
            /*\Yii::info("Api request: " . $method . ": " . $url, self::className());*/
            $curl->httpRequest($method, $url);
        } catch (\Exception $e)
        {
            \Yii::error($e->getMessage(), self::className());
        }


        return $curl;
    }


    /**
     * @param $route
     * @return array
     */
    public function fetch($route)
    {
        $curl = $this->request(Curl::METHOD_GET, $route);

        if ($curl->responseCode == 200 && $curl->response)
        {
            return Json::decode($curl->response);
        }

        return [];
    }

    /**
     * @return array
     */
    public function getInfo()
    {
        $key = 'sx-cms-info';

        $result = \Yii::$app->cache->get($key);
        if ($result === false)
        {
            $result = $this->fetch(['info']);
            \Yii::$app->cache->set($key, $result, (60*60*6) );
        }

        return $result;
    }
}