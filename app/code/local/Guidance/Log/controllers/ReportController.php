<?php

class Guidance_Log_ReportController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Log page
     */
    public function indexAction()
    {
        $this->_title($this->__('System'))
             ->_title($this->__('Guidance Log'))
             ->_title($this->__('Report'));

        $this->loadLayout();
        $this->_setActiveMenu('system/guidance_log');
        $this->renderLayout();
    }

    /**
     * Log grid ajax action
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * View logging details
     */
    public function detailsAction()
    {
        $this->_title($this->__('System'))
             ->_title($this->__('Guidance Log'))
             ->_title($this->__('Report'))
             ->_title($this->__('View Entry'));

        $logId = $this->getRequest()->getParam('log_id');
        $model   = Mage::getModel('guidance_log/log')
            ->load($logId);
        
        echo $model->getHtml();
        //if (!$model->getId()) {
        //    $this->_redirect('*/*/');
        //    return;
        //}
        //Mage::register('current_event', $model);

        //$this->loadLayout();
        //$this->_setActiveMenu('system/enterprise_logging');
        //$this->renderLayout();
    }

    /**
     * Export log to CSV
     */
    public function exportCsvAction()
    {
        $this->_prepareDownloadResponse('log.csv',
            $this->getLayout()->createBlock('guidance_log/adminhtml_index_grid')->getCsvFile()
        );
    }

    /**
     * Export log to MSXML
     */
    public function exportXmlAction()
    {
        $this->_prepareDownloadResponse('log.xml',
            $this->getLayout()->createBlock('guidance_log/adminhtml_index_grid')->getExcelFile()
        );
    }

    /**
     * Archive page
     */
    public function archiveAction()
    {
        $this->_title($this->__('System'))
             ->_title($this->__('Guidance Actions Logs'))
             ->_title($this->__('Archive'));

        $this->loadLayout();
        $this->_setActiveMenu('system/enterprise_logging');
        $this->renderLayout();
    }

    /**
     * Archive grid ajax action
     */
    public function archiveGridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Download archive file
     */
    public function downloadAction()
    {
        $archive = Mage::getModel('guidance_log/archive')->loadByBaseName(
            $this->getRequest()->getParam('basename')
        );
        if ($archive->getFilename()) {
            $this->_prepareDownloadResponse($archive->getBaseName(), $archive->getContents(), $archive->getMimeType());
        }
    }

    /**
     * permissions checker
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'archive':
            case 'download':
            case 'archiveGrid':
                return Mage::getSingleton('admin/session')->isAllowed('admin/system/guidance_log/backups');
                break;
            case 'grid':
            case 'exportCsv':
            case 'exportXml':
            case 'details':
            case 'index':
                return Mage::getSingleton('admin/session')->isAllowed('admin/system/guidance_log/events');
                break;
        }

    }
}
