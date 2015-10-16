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

class LinkoScope extends Component
{
    public $apiConfigFile;

    private $api = null;

    public function __construct($cfg = []){
        parent::__construct($cfg);
    }

    /**
     * @return iLinkoScope
     */
    public function getApi()
    {
        if ($this->api === null)
            $this->readConfig();

        return $this->api;
    }

    private function readConfig(){
        if (!file_exists($this->apiConfigFile))
            return null;

        $cfg = json_decode(file_get_contents($this->apiConfigFile), true);

        if (!Yii::$app->user->isGuest) {
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
