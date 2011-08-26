<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_Logging
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

class Guidance_Log_Model_Mysql4_Log extends Mage_Core_Model_Mysql4_Abstract
{
   /**
    * Constructor
    */
    protected function _construct()
    {
        $this->_init('guidance_log/log', 'log_id');
    }

    /**
     * Rotate logs - get from database and pump to CSV-file
     *
     * @param int $lifetime
     */
    public function rotate($lifetime)
    {
        try {
            $this->beginTransaction();

            $table = $this->getTable('guidance_log/log');

            // get the latest log entry required to the moment
            $clearBefore = $this->formatDate(time() - $lifetime);
            $latestLogEntry = $this->_getWriteAdapter()->fetchOne("SELECT log_id FROM {$table}
                WHERE `timestamp` < '{$clearBefore}' ORDER BY 1 DESC LIMIT 1");
            if (!$latestLogEntry) {
                return;
            }

            // make sure folder for dump file will exist
            $archive = Mage::getModel('guidance_log/archive');
            $archive->createNew();

            // dump all records before this log entry into a CSV-file
            $csv = fopen($archive->getFilename(), 'w');
            foreach ($this->_getWriteAdapter()->fetchAll("SELECT *
                FROM {$table} WHERE log_id <= {$latestLogEntry}") as $row) {
                fputcsv($csv, $row);
            }
            fclose($csv);
            $this->_getWriteAdapter()->query("DELETE FROM {$table} WHERE log_id <= {$latestLogEntry}");
            $this->commit();
        } catch (Exception $e) {
            $this->rollBack();
        }
    }

    /**
     * Select all values of specified field from main table
     *
     * @param string $field
     * @param bool $order
     * @return array
     */
    public function getAllFieldValues($field, $order = true)
    {        
        return $this->_getReadAdapter()->fetchCol("SELECT DISTINCT
            {$this->_getReadAdapter()->quoteIdentifier($field)} FROM {$this->getMainTable()}"
            . (null !== $order ? ' ORDER BY 1' . ($order ? '' : ' DESC') : '')
        );
    }   
        
}
