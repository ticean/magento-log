<?php

class Guidance_Log_Helper_Data extends Mage_Core_Helper_Abstract {
    
    public function write_property_file($assoc_arr, $path, $has_sections=false) 
    {
        $content = "";
        if (!$handle = fopen($path, 'w')) {
            return false;
        }
        foreach ($assoc_arr as $key=>$elem) {
            $content .= $key ."=". $elem."\n";
        }
        mkdir("var/guidance", 0775);
        if (!fwrite($handle, $content)) {
            return false;
        }
        fclose($handle);
        return true;
    }

    public function getOptionArray($append_option_name)
    {
        switch ($append_option_name) 
        {
            case "DatabaseAppender":
                $option_arr = array(
                    array('value' => 7, 'label'=>'PDO Appender')
                );
                break;
            case "FileAppender":
                $option_arr = array(
                    array('value' => 2, 'label'=>'File Appender'),
                    array('value' => 3, 'label'=>'Daily File Appender'),
                    array('value' => 4, 'label'=>'Rolling File Appender')
                );
                break;
            case "Layout":
                $option_arr = array(
                    array('value' => 'LoggerLayoutHtml', 'label'=>'LoggerLayoutHTML'),
                    array('value' => 'LoggerLayoutTTCC', 'label'=>'LoggerLayoutTTCC'),
                    array('value' => 'LoggerLayoutXml', 'label'=>'LoggerLayoutXml'),
                    array('value' => 'LoggerLayoutSimple', 'label'=>'LoggerLayoutSimple'),
                    array('value' => 'LoggerLayoutPattern', 'label'=>'LoggerLayoutPattern')
                );
                break;
            case "LogTypes":
                $option_arr = array(
                    array('value' => 0, 'label'=>'OFF'),
                    array('value' => 1, 'label'=>'FATAL'),
                    array('value' => 2, 'label'=>'ERROR'),
                    array('value' => 3, 'label'=>'WARN'),
                    array('value' => 4, 'label'=>'INFO'),
                    array('value' => 5, 'label'=>'DEBUG'),
                    array('value' => 6, 'label'=>'ALL')
                );
                break;
            case "MailAppender":
                $option_arr = array(
                    array('value' => 5, 'label'=>'Mail Appender'),
                    array('value' => 6, 'label'=>'Mail Event Appender')
                );
                break;
            case "Other":
                $option_arr = array(
                    array('value' => 12, 'label'=>'Syslog Appender'),
                    array('value' => 13, 'label'=>'Socket Appender')
                    );
                break;
            case "PHPAppenders":
                $option_arr = array(
                    array('value' => 9, 'label'=>'Echo Appender')
                    //,array('value' => 10, 'label'=>'Echo Appender'),
                    //array('value' => 11, 'label'=>'Console Appender')
                );
                break;
            case "Yesno":
                $option_arr = array(
                    array('value' => 1, 'label'=>'Yes'),
                    array('value' => 2, 'label'=>'No')
                );
            break;
        }//end of switch
        return $option_arr;
    }//end of public function getOptionArray($append_option_name)


    public function getAppenderClassName($optioncode, $value)
    {
        if($optioncode=="FA")
        {	
            switch($value)
            {
                case "2":
                    $appender_class_name = "LoggerAppenderFile";
                    break;
                case "3":
                    $appender_class_name = "LoggerAppenderDailyFile";
                    break;
                case "4":
                    $appender_class_name = "LoggerAppenderRollingFile";
                    break;
            }//end of switch($value)
        }//end of if($optioncode=="FA")

        if($optioncode=="MA")
        {	
            switch($value)
            {
                case "5":
                    $appender_class_name = "LoggerAppenderMail";
                    break;
                case "6":
                    $appender_class_name = "LoggerAppenderMailEvent";
                    break;
            }//end of switch($value)
        }//end of if($optioncode=="MA")

        if($optioncode=="DB")
        {	
            switch($value)
            {
                case "7":
                    $appender_class_name = "LoggerAppenderPDO";
                    break;
            }//end of switch($value)
        }//end of if($optioncode=="DB")

        if($optioncode=="PHP")
        {	
            switch($value)
            {
                case "9":
                    $appender_class_name = "LoggerAppenderEcho";
                    break;
            }//end of switch($value)
        }//end of if($optioncode=="PHP")

        return $appender_class_name;
    }//end of getAppenderClass($optioncode, $value)

}

?>