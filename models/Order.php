<?php
namespace app\models;

use yii\db\ActiveRecord;

class Order extends ActiveRecord
{
    static $allStatuses = [
        'all' => 'All orders',
        '0' => 'Pending',
        '1' => 'In Progress',
        '2' => 'Completed',
        '3' => 'Canceled',
        '4' => 'Error'
    ];

    static $allModes = [
        'all' => 'All',
        '0' => 'Manual',
        '1' => 'Auto'
    ];

    public static function tableName()
    {
        return '{{orders}}';
    }

    public function getService()
    {
        return $this->hasOne(Service::className(), ['id' => 'service_id']);
    }
}