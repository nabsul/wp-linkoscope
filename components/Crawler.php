<?php
/**
 * Created by PhpStorm.
 * User: Nabeel
 * Date: 2015-10-16
 * Time: 6:42 PM
 */

namespace app\components;


use yii\base\Component;
use yii\log\Logger;

class Crawler extends Component
{
    public $enabled = true;

    public function readTitle($url)
    {
        if (!$this->enabled)
            return null;

        try
        {
            $content = $this->fetchUrl($url);
        }
        catch (\Exception $e)
        {
            \Yii::getLogger()->log("Crawler fetchUrl exception: {$e->getMessage()}", Logger::LEVEL_ERROR);
            return null;
        }

        try
        {
            $crawler = new \Symfony\Component\DomCrawler\Crawler();
            $crawler->addHtmlContent($content);
            $node = $crawler->filterXPath('html/head/title');
            if ($node->count() > 0)
                return $node->first()->text();
        }
        catch (\Exception $e)
        {
            \Yii::getLogger()->log("Crawler DOM extraction exception: {$e->getMessage()}", Logger::LEVEL_ERROR);
        }
        return null;
    }

    private function fetchUrl($url){
        $curlOptions = [
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ];
        $curl = curl_init($url);

        foreach ($curlOptions as $option => $value) {
            curl_setopt($curl, $option, $value);
        }

        $response = curl_exec($curl);
        return $response;
    }
}
