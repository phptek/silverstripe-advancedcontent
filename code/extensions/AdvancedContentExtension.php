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
     * Allow different pagetypes to have a different block-rendering template.
     * 
     * @var string
     */
    private static $block_template = 'BlockList';

    /**
     * @var array
     */
    private static $has_many = [
        'AdvancedContentBlocks'    => 'ACBlock'
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
        
        $blockGridFieldConf = GridFieldConfig_RecordEditor::create();
        $blockGridFieldConf->addComponent(GridFieldOrderableRows::create('BlockSort'));
        $blockGridFieldConf->getComponentByType('GridFieldAddNewButton')
            ->setButtonName('Add block');
     //   $blockGridFieldConf->addComponent(GridFieldAdvancedContentAttributeActions::create());
        $blockGridField = GridField::create(
            'AdvancedContentBlocks',
            _t('AdvancedContentExtension.Labels.BlockField', 'Advanced Blocks'),
            $this->owner->AdvancedContentBlocks(),
            $blockGridFieldConf
        );

        $fields->addFieldToTab('Root.AdvancedContent', $blockGridField);
    }
    
    /**
     * A list of blocks to iterate over in a custom (and overridable) template, then injected into Page.ss $Content
     * variable.
     * 
     * @return HTMLText
     */
    public function Content()
    {
        $data = ArrayData::create([
            'Blocks' => ACBlock::get()->sort('BlockSort'),
        ]);
        
        return $this->owner->renderWith($this->owner->config()->block_template, $data);
    }

}
