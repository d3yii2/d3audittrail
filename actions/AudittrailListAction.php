<?php


namespace d3yii2\d3audittrail\actions;

use d3yii2\d3audittrail\models\TblAuditTrail;
use sammaye\audittrail\LoggableBehavior;
use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\base\Action;

class AudittrailListAction extends Action
{
    /** @var string|string[] */
    public $modelName;

    /** @var string[] */
    public array $modelAliasNames = [];

    /**
     * @param int $id table record id
     * @param int|null $modelId id from tbl_audit_trail_names
     * @return string
     * @throws \yii\base\Exception
     * @throws \yii\httpclient\Exception
     */
    public function run(int $id, int $modelId = null): string
    {
        if ($modelId) {
            if (!$modelIdName = TblAuditTrail::findNameById($modelId)) {
                Yii::error('Requested illegal modelId: ' . $modelId);
                throw new \yii\httpclient\Exception('Illegal request');
            }
            if ($modelIdName !== $this->modelName && !is_subclass_of($modelIdName, $this->modelName)) {
                Yii::error('Requested illegal modelId: ' . $modelId . ' modelName: ' . $modelIdName);
                throw new \yii\httpclient\Exception('Illegal request');
            }
            $this->modelName = $modelIdName;
        }
        /**
         * validate record id access by controller method findModel() or simple findOne()
         */
        if (method_exists($this->controller, 'findModel')) {
            $this->controller->findModel($id);
        } elseif (!$this->modelName::findOne($id)) {
            throw new Exception('No access!');
        }

        $modelsNames = [
            [
                'model_name' => $this->modelName,
                'model_alias_names' => $this->modelAliasNames,
                'model_id' => $id,
                'hidded_fields' => method_exists($this->modelName,
                    'audittrailHiddedFields') ? $this->modelName::audittrailHiddedFields()
                    : [],
                'field_sql' => method_exists($this->modelName,
                    'audittrailSqlFields') ? $this->modelName::audittrailSqlFields()
                    : [],
            ],
        ];


        if (method_exists($this->modelName, 'audittrailRefModels')) {
            $refModels = $this->modelName::audittrailRefModels();

            foreach ($refModels as $rModel) {
                $relRecords = TblAuditTrail::find()
                    ->select('`tbl_audit_trail`.`model_id`')
                    ->where([
                        'model_name_id' => LoggableBehavior::findIdByName($rModel['model']),
                        'field_name_id' => LoggableBehavior::findIdByName($rModel['ref_field']),
                        'action' => TblAuditTrail::ACTION_SET,
                        'new_value' => $id
                    ])
                    ->orderBy('stamp')
                    ->asArray()
                    ->all();

                $hidded_fields = $rModel['hidded_fields'] ?? [];

                foreach ($relRecords as $rr) {
                    if (!$rModel['model']::findOne($rr['model_id'])) {
                        continue;
                    }
                    $modelsNames[] = [
                        'model_name' => $rModel['model'],
                        'model_id' => $rr['model_id'],
                        'hidded_fields' => $hidded_fields,
                        'field_sql' => $rModel['field_sql'] ?? false,
                    ];
                }
            }
        }
        $connection = Yii::$app->getDb();
        $data = [];
        foreach ($modelsNames as $m) {
            $mName = $m['model_name'];
            $mNameList = [LoggableBehavior::findIdByName($mName)];

            foreach($m['model_alias_names']??[] as $modelAliasName){
                $mNameList[] = LoggableBehavior::findIdByName($modelAliasName);
            }
            $mId = $m['model_id'];
            /**
             * @todo add validation to access to record
             */
            $data[$mName]['label'] = $mName;

            /** @var ActiveRecord $mObject */
            $mObject = new $mName();

            $data[$mName]['attribute_labels'] = $mObject->attributeLabels();
            if (method_exists($mName, 'tableLabel')) {
                $data[$mName]['label'] = $mObject->tableLabel();
            }
            $hiddedFields = [];
            foreach ($m['hidded_fields'] as $hf) {
                $hiddedFields[] = LoggableBehavior::findIdByName($hf);
            }
            $data[$mName]['table'] = TblAuditTrail::find()
                ->select([
                    '`tbl_audit_trail`.*',
                    'user.username',
                    'd3p_person.first_name',
                    'd3p_person.last_name',
                ])
                ->leftJoin(
                    'user',
                    'tbl_audit_trail.user_id = user.id'
                )
                ->leftJoin(
                    'd3p_person',
                    'tbl_audit_trail.user_id = d3p_person.user_id'
                )
                ->where([
                    'model_name_id' => $mNameList,
                    'model_id' => $mId,
                    //
                ])
                ->andWhere(['not in', 'field_name_id', $hiddedFields])
                ->orderBy('stamp')
                ->asArray()
                ->all();
            foreach ($data[$mName]['table'] as $k => $row) {
                $data[$mName]['table'][$k]['field'] = TblAuditTrail::findNameById($row['field_name_id']);
                $data[$mName]['table'][$k]['model'] = TblAuditTrail::findNameById($row['model_name_id']);
            }
            if (isset($m['field_sql']) && $m['field_sql']) {
                $fieldValues = [];
                foreach ($data[$mName]['table'] as $k => $row) {
                    $row['field'] = TblAuditTrail::findNameById($row['field_name_id']);
                    $row['model'] = TblAuditTrail::findNameById($row['model_name_id']);
                    if (!isset($m['field_sql'][$row['field']])) {
                        continue;
                    }
                    if (!isset($fieldValues[$row['field']][$row['old_value']])) {
                        if (!$row['old_value']) {
                            $fieldValues[$row['field']][$row['old_value']] = $row['old_value'];
                        } else {
                            $command = $connection->createCommand(
                                $m['field_sql'][$row['field']],
                                [':id' => $row['old_value']]
                            );
                            $field_value = $command->queryScalar();
                            $fieldValues[$row['field']][$row['old_value']] = $field_value ?: $row['old_value'];
                        }
                    }
                    if (!isset($fieldValues[$row['field']][$row['new_value']])) {
                        if (!$row['new_value']) {
                            $fieldValues[$row['field']][$row['new_value']] = $row['new_value'];
                        } else {
                            $command = $connection->createCommand(
                                $m['field_sql'][$row['field']],
                                [':id' => $row['new_value']]
                            );
                            $field_value = $command->queryScalar();
                            $fieldValues[$row['field']][$row['new_value']] = $field_value ?: $row['new_value'];
                        }
                    }
                    $data[$mName]['table'][$k]['old_value'] = $fieldValues[$row['field']][$row['old_value']];
                    $data[$mName]['table'][$k]['new_value'] = $fieldValues[$row['field']][$row['new_value']];
                }
            }
        }
        return $this->controller->render(
            '@vendor/d3yii2/d3audittrail/views/data/list',
            [
                'data' => $data
            ]
        );
    }
}
