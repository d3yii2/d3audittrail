<?php

use yii\db\Migration;

class m230430_212951_d3yii2_d3audittrail_add_indexes  extends Migration {

    public function safeUp() { 
        $this->execute('
            ALTER TABLE `tbl_audit_trail` 
            	ADD KEY `idx_field_name_id`(`field_name_id`,`model_name_id`,`model_id`,`stamp`) , 
            	ADD KEY `idx_model_name_id`(`model_name_id`) ;        
        ');
    }

    public function safeDown() {
        echo "m230430_212951_d3yii2_d3audittrail_add_indexes cannot be reverted.\n";
        return false;
    }
}
