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

    private function readConfig(){
        if (!file_exists($this->apiConfigFile))
            return null;

        $cfg = json_decode(file_get_contents($this->apiConfigFile), true);

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

    public function saveConfig(iLinkoScope $api)
    {
        $file = $this->apiConfigFile;
        $cfg = $api->getConfig();
        $cfg['type'] = get_class($api);
        file_put_contents($file, json_encode($cfg, JSON_PRETTY_PRINT));
    }
}
