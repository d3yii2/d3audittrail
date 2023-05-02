<?php

namespace d3yii2\d3audittrail\models;

use d3system\exceptions\D3ActiveRecordException;
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

    public static function findNameById(int $id, bool $secondCall = false): string
    {
        if (!$id) {
            return '-';
        }
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
        if (isset(self::$listIdToName[$id])) {
            return self::$listIdToName[$id];
        }
        if ($secondCall) {
            return '';
        }
        Yii::$app->cache->delete('AuditTraiModelNamesList3');
        return self::findNameById($secondCall, true);
    }

    public static function findIdByName(string $name, bool $secondCall = false): int
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
        if (isset(self::$listNameToId[$name])) {
            return self::$listNameToId[$name];
        }
        if ($secondCall) {
            return 0;
        }
        $model = new AuditTraiNames();
        $model->name = $name;
        if (!$model->save()) {
            throw new D3ActiveRecordException($model);
        }
        self::$listNameToId[$name] = $model->id;
        Yii::$app->cache->set('AuditTraiModelIdList3',self::$listNameToId, 60 * 60);
        return self::$listNameToId[$name];
    }
}
