<?php
/**
 * @author Russell Michell 2015 - 2016 <russell.michell@deviate.net.nz>
 * @package silverstripe-advancedccontent
 */

define('ADVANCEDCONTENT_DIR', 'advancedcontent');
define('ADVANCEDCONTENT_NAME', 'Advanced Content');

if ((bool)floatval(phpversion()) < 5.4) {
    throw new Exception('Minimum PHP version is 5.4.');
}

// For New Zealand's Common Web Platform (cwp.govt.nz)
if (defined('CWP_ENVIRONMENT')) {
    BasePage_Controller::$classes_to_search = AdvancedContentFulltextSearch::$advanced_content_classes;
    BasePage_Controller::$search_index_class = 'AdvancedContentSearchIndex_CWP';
}