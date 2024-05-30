
[![Yii2](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](https://www.yiiframework.com/)
[![Total Downloads](https://poser.pugx.org/d3yii2/d3audittrail/downloads)](https://packagist.org/packages/d3yii2/d3audittrail)

Add to composer require
```
      "d3yii2/d3audittrail": "dev-master"
```

Display audittrail data
=====

Add migrations paths
-----
```php
    'controllerMap' => [
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationPath' => [
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
class Model extends extends \yii\db\ActiveRecord
    /**
    * model label 
    */
    public function tableLabel(): string
    {
        return 'Data Record';
    }

    public static function audittrailHiddedFields()
    {
        return [
            'password'
        ];
    }

    public static function audittrailSqlFields()
    {
        return [
            'client_id' => 'select name from client where id=:id'
        ];
    }

    public static function audittrailRefModels()
    {
        return [
            [
                'model' => TblAuditTrail::class,
                'ref_field' => 'my_id',
                'hidded_fields' => ['a','b'],
                'field_sql' => [
                    'field1' => 'select name from user where id=:id'
                 ]               
            ]           
        ];
    }
}
```    

Add to view button to audittrail controller (unsecure)
----------------
```php
    $this->addPageButtons(AuditTrailButton::widget([
                'modelName' => coalmar\delivery\models\CmdDelivery::className(),
                'modelId' => $model->id
    ]));
```

Add to view button  to actual controller (secure)
----------------
```php
$this->addPageButtons(ThButton::widget([
    'label' => 'Auditpieraksti',
    'link' => [
        'audit-trail',
        'id' => $model->id,
        'audittrail-ru' => ReturnUrl::getToken('Partija')
    ]
]));
```
Add to controller action
---------------------

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'audit-trail'
                        ],
                        'roles' => [
                            '@'
                        ],
                    ],
                ],
            ],
            'clearFilterState' => ClearFilterStateBehavior::class,
        ];
    }

```php
    public function actions()
    {
        return [
            'audit-trail' => [
                'class' => AudittrailListAction::class,
                'modelName' => CwbrProduct::class,
            ]
        ];
    }
```

Add in kartik grid action column histry button
---------------------

```php 
    $returnUrlToken = ReturnUrl::getToken();
    $columns[] = [
        'class' => ActionColumn::class,
        'template' => '{update}{delete}{audit-trail}',
        'noWrap' => true,
        'urlCreator' =>
            static function ($action, $model, $key) use ($returnUrlToken) {
                return Url::toRoute([
                    '/d3persons/d3p-person-contact/' . $action,
                    'id' => (string)$key,
                    'ru' => $returnUrlToken
                ]);
            },
        'buttons'=>[
            'audit-trail' => static function ($url, $model) {
                return Html::a(
                    '<span class="glyphicon glyphicon-time"></span>', $url, [
                    'title' => Yii::t('d3audittrail', 'Audit trail'),
                ]);
            }
        ]
    ]
```


