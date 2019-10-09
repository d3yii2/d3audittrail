<?php


namespace d3yii2\d3audittrail\widgets;


use cornernote\returnurl\ReturnUrl;
use eaBlankonThema\widget\ThButton;
use Yii;
use yii\base\Widget;

class AuditTrailButton extends Widget
{
    public $modelName;
    public $modelId;

    private $returnUrlRequestKey;

    public function init()
    {
        $this->returnUrlRequestKey = ReturnUrl::$requestKey;
        ReturnUrl::$requestKey = 'audittrail-ru';
        parent::init();

    }

    public function run()
    {
        parent::run();
        return ThButton::widget([
            'label' => Yii::t('d3audittrail', 'Audit trail'),
            'type' => ThButton::TYPE_SUCCESS,
            'link' => [
                '/d3audittrail/data/list',
                'modelName' => $this->modelName,
                'modelId' => $this->modelId,
                'audittrail-ru' => ReturnUrl::getToken()
            ]
        ]);

    }

    public function afterRun($result)
    {
        ReturnUrl::$requestKey = $this->returnUrlRequestKey;
        return parent::afterRun($result);
    }
}