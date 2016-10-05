<?php

namespace d3yii2\d3audittrail\models;

use Yii;
use \d3yii2\d3audittrail\models\base\TblAuditTrail as BaseTblAuditTrail;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tbl_audit_trail".
 */
class TblAuditTrail extends BaseTblAuditTrail
{

public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # custom behaviors
            ]
        );
    }

    public function rules()
    {
        return ArrayHelper::merge(
             parent::rules(),
             [
                  # custom validation rules
             ]
        );
    }
}
