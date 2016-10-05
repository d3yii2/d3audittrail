<?php

namespace d3yii2\d3audittrail\controllers;

use yii\web\Controller;
use ea\app\controllers\LayoutController;
use d3yii2\d3audittrail\models\TblAuditTrail;
use yii\filters\AccessControl;

class DataController extends LayoutController
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['list'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionList($modelName, $modelId)
    {
        if(!$modelName::findOne($modelId)){
            throw new exception('No access!');
        }

        $modelsNames   = [];
        $modelsNames[] = [
            'model_name' => $modelName,
            'model_id' => $modelId,
            'hidded_fields' => method_exists($modelName,
                'audittrailHiddedFields') ? $modelName::audittrailHiddedFields()
                    : [],
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

                $hidded_fields = isset($rModel['hidded_fields']) ? $rModel['hidded_fields']
                        : [];

                foreach ($relRecords as $rr) {
                    if(!$rModel['model']::findOne($rr['model_id'])){
                        continue;
                    }
                    $modelsNames[] = [
                        'model_name' => $rModel['model'],
                        'model_id' => $rr['model_id'],
                        'hidded_fields' => $hidded_fields,
                        'field_sql' => isset($rModel['field_sql'])?$rModel['field_sql']:false,
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

            $mObject = new $mName();

            $data[$mName]['attribute_labels'] = $mObject->attributeLabels();
            if (method_exists($mName, 'tableLabel')) {
                $data[$mName]['label'] = $mObject->tableLabel();
            }

            $data[$mName]['table'] = TblAuditTrail::find()
                ->select('`tbl_audit_trail`.*, user.username')
                ->leftJoin('user', 'tbl_audit_trail.user_id = user.id')
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
                $connection  = \Yii::$app->getDb();
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
                            $fieldValues[$row['field']][$row['old_value']] = $field_value
                                    ? $field_value : $row['old_value'];
                        }
                    }
                    if (!isset($fieldValues[$row['field']][$row['new_value']])) {
                        if (!$row['new_value']) {
                            $fieldValues[$row['field']][$row['new_value']] = $row['new_value'];
                        } else {

                            $command                                       = $connection->createCommand($m['field_sql'][$row['field']],
                                [':id' => $row['new_value']]);
                            $field_value                                   = $command->queryScalar();
                            $fieldValues[$row['field']][$row['new_value']] = $field_value
                                    ? $field_value : $row['new_value'];
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