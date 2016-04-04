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
