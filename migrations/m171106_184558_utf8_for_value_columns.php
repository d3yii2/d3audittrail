<?php

use yii\db\Migration;

class m171106_184558_utf8_for_value_columns extends Migration
{
    public function safeUp()
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS `tbl_audit_trail` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `old_value` text CHARACTER SET utf8,
              `new_value` text CHARACTER SET utf8,
              `action` varchar(255) NOT NULL,
              `model` varchar(255) NOT NULL,
              `field` varchar(255) DEFAULT NULL,
              `stamp` datetime NOT NULL,
              `user_id` varchar(255) DEFAULT NULL,
              `model_id` varchar(255) NOT NULL,
              PRIMARY KEY (`id`),
              KEY `idx_audit_trail_user_id` (`user_id`),
              KEY `idx_audit_trail_model_id` (`model_id`),
              KEY `idx_audit_trail_field` (`field`),
              KEY `idx_audit_trail_action` (`action`),
              KEY `idx_audit_trail_model` (`model`,`stamp`)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
        ";
        $this->execute($sql);

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
