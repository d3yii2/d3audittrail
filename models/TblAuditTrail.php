<?php

namespace d3yii2\d3audittrail\models;

use d3yii2\d3audittrail\models\base\TblAuditTrail as BaseTblAuditTrail;
use DateTime;
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

    /**
     * search field actual value for time
     * @param int $modelNameId
     * @param int $fieldNameId
     * @param int $modelId
     * @param DateTime $time
     * @return string|null
     */
    public static function findValueForDate(
        int $modelNameId,
        int $fieldNameId,
        int $modelId,
        DateTime $time
    ): ?string {
        return
            self::find()
                ->select('new_value')
                ->where([
                    'model_name_id' => $modelNameId,
                    'field_name_id' => $fieldNameId,
                    'model_id' => $modelId,
                ])
                ->andWhere(
                    'stamp < :date ',
                    [':date' => $time->format('Y-m-d H:i:s')]
                )
                ->orderBy([
                    'stamp' => SORT_DESC,
                ])
                ->scalar();
    }
}
