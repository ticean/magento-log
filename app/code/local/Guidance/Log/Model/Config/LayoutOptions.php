<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LayoutOptions
 *
 * @author mumate
 */
class Guidance_Log_Model_Config_LayoutOptions {
    /**
     * Options getter
     *
     * @return array
     */
   public function toOptionArray()
    {
        return Mage::helper('guidance_log')->getOptionArray("Layout");
    }
}

?>
