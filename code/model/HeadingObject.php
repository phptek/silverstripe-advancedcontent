<?php

/**
 * @package silverstripe-advancedcontent
 * @subpackage model
 * @author Russell Michell 2016 <russ@theruss.com>
 */
class HeadingObject extends DataObject implements AdvancedContentBlockProvider
{

    /**
     * @var string
     */
    private static $singular_name = 'Heading';

    /**
     * @var string
     */
    private static $plural_name = 'Heading objects';

    /**
     * @var array
     */
    private static $db = [
        'Title'   => 'Varchar(255)', // Internal use only
        'Level'   => 'Varchar(2)',
        'Content' => 'Varchar(255)'
    ];

    /**
     * @return FieldList
     * @todo Why doesn't the 'Content' field retain its value?
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $labelField = $fields->dataFieldByName('Title');
        $levelField = DropdownField::create('Level', 'Heading level', array_combine(range(1,6), range(1,6)), $this->Level)
            ->setDescription('Sets what heading HTML tag will be used:<strong> &lt;h1&gt; - &lt;h6&gt;.</strong>')
            ->setAttribute('style', 'width: 50px;');
        $labelField->setDescription(_t(
            'TextObject.LabelField.Description',
            'Used for internal reference purposes only.'
        ));
        
        $fields->addFieldToTab('Root.Main', $levelField);

        return $fields;
    }
    
    /**
     * @return string
     */
    public function BlockView()
    {
        $tag = 'h' . $this->getField('Level');
        
        return '<' . $tag . '>' . $this->getField('Content') . '</' . $tag . '>';
    }

}
