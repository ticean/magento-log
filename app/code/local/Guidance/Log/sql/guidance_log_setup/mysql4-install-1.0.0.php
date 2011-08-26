<?php
/**
 * Adding Different Attributes
 */

Mage::log("START INSTALL CUSTOM CUSTOMER ATTRIBUTES");
$installer = $this;
$installer->startSetup();

//Add table to log data
$installer->run('DROP TABLE IF EXISTS `guidance_log`');
$installer->run('CREATE TABLE `guidance_log` 
                (`log_id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
                `timestamp` varchar(32),
                `logger` varchar(64),
                `level` varchar(32),
                `message` varchar(9999),
                `additional_info` text,
                PRIMARY KEY (`log_id`))');
$installer->endSetup();