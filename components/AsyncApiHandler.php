<?php
/**
 * Created by PhpStorm.
 * User: Nabeel
 * Date: 2015-10-20
 * Time: 8:28 PM
 */

namespace app\components;


use app\models\Job;
use ShortCirquit\LinkoScopeApi\iApiHandler;

class AsyncApiHandler implements iApiHandler
{
    function refreshLink($id)
    {
        $job = new Job();
        $job->type = 'refresh_link';
        $job->arguments = json_encode(['id' => $id]);
        $job->save();
    }

    function refreshComment($id)
    {
        $job = new Job();
        $job->type = 'refresh_comment';
        $job->arguments = json_encode(['id' => $id]);
        $job->save();
    }
}

