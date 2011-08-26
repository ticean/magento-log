<?php

class Guidance_Log_Model_Config_OtherOptions {
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
	return Mage::helper('guidance_log')->getOptionArray("Other");
    }
}

?>
