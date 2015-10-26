<?php
/**
 * Created by PhpStorm.
 * User: Nabeel
 * Date: 2015-10-16
 * Time: 4:11 PM
 */

namespace app\components;

use yii\base\Component;
use Yii;
use app\models\Job;
use yii\log\Logger;

class LinkoScopeWorker extends Component
{

    public function Run()
    {
        while ($this->keepRunning())
        {
            $job = Job::find()->where('status IS NULL')->one();
            if ($job === null)
            {
                sleep(1);
                continue;
            }

            if (!$this->assignJob($job))
            {
                continue;
            }

            $this->runJob($job);
        }
    }

    private function keepRunning()
    {
        return true;
    }

    private function assignJob(Job $job)
    {
        $job->status = 'running';
        return $job->save();
    }

    private function runJob(Job $job)
    {
        $msg = "Running $job->id $job->type $job->arguments";
        Yii::getLogger()->log($msg, Logger::LEVEL_INFO);
        echo "$msg\n";
        try
        {
            switch ($job->type)
            {
                case 'refresh_link':
                    $this->updateLink($job);
                    break;
                case 'refresh_comment':
                    $this->updateComment($job);
                    break;
                default:
                    $this->failJob($job, "unknown job type: $job->type");
                    break;
            }
        }
        catch (\Exception $e)
        {
            $this->failJob($job, $e->getMessage());
        }

        $job->status = 'complete';
        $job->save();
    }

    private function updateLink(Job $job)
    {
        $api = $this->getLinkoScope()->getConsoleApi();
        $link = $api->getLink($job->args->id);
        $api->updateLink($link);
    }

    private function updateComment(Job $job)
    {
        $api = $this->getLinkoScope()->getConsoleApi();
        $link = $api->getComment($job->args->id);
        $api->updateComment($link);
    }

    private function failJob(Job $job, $msg)
    {
        $message = "Job with ID $job->id failed: $msg";
        echo "$message\n";
        Yii::getLogger()->log($message, Logger::LEVEL_ERROR);
        $job->status = 'failed';
        if (!$job->save())
        {
            Yii::getLogger()->log("Failed to save failure status in the job $job->id", Logger::LEVEL_ERROR);
        }
    }

    /**
     * @return LinkoScope
     */
    private function getLinkoScope()
    {
        return Yii::$app->linko;
    }
}
