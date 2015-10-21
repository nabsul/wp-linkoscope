<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "jobs".
 *
 * @property integer $id
 * @property string $date
 * @property string $started
 * @property string $completed
 * @property string $status
 * @property string $type
 * @property string $arguments
 */
class Job extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jobs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date', 'started', 'completed'], 'safe'],
            [['arguments'], 'string'],
            [['status', 'type'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => 'Date',
            'started' => 'Started',
            'completed' => 'Completed',
            'status' => 'Status',
            'type' => 'Type',
            'arguments' => 'Arguments',
        ];
    }

    public function getArgs()
    {
        return json_decode($this->arguments);
    }
}
