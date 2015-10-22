<?php
/**
 * Created by PhpStorm.
 * User: Nabeel
 * Date: 2015-10-15
 * Time: 3:55 PM
 */

namespace app\components;


use ShortCirquit\LinkoScopeApi\iLinkoScope;
use yii\base\Component;
use app\models\User;
use Yii;
use yii\web\HttpException;

class LinkoScope extends Component
{
    public $apiConfigFile;
    public $async = false;
    public $config;

    private $api = null;

    /**
     * @return iLinkoScope
     */
    public function getApi()
    {
        if ($this->api === null)
            $this->readConfig();

        if ($this->api === null)
            throw new HttpException(500, 'The site is not configured');

        return $this->api;
    }

    /**
     * @return iLinkoScope
     */
    public function getConsoleApi(){
        $config = json_decode(file_get_contents($this->apiConfigFile), true);
        $config['token'] = $config['adminToken'];
        if (isset($config['adminSecret']))
            $config['tokenSecret'] = $config['adminSecret'];
        $api = new $config['type']($config);
        return $api;
    }

    public function readConfig(){
        if (!file_exists($this->apiConfigFile))
            return null;

        $this->config = json_decode(file_get_contents($this->apiConfigFile), true);

        $cfg = $this->config;
        if ($this->async){
            $cfg['handler'] = new AsyncApiHandler();
        }

        if (isset(Yii::$app->user) && !Yii::$app->user->isGuest) {
            /** @var User $id */
            $id = Yii::$app->user->identity;
            $cfg['token'] = $id->token;
            $cfg['tokenSecret'] = $id->secret;
            $cfg['userId'] = $id->id;
        }

        $className = $cfg['type'];
        $this->api = new $className($cfg);
        return $this->api;
    }

    public function saveConfig()
    {
        file_put_contents($this->apiConfigFile, json_encode($this->config, JSON_PRETTY_PRINT));
    }
}
