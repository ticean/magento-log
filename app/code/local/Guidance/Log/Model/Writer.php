<?php

set_include_path(get_include_path() . PS . Mage::getBaseDir('lib') . DS . 'Log4php');



class Guidance_Log_Model_Writer extends Mage_Core_Model_Abstract {

    protected $_logMode;
    protected $_appender;
    /** inidicates if log4php has already been configured */
    private static $configured = false;

    protected static $types = array('0'=>'OFF','1'=>'FATAL','2'=>'ERROR','3'=>'WARN','4'=>'INFO','5'=>'DEBUG','6'=>'ALL');
    /**
     * Configure log4php framework if not already configured
     */
    private static function configureLog4php() {
        if (self::$configured == false) {
            self::$configured = true;
            $confinugartionParametersArray = self::getAdminConfigurationArray();
            Logger::configure($confinugartionParametersArray);
        }
    }

        
    public static function log($moduleName,$logLevel,$message, $additionalInfo  = '')
    {
        try{                
            $msgArr = null;
            if($moduleName)
            {
                $moduleName = str_replace(' ', '_', $moduleName);
            }
            if($additionalInfo) {
                $msgArr = array($message,$additionalInfo);
            }
            else {
                $msgArr = $message;
            }

            if ($logLevel) {
               $logLevel = trim(strtolower($logLevel));
            }
            else {
                 $logLevel = 'debug';
            }

            $levelArr = array('all','debug','info','warn','error','fatal');
            if(!in_array($logLevel,$levelArr))
            {
                $logLevel = 'debug';
            }

            if (!self::$configured) {
                self::configureLog4php();
                self::$configured = true;
            }

            $logger = Logger::getLogger($moduleName);       

            if ($logLevel == 'debug') {
                $logger->debug($msgArr);
            }

            if ($logLevel == 'info') {
                $logger->info($msgArr);
            }

            if ($logLevel == 'fatal') {
                $logger->fatal($msgArr);
            }


            if ($logLevel == 'error') {
                $logger->error($msgArr);
            }

            if ($logLevel == 'warn') {
                $logger->warn($msgArr);
            }
        }
        catch(Exception $ex){
            Mage::log('Error in Guidancelog Module:'.$ex->getTraceAsString());
        }
    }

    public static function getAdminConfigurationArray()
    {
        
        $doFileLog = Mage::getStoreConfig('guidancelog/fileappendersettings/file_appender_log');
        $doMailLog = Mage::getStoreConfig('guidancelog/mailappendersettings/mail_appender_log');
        $doDBLog = Mage::getStoreConfig('guidancelog/databaseappendersettings/database_appender_log');
        $doPHPLog = Mage::getStoreConfig('guidancelog/phpappendersettings/php_appender_log');
        
        $logarray=array();
        
        $logarray['log4php.rootLogger']='ALL, FA, MA, DB, PHP';
        
        if((is_null($doFileLog) || $doFileLog == '0') && (is_null($doMailLog) || $doMailLog == '0') && (is_null($doDBLog) || $doDBLog == '0') && (is_null($doPHPLog) || $doPHPLog == '0'))
        {
            $logarray['log4php.appender.FA'] = 'LoggerAppenderNull';
        }
        else
        {
            if($doFileLog != '0')
            {
                self::fillFileAppenderData('guidancelog/fileappendersettings/', $logarray);
            }
            if($doMailLog != '0')
            {
                self::fillMailAppenderData('guidancelog/mailappendersettings/', $logarray);
            }
            if($doDBLog != '0')
            {
                self::fillDBAppenderData('guidancelog/databaseappendersettings/', $logarray);
            }
            if($doPHPLog != '0')
            {
                self::fillPHPAppenderData('guidancelog/phpappendersettings/', $logarray);
            }
        }
        return $logarray;
        
    }
    
