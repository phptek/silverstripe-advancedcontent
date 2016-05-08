<?php

/**
 * We're managing content-as-objects and not just blobs of content-as-fields on _other_ DataObjects. We need a dedicated
 * DataObject dedicated to representing distinct chunks of text.
 * 
 * {@link SimpleJSONText} maps some page-specific metadata to a DataObject whether that's a {@link File},
 * {@link Image} or {@link TextObject} and as such, each of these objects will have no useable $has_one on the page(s) on
 * which they're used.
 * 
 * @package silverstripe-advancedcontent
 * @subpackage model
 * @author Russell Michell 2016 <russ@theruss.com>
 * @todo Search:
 * - Each ContentObject shows up as a separate result, even though each one may be on a single page
 * - Show Each one and the page itself (if it has matching text found in its optional Content field)
 * - Better: Create a SearchIndex with own search() implementation that:
 *  1). "Pools" all blocks from the same page into a single result
 *  2). Remove the TextObject::Title() and TextObject::Link() methods and the ref to include subclasses in BasePage
 */
class TextObject extends DataObject implements AdvancedContentBlockProvider
{

    /**
     * @var string
     */
    private static $singular_name = 'Text';

    /**
     * @var string
     */
    private static $plural_name = 'Text objects';

    /**
     * @var array
     */
    private static $db = [
        'Title'         => 'Varchar(255)', // Internal use only
        'Content'       => 'HTMLText'
    ];

    /**
     * @var array
     */
    private static $has_one = [
        'MetaBlock'     => 'AdvancedContentBlock'
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
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $contentField = $fields->dataFieldByName('Content');
        $contentField->setRows(10);

        $labelField = $fields->dataFieldByName('Title');
        $labelField->setDescription(_t(
            'TextObject.LabelField.Description',
            'Used for internal reference purposes only.'
        ));

        return $fields;
    }

    /**
     * @return AdvancedContentBlock
     * @todo Use has_one instead
     */
    public function getMetaBlock()
    {
        return AdvancedContentBlock::get()
            ->filter('BlockID', $this->getField('ID'))
            ->first();
    }

    /**
     * Dedicated method to show a title, useful for search results for example.
     *
     * @todo Extend DataExtension for common Native/Module object logic
     * @todo Use has_one instead
     * @return string
     */
    public function Title()
    {
        if (!$block = $this->getMetaBlock()) {
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
        if (!$block = $this->getMetaBlock()) {
            return '';
        }

        return $block->ParentPage()->Link();
    }

    /**
     * @return HTMLText
     */
    public function Content()
    {
        return $this->getField('Content');
    }

}
