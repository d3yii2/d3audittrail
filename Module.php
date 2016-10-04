<?php

namespace d3yii2\d3audittrail;

use dmstr\web\traits\AccessBehaviorTrait;

class Module extends \yii\base\Module
{
    use AccessBehaviorTrait;

    public $controllerNamespace = 'd3yii2\d3audittrail\controllers';

    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
