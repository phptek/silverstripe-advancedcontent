<?php
/**
 * Decorates {@link Page} to give it the ability to access content-blocks as-objects and to supply them
 * with $fields via updateCMSFields().
 *
 * @package silverstripe-advancedcontent
 * @author Russell Michell 2016 <russ@theruss.com>
 */

class AdvancedContentExtension extends DataExtension
{
    /**
     * Allows for different pagetypes to have a different block-rendering template.
     * 
     * @var string
     */
    private static $template = 'BlockList';

    /**
     * @var array
     */
    private static $db = [
        'HideWYSIWYG'   => 'Boolean'
    ];

    /**
     * @var array
     */
    private static $has_many = [
        'AdvancedContentBlocks'    => 'AdvancedContentBlock'
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
     * Build us a sortable GridField.
     * 
     * @param FieldList $fields
     * @return void
     */
    public function updateCMSFields(FieldList $fields)
    {
        Requirements::css(ADVANCEDCONTENT_DIR . '/css/advancedcontent.css');
        Requirements::javascript(ADVANCEDCONTENT_DIR . '/javascript/advancedcontent.js');

        if ($this->owner->HideWYSIWYG) {
            $fields->removeByName('Content'); // Yay, yay the witch is dead!
        }
        $fields->removeByName('BlockSort');

        $blockTitleField = LiteralField::create(
            'BlockTitle',
            '<p class="message">' . _t('AdvancedContentExtension.Labels.Content', 'Content') . '</p>'
        );
        $hideEditorField = CheckboxField::create(
            'HideWYSIWYG',
            _t('AdvancedContentExtension.Labels.HideWYSIWYGField', 'Hide WYSIWYG')
        );
        
        $blockGridFieldConf = GridFieldConfig_RecordEditor::create();
        $blockGridFieldConf->addComponent(GridFieldOrderableRows::create('BlockSort'));
        $blockGridFieldConf->getComponentByType('GridFieldAddNewButton')
            ->setButtonName('Add block');
        $blockGridField = GridField::create(
            'AdvancedContentBlocks',
            _t('AdvancedContentExtension.Labels.BlockField', 'Advanced Content Blocks'),
            $this->owner->getComponents('AdvancedContentBlocks'),
            $blockGridFieldConf
        );

        $fields->addFieldsToTab('Root.Main', [
            $blockTitleField,
            $hideEditorField,
            $blockGridField
        ]);
    }

    /**
     * @return ArrayList $list
     */
    public function getBlocks()
    {
        $blocks = AdvancedContentBlock::get()->sort('BlockSort');
        $list = ArrayList::create();
        foreach ($blocks as $block) {
            if (!$block->canView()) {
                continue;
            }

            $list->push($block);
        }
        
        return $list;
    }
    
    /**
     * A list of blocks to iterate over in a custom (and overridable) template, then injected into the Page.ss $Content
     * template variable.
     * 
     * @return HTMLText
     */
    public function Content()
    {
        $showWYSIWYG = !$this->owner->getField('HideWYSIWYG');
        $data = ArrayData::create([
            'WYSIWYG'   => $showWYSIWYG ? $this->owner->getField('Content') : null,
            'Blocks'    => $this->getBlocks(),
        ]);
        
        return $this->owner->renderWith($this->owner->config()->template, $data);
    }

}
