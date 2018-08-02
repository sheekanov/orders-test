<?php
namespace app\models;

use yii\db\ActiveRecord;

class Service extends ActiveRecord
{
    public static function tableName()
    {
        return '{{services}}';
    }

    public function getOrders()
    {
        return $this->hasMany(Order::className(), ['service_id' => 'id']);
    }
}