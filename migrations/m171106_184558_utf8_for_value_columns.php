<?php

use yii\db\Migration;

class m171106_184558_utf8_for_value_columns extends Migration
{
    public function safeUp()
    {
        $sql = "
            ALTER TABLE `tbl_audit_trail` 
            CHANGE COLUMN `old_value` `old_value` TEXT CHARACTER SET 'utf8' COLLATE utf8_general_ci NULL DEFAULT NULL,
            CHANGE COLUMN `new_value` `new_value` TEXT CHARACTER SET 'utf8' COLLATE utf8_general_ci NULL DEFAULT NULL;
        ";
        $this->execute($sql);
    }

    public function safeDown()
    {
        echo "m171106_184558_utf8_for_value_columns cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171106_184558_utf8_for_value_columns cannot be reverted.\n";

        return false;
    }
    */
}
