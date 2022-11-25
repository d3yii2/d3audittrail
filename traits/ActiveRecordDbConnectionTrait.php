<?php

namespace d3yii2\d3audittrail\traits;

use Yii;

trait ActiveRecordDbConnectionTrait
{
    public static function getDb()
    {
        return Yii::$app->db;
    }
}
