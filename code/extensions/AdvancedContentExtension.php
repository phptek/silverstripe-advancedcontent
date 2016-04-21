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
    private static $has_many = [
        'AdvancedContentBlocks'    => 'AdvancedContentBlock'
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

        $fields->removeByName('BlockSort');
        
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

        $fields->addFieldToTab('Root.AdvancedContent', $blockGridField);
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
        $data = ArrayData::create([
            'Blocks' => $this->getBlocks(),
        ]);
        
        return $this->owner->renderWith($this->owner->config()->template, $data);
    }

}