    static function fillFileAppenderData($path,&$logarray)
    {
        $file_appender_type = Mage::getStoreConfig($path.'file_appender_type');
        $log_filename = Mage::getStoreConfig($path.'log_filename');
        $file_appender_layout = Mage::getStoreConfig($path.'file_appender_layout');
        if($file_appender_type == '2')
        {
            $logarray['log4php.appender.FA']=Mage::helper('guidance_log')->getAppenderClassName('FA',$file_appender_type);
            $logarray['log4php.appender.FA.file']='var/guidance/log/'.$log_filename;
        }
        elseif($file_appender_type == '3')
        {
            $logarray['log4php.appender.FA']=Mage::helper('guidance_log')->getAppenderClassName('FA',$file_appender_type);
            $logarray['log4php.appender.FA.file']='var/guidance/log/'.str_replace('.','_%d.',$log_filename) ;
            $logarray['log4php.appender.FA.datePattern']='Y-m-d';
        }
        elseif($file_appender_type == '4')
        {
            $logarray['log4php.appender.FA']=Mage::helper('guidance_log')->getAppenderClassName('FA',$file_appender_type);
            $logarray['log4php.appender.FA.file']='var/guidance/log/'.$log_filename;
            $logarray['log4php.appender.FA.maxFileSize']=Mage::getStoreConfig($path.'log_file_size');
            $logarray['log4php.appender.FA.maxBackupIndex']=Mage::getStoreConfig($path.'log_file_maxbackupindex');
        }
        $logarray['log4php.appender.FA.layout']=$file_appender_layout;
        if($file_appender_layout == 'LoggerLayoutPattern')
        {
            $logarray['log4php.appender.FA.layout.ConversionPattern']=Mage::getStoreConfig($path.'file_layout_pattern');
        }
        $logarray['log4php.appender.FA.threshold']=self::$types[Mage::getStoreConfig($path.'file_appender_log')];
    }
    
    static function fillMailAppenderData($path,&$logarray)    
    {
        $logarray['log4php.appender.MA'] = Mage::helper('guidance_log')->getAppenderClassName('MA',Mage::getStoreConfig($path.'mail_appender_type'));
        $logarray['log4php.appender.MA.layout'] = Mage::getStoreConfig($path.'mail_appender_layout');
        if(Mage::getStoreConfig($path.'mail_appender_layout') == 'LoggerLayoutPattern')
        {
            $logarray['log4php.appender.FA.layout.ConversionPattern']=Mage::getStoreConfig($path.'mail_layout_pattern');
        }
        $logarray['log4php.appender.MA.from'] = Mage::getStoreConfig($path.'mail_account_from');
        $logarray['log4php.appender.MA.to'] = Mage::getStoreConfig($path.'mail_account_to');
        $logarray['log4php.appender.MA.subject'] = Mage::getStoreConfig($path.'mail_subject');
        if(Mage::getStoreConfig($path.'mail_appender_type') == '6')
        {
            $logarray['log4php.appender.MA.smtpHost'] = Mage::getStoreConfig($path.'mail_smtp_host');
            $logarray['log4php.appender.MA.port'] = Mage::getStoreConfig($path.'mail_smtp_port');
        }
        $logarray['log4php.appender.FA.threshold'] =self::$types[Mage::getStoreConfig($path.'mail_appender_log')];
    }
    
    static function fillDBAppenderData($path,&$logarray) 
    {   
    
        $logarray['log4php.appender.DB'] = Mage::helper('guidance_log')->getAppenderClassName('DB',Mage::getStoreConfig($path.'database_appender_type'));
        $dsn = 'mysql:host='.Mage::getStoreConfig($path.'database_server_url')
                .';dbname='.Mage::getStoreConfig($path.'database_name');
        $logarray['log4php.appender.DB.dsn'] = $dsn;        
        $logarray['log4php.appender.DB.user'] = Mage::getStoreConfig($path.'database_account_username');
        $logarray['log4php.appender.DB.password'] = Mage::getStoreConfig($path.'database_account_password');
        if(Mage::getStoreConfig($path.'database_create_table') == '1')
        {
            $logarray['log4php.appender.DB.createTable'] = 'true';
        }
        else
        {
            $logarray['log4php.appender.DB.createTable'] = 'false';
        }
        $logarray['log4php.appender.DB.table'] = Mage::getStoreConfig($path.'database_table_name');
        $logarray['log4php.appender.DB.insertSql'] = Mage::getStoreConfig($path.'database_insert_sql');
        $logarray['log4php.appender.DB.insertPattern'] = Mage::getStoreConfig($path.'database_insert_pattern');
        $logarray['log4php.appender.DB.threshold'] = self::$types[Mage::getStoreConfig($path.'database_appender_log')];
    }
    
    static function fillPHPAppenderData($path,&$logarray)     
    {
	$logarray['log4php.appender.PHP'] = Mage::helper('guidance_log')->getAppenderClassName('PHP',Mage::getStoreConfig($path.'php_appender_type'));
        $logarray['log4php.appender.PHP.layout'] = Mage::getStoreConfig($path.'php_appender_layout');
        if(Mage::getStoreConfig($path.'php_appender_layout') == 'LoggerLayoutPattern')
        {
            $logarray['log4php.appender.PHP.layout.ConversionPattern']=Mage::getStoreConfig($path.'php_layout_pattern');
        }
       // $logarray['log4php.appender.PHP.htmlLineBreaks'] = "true";
        $logarray['log4php.appender.PHP.threshold'] = self::$types[Mage::getStoreConfig($path.'php_appender_log')];
    }

    static function setConfigChanged()
    {
        self::$configured = false;
    }
    
}