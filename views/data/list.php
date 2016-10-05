<?php

use yii\helpers\Html;
use yii\helpers\Url;
use eaBlankonThema\widget\ThButton;
use eaBlankonThema\assetbundles\layout\LayoutAsset;
use d3yii2\d3audittrail\models\TblAuditTrail;

LayoutAsset::register($this);

$title                                 = Yii::t('d3audittrail',
        'Audittrail data');
$this->title                           = $title;
Yii::$app->view->params['pageHeader']  = $title;
Yii::$app->view->params['pageButtons'] = ThButton::widget([
        'label' => Yii::t('d3audittrail', 'Back'),
        'link' => Url::previous(),
        'icon' => ThButton::ICON_CHEVRON_LEFT,
        'type' => ThButton::TYPE_DEFAULT
    ]);
?>
<div class="row">
    <div class="col-md-12">
        <?php
        foreach ($data as $mName => $mData) {
            ?>
            <div class="panel rounded shadow">
                <div class="panel-heading">
                    <div class="pull-left">
                        <h3 class="panel-title">
                            <?= $mData['label'] ?>
                        </h3>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="panel-body  no-padding">

                    <div class="table-responsive mb-20">
                        <table class="table table-striped  table-success">
                            <thead>
                                <tr>
                                    <th><?= Yii::t('d3audittrail', 'Time') ?></th>
                                    <th><?= Yii::t('d3audittrail', 'Field') ?></th>
                                    <th><?= Yii::t('d3audittrail', 'Action') ?></th>
                                    <th><?= Yii::t('d3audittrail', 'Old Value') ?></th>
                                    <th><?= Yii::t('d3audittrail', 'New Value') ?></th>
                                    <th><?= Yii::t('d3audittrail', 'User') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($mData['table'] as $row) {
                                    ?>
                                    <tr data-key="8">
                                        <td><?= $row['stamp'] ?></td>
                                        <td><?= $row['field']?$mData['attribute_labels'][$row['field']]:' - ' ?></td>
                                        <td><?= TblAuditTrail::getActionValueLabel($row['action']) ?></td>
                                        <td><?= $row['old_value'] ?></td>
                                        <td><?= $row['new_value'] ?></td>
                                        <td><?= $row['username'] ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>
