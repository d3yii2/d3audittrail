<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace d3yii2\d3audittrail\models\base;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the base-model class for table "tbl_audit_trail".
 *
 * @property integer $id
 * @property string $old_value
 * @property string $new_value
 * @property string $action
 * @property string $model_name_id
 * @property string $field_name_id
 * @property string $stamp
 * @property integer $user_id
 * @property integer $model_id
 * @property-read string $modelLabel
 * @property-read string $fieldLabel
 * @property string $aliasModel
 */
abstract class TblAuditTrail extends ActiveRecord
{



    /**
    * ENUM field values
    */
    public const ACTION_CHANGE = 'CHANGE';
    public const ACTION_CREATE = 'CREATE';
    public const ACTION_DELETE = 'DELETE';
    public const ACTION_SET = 'SET';

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return 'tbl_audit_trail';
    }


    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['old_value', 'new_value', 'action'], 'string'],
            [['action', 'model_name_id', 'stamp', 'model_id'], 'required'],
            [['stamp'], 'safe'],
            [['model_name_id','field_name_id','user_id','model_id'], 'integer'],
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
    public function attributeLabels(): array
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
    public static function getActionValueLabel(string $value): string
    {
        $labels = self::optsAction();
        return $labels[$value] ?? $value;
    }

    /**
     * column action ENUM value labels
     * @return array
     */
    public static function optsAction(): array
    {
        return [
            self::ACTION_CHANGE => Yii::t('d3audittrail', self::ACTION_CHANGE),
            self::ACTION_CREATE => Yii::t('d3audittrail', self::ACTION_CREATE),
            self::ACTION_DELETE => Yii::t('d3audittrail', self::ACTION_DELETE),
            self::ACTION_SET => Yii::t('d3audittrail', self::ACTION_SET),
        ];
    }
}
