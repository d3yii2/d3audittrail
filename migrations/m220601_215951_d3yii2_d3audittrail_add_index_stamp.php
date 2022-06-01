<?php

use yii\db\Migration;

class m220601_215951_d3yii2_d3audittrail_add_index_stamp  extends Migration {

    public function safeUp() { 
        $this->execute('
            ALTER TABLE `tbl_audit_trail`
              ADD INDEX `idx_audit_trail_stamp` (`stamp`);
            
                    
        ');
    }

    public function safeDown() {
        echo "m220601_215951_d3yii2_d3audittrail_add_index_stamp cannot be reverted.\n";
        return false;
    }
}
