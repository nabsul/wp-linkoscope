<?php
/**
 * Created by PhpStorm.
 * User: Nabeel
 * Date: 2015-10-15
 * Time: 3:58 PM
 */

namespace app\commands;

use app\models\Job;
use yii\console\Controller;

class WorkerController extends Controller
{
    public function actionStatus()
    {
        echo "Unexecuted jobs: " . Job::find()->where('status IS NULL')->count() . "\n";
        echo "Running    jobs: " . Job::find()->where('status = "running"')->count() . "\n";
        echo "Completed  jobs: " . Job::find()->where('status = "completed"')->count() . "\n";
    }
}
