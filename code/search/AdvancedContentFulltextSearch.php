<?php
/**
 * Fulltextsearch logic for use with the silverstripe-fulltextsearch module in a CWP context.
 *
 * Allows multiple blocks related to a page to appear as a single result using the parent page's Title and Link.
 * 
 * To make this work, AdvancedContentSearchIndex_CWP will need to be the default Solr core index in your project.
 * It subclasses {@link CwpSearchIndex} so you still have all of CWP's search features available to you.
 *
 * @package silverstripe-advancedcontent
 * @subpackage search
 * @author Russell Michell 2016 <russ@theruss.com>
 * @see {CwpSearchEngine}, {@link AdvancedContentSearchIndex_CWP}.
 */

class AdvancedContentFulltextSearch extends CwpSearchEngine
{

    /**
     * @var array
     * @todo create a private static on each class that tells us which fields are fulltext-searchable
     */
    public static $advanced_content_classes = [
        [
            'class' 			=> 'TextObject',
            'includeSubclasses'	=> false
        ],
		[
			'class'				=> 'HeadingObject',
			'includeSubclasses'	=> false
		],
		[
			'class'				=> 'AdvancedContentBlock',
			'includeSubclasses'	=> false
		]
    ];

    /**
     * Build a SearchQuery for a new search, via CWP's own getSearchQuery().
     *
     * @param string $keywords
     * @param array $classes
     * @return SearchQuery
     */
    public function getSearchQuery($keywords, $classes = null) {
        $classes = $classes ?: self::$advanced_content_classes;
        $query = parent::getSearchQuery($keywords, $classes);

        // Whittles down our list of blocks to only one per Page
        $firstBlockForEachPage = [];
        $blocks = AdvancedContentBlock::get(); // TODO class-cache with PageID as key
        foreach ($blocks as $block) {
            $firstBlockForEachPage[$block->ParentPageID] = $block->ID;
        }
        
        $range = new SearchQuery_Range(min($firstBlockForEachPage), max($firstBlockForEachPage));
        $query->filter('fAdvancedContentBlockID', $range);
        
        return $query;
    }
    
}
