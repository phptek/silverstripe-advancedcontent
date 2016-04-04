<?php

/**
 * We're managing content-as-objects not just blobs of content-as-fields on _other_ DataObjects. We need a dedicated
 * DataObject dedicated to representing unique chunks of text/copy.
 * The {@link SimpleJSONStorage} simple maps some page-specific metadata to a DataObject whether that's a {@link File},
 * {@link Image} or {@link TextObject}, as such each of these objects will have no useable $has_one on the page(s) on
 * which they're used.
 * @package silverstripe-advancedcontent
 * @subpackage model
 * @author Russell Michell 2016 <russ@theruss.com>
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
        'Title'   => 'Varchar(255)', // Internal use only
        'Content' => 'HTMLText'
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
     * @return string
     */
    public function BlockView()
    {
        return $this->Content;
    }

}
