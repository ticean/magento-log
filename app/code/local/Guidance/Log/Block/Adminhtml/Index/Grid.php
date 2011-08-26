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

/**
 * Admin Actions Log Grid
 */
class Guidance_Log_Block_Adminhtml_Index_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('guidanceLogGrid');
        $this->setDefaultSort('timestamp');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * PrepareCollection method.
     */
    protected function _prepareCollection()
    {
        $this->setCollection(Mage::getResourceModel('guidance_log/log_collection'));
        return parent::_prepareCollection();
    }

    /**
     * Return grids url
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    /**
     * Grid URL
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/details', array('event_id'=>$row->getId()));
    }

    /**
     * Configuration of grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('timestamp', array(
            'header'    => Mage::helper('guidance_log')->__('Time'),
            'index'     => 'timestamp',
            'type'      => 'datetime',
            'width'     => 160,
        ));

        $loggers = array();
        foreach (Mage::getResourceSingleton('guidance_log/log')->getAllFieldValues('logger') as $logger) {
            $loggers[$logger] = $logger;
        }
        
        $this->addColumn('logger', array(
            'header'    => Mage::helper('guidance_log')->__('Module'),
            'index'     => 'logger',
            'type'      => 'options',
            'options'   => $loggers,
            'sortable'  => false,
        ));

        $this->addColumn('level', array(
            'header'    => Mage::helper('guidance_log')->__('Level'),
            'index'     => 'level',
            'type'      => 'options',
            'sortable'  => true,
            'width'     => 100,
            'options'   => Mage::getSingleton('guidance_log/config')->getLogLevels(),
        ));

       $this->addColumn('message', array(
            'header'    => Mage::helper('guidance_log')->__('Message'),
            'index'     => 'message',
            'type'      => 'text',
            'sortable'  => true,
            'width'     => 300,
        ));

       $this->addColumn('additional_info', array(
            'header'    => Mage::helper('guidance_log')->__('Additional Information'),
            'index'     => 'additional_info',
            'type'      => 'text',
            'sortable'  => true,
            'width'     => 500,
        ));

        $this->addColumn('view', array(
            'header'  => Mage::helper('guidance_log')->__('Full Details'),
            'width'   => 50,
            'type'    => 'action',
            'getter'  => 'getId',
            'actions' => array(array(
                'caption' => Mage::helper('guidance_log')->__('View'),
                'url'     => array(
                    'base'   => '*/*/details',
                ),
                'field'   => 'log_id'
            )),
            'filter'    => false,
            'sortable'  => false,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('customer')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('customer')->__('Excel XML'));
        return $this;
    }


}
