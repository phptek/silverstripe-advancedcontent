<?php

/**
 * Simple service for providing common logic in a flexible way instead of non DRY logic or use of global state.
 *
 * @package silverstripe-advancedcontent
 * @author Russell Michell 2016 <russ@theruss.com>
 */
class AdvancedContentService extends Object
{
    /**
     * Return an array of SilverStripe's core DataObjects that we'd like to feature as a block.
     * N.b these are not defined on an SS config static because the only way dev's should be able to append to
     * the list of block-types is by implementing {@link AdvancedContentBlockProvider}.
     * 
     * @return array
     */
    public function ssCoreTypes()
    {
        return ['File', 'Image'];
    }
    
    /**
     * Fetch all currently available block types in the system.
     *
     * @return ArrayList
     */
    public function getBlockTypes()
    {
        $nativeBlockClasses = $this->ssCoreTypes();
        $moduleBlockClasses = ClassInfo::implementorsOf('AdvancedContentBlockProvider');
        $allBlockClasses = array_merge($nativeBlockClasses, $moduleBlockClasses);
        
        // Run some checks on custom / 3rd party blocks before
        $this->checkCustomBlocks($moduleBlockClasses);

        $types = [];
        foreach ($allBlockClasses as $class) {
            $object = $class::create();
            $object->setField('SingularName', $class::config()->singular_name);
            $object->setField('Icon', $class::config()->icon); // TODO
            $types[$class] = $object;
        }

        return ArrayList::create($types);
    }

    /**
     * @param array $list
     * @throws AdvancedContentException
     * @return void
     */
    public function checkCustomBlocks($list)
    {
        $errors = [];
        foreach ($list as $class) {
            if(!$class::config()->singular_name) {
                $errors[] = $class;
            }
        }
        
        if ($errors) {
            $msg = (count($errors) >1 ? 'These classes' : $errors[0]) . ' should define a "$singular_name" static';
            throw new AdvancedContentException($msg);
        }
        
        // TODO Check this works..
        $this->extend('updateCheckCustomBlocks', $list);
    }
    
}
