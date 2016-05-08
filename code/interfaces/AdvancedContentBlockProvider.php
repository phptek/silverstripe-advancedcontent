<?php
/**
 * Implementors will be shown in the block "Create" menu in the CMS. Implementors should also declare a $singular_name
 * private (config) static, in order to be available for use.
 *
 * @package silverstripe-advancedcontent
 * @subpackage model
 * @author Russell Michell 2016 <russ@theruss.com>
 */

interface AdvancedContentBlockProvider
{

    /**
     * Requiring this is only really useful if dev's try and create an implementation tht isn't a {@link DataObject}
     * subclass.
     * 
     * @return FieldList
     */
    public function getCMSFields();
    
    /**
     * @return mixed string|HTMLText
     */
    public function Content();
    
}
