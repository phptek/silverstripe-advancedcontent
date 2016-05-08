<?php
/**
 * Fulltextsearch logic for use with the silverstripe-fulltextsearch module in a CWP context.
 *
 * Allows multiple blocks related to a page, to appear as a single result using the parent page's Title and Link by
 * configuring Solr with a facet field into which will be stored each block's parent page ID.
 *
 * Basic Configuration (See docs/en/search.md for more detail):
 *
 * Add the following to your project's mysite/_config.php:
 *
 * BasePage_Controller::$search_index_class = 'AdvancedContentSearchIndex_CWP';
 *
 * @package silverstripe-advancedcontent
 * @subpackage search
 * @author Russell Michell 2016 <russ@theruss.com>
 * @see {CwpSearchEngine}, {@link AdvancedContentSearchIndex_CWP}, {@link CwpSearchIndex}.
 */

class AdvancedContentSearchIndex_CWP extends CwpSearchIndex
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        
        foreach (BasePage_Controller::$classes_to_search as $classSpec) {
            $this->addClass($classSpec['class']);
        }

		$this->addClass('SiteTree');
		$this->addFilterField('ShowInSearch', null, ['stored' => true]);
        
        $this->addAllFulltextFields();
   		$this->addFilterField('BlockID', null, ['stored' => true]);
        $this->addCopyField('AdvancedContentBlock_BlockID', 'fAdvancedContentBlockID');
    }

    /**
     * Add each block's parent page IDs to a Solr index.
     * 
     * @return string
     */
    public function getFieldDefinitions()
	{
		$xml = parent::getFieldDefinitions();
		$xml .= "\n\n\t\t<!-- Additional custom fields for AdvancedContent Module -->";
		$xml .= "\n\t\t<field name='fAdvancedContentBlockID' type='int' indexed='true' stored='true' multiValued='false' />";
		
		return $xml;
	}

}
