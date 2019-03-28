
[![Total Downloads](https://poser.pugx.org/d3yii2/d3audittrail/downloads)](https://packagist.org/packages/d3yii2/d3audittrail)


Display audittrail data
=====

Add migrations paths
-----
```php
    'controllerMap' => [
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationPath' => [
                '@vendor/sammaye/yii2-audittrail/migrations',
                '@vendor/d3yii2/d3audittrail/migrations',

            ],
        ],
    ],
``` 

Define module in console and web
----

```php
    'modules' => [

        'd3audittrail' => [
            'class' => 'd3yii2\d3audittrail\Module',
        ],
    ]    
```

define in parameters table name
--------------
```php
    'params' => [
        'audittrail.table' => 'tbl_audit_trail'
    ]    
```

Add to model behaviors
--------
```php

    public function behaviors(): array
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'sammaye\audittrail\LoggableBehavior'
            ]
        );
    }
```

Define model label
--------
```php

    public function tableLabel(): string
    {
        return 'Reģistra ieraksts';
    }
```    

Add to view button
----------------

```php
    $this->addPageButtons(ThButton::widget([
        'label' => 'Auditpieraksti',
        'link' => [
            '/d3audittrail/data/list',
            'modelName' => \tran\permit\models\ClpTransaction::class,
            'modelId' => $model->id
        ],
        'type' => ThButton::TYPE_SUCCESS,
    ]));
```