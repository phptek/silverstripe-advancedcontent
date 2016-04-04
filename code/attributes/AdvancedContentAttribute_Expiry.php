<?php

/**
 * Very simple block attribute that allows CMS authors to specify an expiry date for a block.
 *
 * @package silverstripe-advancedcontent
 * @subpackage attributes
 * @author Russell Michell 2016 <russ@theruss.com>
 */
class AdvancedContentAttribute_Expiry extends AdvancedContentAttribute
{

    /**
     * @var string
     */
    protected $dateFormatFunc = '';
    
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
        return 'Block Expiry';
    }

    /**
     * @param string $func A string representing one of the callable methods on {@link SS_Datetime}. 
     * @return AdvancedContentAttribute_Embargo
     */
    public function setDateFormat($func)
    {
        $this->dateFormatFunc = $func;
        
        return $this;
    }

    /**
     * @return string
     */
    public function getDateFormat()
    {
        return $this->dateFormatFunc;
    }
    
    /**
     * Return the embargo date, optionally formatted via {@link $this->setDateFormat()}.
     * 
     * @return mixed DateTime|string
     */
    public function getDate()
    {
        $date = SS_Datetime::create()->setValue($this->getValueForUserControl());
        if ($func = $this->getDateFormat() && is_callable($date::$func)) {
            $date = $date->$func();
        }
        
        return $date;
    }
    
    /**
     * @inheritdoc
     */
    public function UserControl($useField = 'TextField', array $config = [])
    {
        Config::inst()->update('DateField', 'default_config', [
            'showcalendar'      => true,
            'dateformat'        => 'dd-MM-yyyy',
            'datavalueformat'   => 'dd-MM-yyyy'
        ]);

        $field = parent::UserControl('DateField');
        
        return $field;
    }

}
