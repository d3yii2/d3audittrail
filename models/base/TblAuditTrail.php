<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace d3yii2\d3audittrail\models\base;

use Yii;

/**
 * This is the base-model class for table "tbl_audit_trail".
 *
 * @property integer $id
 * @property string $old_value
 * @property string $new_value
 * @property string $action
 * @property string $model
 * @property string $field
 * @property string $stamp
 * @property string $user_id
 * @property string $model_id
 * @property string $aliasModel
 */
abstract class TblAuditTrail extends \yii\db\ActiveRecord
{



    /**
    * ENUM field values
    */
    const ACTION_CHANGE = 'CHANGE';
    const ACTION_CREATE = 'CREATE';
    const ACTION_DELETE = 'DELETE';
    const ACTION_SET = 'SET';
    var $enum_labels = false;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_audit_trail';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['old_value', 'new_value', 'action'], 'string'],
            [['action', 'model', 'stamp', 'model_id'], 'required'],
            [['stamp'], 'safe'],
            [['model', 'field', 'user_id', 'model_id'], 'string', 'max' => 255],
            ['action', 'in', 'range' => [
                    self::ACTION_CHANGE,
                    self::ACTION_CREATE,
                    self::ACTION_DELETE,
                    self::ACTION_SET,
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('d3audittrail', 'ID'),
            'old_value' => Yii::t('d3audittrail', 'Old Value'),
            'new_value' => Yii::t('d3audittrail', 'New Value'),
            'action' => Yii::t('d3audittrail', 'Action'),
            'model' => Yii::t('d3audittrail', 'Model'),
            'field' => Yii::t('d3audittrail', 'Field'),
            'stamp' => Yii::t('d3audittrail', 'Stamp'),
            'user_id' => Yii::t('d3audittrail', 'User ID'),
            'model_id' => Yii::t('d3audittrail', 'Model ID'),
        ];
    }




    /**
     * get column action enum value label
     * @param string $value
     * @return string
     */
    public static function getActionValueLabel($value){
        $labels = self::optsAction();
        if(isset($labels[$value])){
            return $labels[$value];
        }
        return $value;
    }

    /**
     * column action ENUM value labels
     * @return array
     */
    public static function optsAction()
    {
        return [
            self::ACTION_CHANGE => Yii::t('d3audittrail', self::ACTION_CHANGE),
            self::ACTION_CREATE => Yii::t('d3audittrail', self::ACTION_CREATE),
            self::ACTION_DELETE => Yii::t('d3audittrail', self::ACTION_DELETE),
            self::ACTION_SET => Yii::t('d3audittrail', self::ACTION_SET),
        ];
    }

}
