<?php
/**
 * Created by PhpStorm.
 * User: Nabeel
 * Date: 2015-10-19
 * Time: 5:00 PM
 */

namespace app\commands;


use Httpful\Request;
use ShortCirquit\LinkoScopeApi\Models\Link;
use ShortCirquit\LinkoScopeApi\iLinkoScope;
use yii\console\Controller;

class UtilController extends Controller
{
    public function actionClearAll(){
        echo "WARNING! This will delete EVERYTHING from your blog!\n";
        $input = readline("If you're sure, type yeS (yes, with a capital S): ");
        if ($input !== 'yeS')
            return;

        /** @var iLinkoScope $api */
        $api = \Yii::$app->linko->getConsoleApi();

        while(count($posts = $api->getLinks()->links) > 0){
            foreach ($posts as $post){
                echo "Deleting {$post->id}: {$post->title}\n";
                $api->deleteLink($post->id);
            }
        }
    }

    public function actionAddNews($count = 20)
    {
        /** @var iLinkoScope $api */
        $api = \Yii::$app->linko->getConsoleApi();
        $stories = Request::get('https://hacker-news.firebaseio.com/v0/newstories.json')->send()->body;
        $users = $api->getAccounts();
        $uCount = count($users);

        foreach ($stories as $id)
        {
            $story = Request::get("https://hacker-news.firebaseio.com/v0/item/$id.json")->send()->body;
            if (!isset($story->title, $story->url))
                continue;
            echo json_encode($story) . "\n";
            $link = new Link([
                'title' => $story->title,
                'url' => $story->url,
                'authorId' => $users[$count % $uCount]->id
            ]);
            echo json_encode($link) . "\n";
            $api->addLink($link);
            echo "Saved {$story->title}: {$story->url}\n";

            if (--$count <= 0)
                break;
        }
    }

    public function actionAccounts()
    {
        /** @var iLinkoScope $api */
        $api = \Yii::$app->linko->getConsoleApi();
        foreach ($api->getAccounts() as $u)
        {
            echo json_encode($u) . "\n";
        }
    }
}
