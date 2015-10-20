<?php
/**
 * Created by PhpStorm.
 * User: Nabeel
 * Date: 2015-10-19
 * Time: 5:00 PM
 */

namespace app\commands;


use ShortCirquit\LinkoScopeApi\OrgLinkoScope;
use yii\console\Controller;

class UtilController extends Controller
{

    public function actionClearAll(){
        echo "WARNING! This will delete EVERYTHING from your blog!\n";
        $input = readline("If you're sure, type yeS (yes, with a capital S): ");
        if ($input !== 'yeS')
            return;

        $config = json_decode(file_get_contents(\Yii::$app->runtimePath . '/api.cfg'), true);
        $config['token'] = $config['adminToken'];
        if (isset($config['adminSecret']))
            $config['tokenSecret'] = $config['adminSecret'];
        $api = new $config['type']($config);

        while(count($posts = $api->getLinks()) > 0){
            foreach ($posts as $post){
                echo "Deleting {$post->id}: {$post->title}\n";
                $api->deleteLink($post->id);
            }
        }
    }
}
