<?php

/**
 * Very simple block attribute that allows CMS authors to specify one or more CSS classes for appending to each block
 * in the frontend, via a {@link TextField} in the CMS.
 *
 * @package silverstripe-advancedcontent
 * @subpackage attributes
 * @author Russell Michell 2016 <russ@theruss.com>
 */
class AdvancedContentAttribute_BlockCSS extends AdvancedContentAttribute
{
    /**
     * @inheritdoc
     */
    public function getType()
    {
        return self::ADV_ATTR_TYPE_FORM;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return 'Block CSS';
    }
    
    /**
     * @inheritdoc
     */
    public function UserControl($useField = 'TextField', array $config = [])
    {
        $field = TextField::create($this->getFieldName(), $this->getLabel());
        $field->addExtraClass($this->getCSSClass());
        $field->setValue($this->getValueForUserControl());
        
        return $field;
    }
    
}
