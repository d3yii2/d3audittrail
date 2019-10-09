<?php


use cornernote\returnurl\ReturnUrl;
use d3system\yii2\web\D3SystemView;
use eaBlankonThema\widget\ThReturnButton;
use yii\helpers\Url;
use eaBlankonThema\widget\ThButton;
use eaBlankonThema\assetbundles\layout\LayoutAsset;
use d3yii2\d3audittrail\models\TblAuditTrail;

/**
* @var D3SystemView $this
 */

LayoutAsset::register($this);



$this->title = Yii::t('d3audittrail','Audit trail records');
$this->setPageHeader($this->title);
ReturnUrl::$requestKey = 'audittrail-ru';
$this->addPageButtons(ThReturnButton::widget([
    'link' => ReturnUrl::getUrl()
]));
ReturnUrl::$requestKey = 'ru';
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
                                        <td><?= ($row['field']&&isset($mData['attribute_labels'][$row['field']]))?$mData['attribute_labels'][$row['field']]:$row['field'] ?></td>
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
