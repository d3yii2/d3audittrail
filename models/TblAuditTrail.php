<?php

namespace d3yii2\d3audittrail\models;

use d3yii2\d3audittrail\models\base\TblAuditTrail as BaseTblAuditTrail;
use sammaye\audittrail\AuditTraiNames;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tbl_audit_trail".
 */
class TblAuditTrail extends BaseTblAuditTrail
{
    /** @var string[] */
    private static array $listIdToName = [];

    /** @var int[] */
    private static array $listNameToId = [];
    public function getModelLabel(): string
    {
        return self::findNameById($this->model_name_id);
    }

    public function getFieldLabel(): string
    {
        return self::findNameById($this->field_name_id);
    }

    public static function findNameById(int $id): string
    {
        if (!self::$listIdToName) {
            self::$listIdToName = Yii::$app->cache->getOrSet(
                'AuditTraiModelNamesList3',
                static function () {
                    return ArrayHelper::map(
                        AuditTraiNames::find()->asArray()->all(),
                        'id',
                        'name'
                    );
                },
                60 * 60
            );
        }
        return self::$listIdToName[$id]??'??';
    }

    public static function findIdByName(string $name): int
    {
        if (!self::$listNameToId) {
            self::$listNameToId = Yii::$app->cache->getOrSet(
                'AuditTraiModelIdList3',
                static function () {
                    return ArrayHelper::map(
                        AuditTraiNames::find()->asArray()->all(),
                        'name',
                        'id'
                    );
                },
                60 * 60
            );
        }
        return self::$listNameToId[$name]??'0';
    }
}
