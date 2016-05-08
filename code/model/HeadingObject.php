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
     * An array of fields to be indexed by fulltextsearch engines.
     *
     * @var array
     */
    private static $fulltextsearch_fields = [
        'Title',
        'Content'
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
     * Dedicated method to show a title, useful for search results for example.
     *
     * @todo Extend DataExtension for common Native/Module object logic
     * @return string
     */
    public function Title()
    {
        if (!$block = $this->getBlock()) {
            return '';
        }

        return $block->ParentPage()->Title;
    }

    /**
     * Dedicated method to show a link, useful for search results for example.
     *
     * @todo Extend DataExtension for common Native/Module object logic
     * @return string
     */
    public function Link()
    {
        if (!$block = $this->getBlock()) {
            return '';
        }

        return $block->ParentPage()->Link();
    }
    
    /**
     * @return string
     */
    public function Content()
    {
        $tag = 'h' . $this->getField('Level');
        
        return '<' . $tag . '>' . $this->getField('Content') . '</' . $tag . '>';
    }

}
