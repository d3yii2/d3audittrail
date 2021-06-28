<?php

namespace d3yii2\d3audittrail\controllers;

use d3yii2\d3audittrail\Module;
use eaBlankonThema\yii2\web\LayoutController;
use d3yii2\d3audittrail\models\TblAuditTrail;
use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\filters\AccessControl;

/**
 * Class DataController
 * @package d3yii2\d3audittrail\controllers
 * @property  Module $module
 */
class DataController extends LayoutController
{

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {

        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['list'],
                        'roles' => [$this->module->viewRole],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param string $modelName
     * @param int $modelId
     * @return string
     * @throws Exception
     * @throws \yii\db\Exception
     */
    public function actionList(string $modelName, int $modelId): string
    {
        if(!$modelName::findOne($modelId)){
            throw new Exception('No access!');
        }

        /**
         * base model
         */
        $modelsNames = [
            [
                'model_name' => $modelName,
                'model_id' => $modelId,
                'hidded_fields' => method_exists($modelName,
                    'audittrailHiddedFields') ? $modelName::audittrailHiddedFields()
                    : [],
                'field_sql' => method_exists($modelName,
                    'audittrailSqlFields') ? $modelName::audittrailSqlFields()
                    : [],
            ],
        ];


        if (method_exists($modelName, 'audittrailRefModels')) {
            $refModels = $modelName::audittrailRefModels();

            foreach ($refModels as $rModel) {
                $relRecords    = TblAuditTrail::find()
                    ->select('`tbl_audit_trail`.`model_id`')
                    ->where([
                        'model' => $rModel['model'],
                        'field' => $rModel['ref_field'],
                        'action' => TblAuditTrail::ACTION_SET,
                        'new_value' => $modelId
                    ])
                    ->orderBy('stamp')
                    ->asArray()
                    ->all();

                $hidded_fields = $rModel['hidded_fields'] ?? [];

                foreach ($relRecords as $rr) {
                    if(!$rModel['model']::findOne($rr['model_id'])){
                        continue;
                    }
                    $modelsNames[] = [
                        'model_name' => $rModel['model'],
                        'model_id' => $rr['model_id'],
                        'hidded_fields' => $hidded_fields,
                        'field_sql' => $rModel['field_sql']??false,
                    ];
                }
            }
        }

        $data = [];
        foreach ($modelsNames as $m) {
            $mName                 = $m['model_name'];
            $mId                   = $m['model_id'];
            /**
             * @todo add validation to acces to record
             */
            $data[$mName]['label'] = $mName;

            /** @var ActiveRecord $mObject */
            $mObject = new $mName();

            $data[$mName]['attribute_labels'] = $mObject->attributeLabels();
            if (method_exists($mName, 'tableLabel')) {
                $data[$mName]['label'] = $mObject->tableLabel();
            }

            $data[$mName]['table'] = TblAuditTrail::find()
                ->select([
                    '`tbl_audit_trail`.*',
                    'user.username',
                    'd3p_person.first_name',
                    'd3p_person.last_name',
                ])
                ->leftJoin('user', 'tbl_audit_trail.user_id = user.id')
                ->leftJoin('d3p_person', 'tbl_audit_trail.user_id = d3p_person.user_id')
                ->where([
                    'model' => $mName,
                    'model_id' => $mId,
                    //
                ])
                ->andWhere(['not in', 'field', $m['hidded_fields']])
                ->orderBy('stamp')
                ->asArray()
                ->all();

            if (isset($m['field_sql']) && $m['field_sql']) {
                $connection  = Yii::$app->getDb();
                $fieldValues = [];
                foreach ($data[$mName]['table'] as $k => $row) {
                    if (!isset($m['field_sql'][$row['field']])) {
                        continue;
                    }
                    if (!isset($fieldValues[$row['field']][$row['old_value']])) {
                        if (!$row['old_value']) {
                            $fieldValues[$row['field']][$row['old_value']] = $row['old_value'];
                        } else {
                            $command                                       = $connection->createCommand($m['field_sql'][$row['field']],
                                [':id' => $row['old_value']]);
                            $field_value                                   = $command->queryScalar();
                            $fieldValues[$row['field']][$row['old_value']] = $field_value ?: $row['old_value'];
                        }
                    }
                    if (!isset($fieldValues[$row['field']][$row['new_value']])) {
                        if (!$row['new_value']) {
                            $fieldValues[$row['field']][$row['new_value']] = $row['new_value'];
                        } else {

                            $command                                       = $connection->createCommand($m['field_sql'][$row['field']],
                                [':id' => $row['new_value']]);
                            $field_value                                   = $command->queryScalar();
                            $fieldValues[$row['field']][$row['new_value']] = $field_value ?: $row['new_value'];
                        }
                    }

                    $data[$mName]['table'][$k]['old_value'] = $fieldValues[$row['field']][$row['old_value']];
                    $data[$mName]['table'][$k]['new_value'] = $fieldValues[$row['field']][$row['new_value']];
                }
            }
        }
        
        return $this->render('list', ['data' => $data]);
    }
}