<?php

namespace d3yii2\d3audittrail\widgets;

use cornernote\returnurl\ReturnUrl;
use eaBlankonThema\widget\ThButton;
use Yii;
use yii\base\Widget;

/**
 * Auditrail button
 */
class AuditTrailActionButton extends Widget
{

    public $modelId;

    public ?string $requestRuName = null;

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
                'audit-trail',
                'id' => $this->modelId,
                $this->requestRuName??'audittrail-ru' => ReturnUrl::getToken()
            ]
        ]);
    }

    public function afterRun($result)
    {
        ReturnUrl::$requestKey = $this->returnUrlRequestKey;
        return parent::afterRun($result);
    }
}
