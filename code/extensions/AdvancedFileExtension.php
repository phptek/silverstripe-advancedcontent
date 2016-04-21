<?php
/**
 * Decorates {@link File} and subclasses to confer specific behaviour onto these objects.
 *
 * @package silverstripe-advancedcontent
 * @author Russell Michell 2016 <russ@theruss.com>
 */

class AdvancedContentFileExtension extends DataExtension
{

    /**
     * Called when an {@link AdvancedContentBlock} is deleted in the CMS. 
     * Checks the system to see if this {@link File} object is used anywhere. If it isn't, it can be safely deleted,
     * otherwise we leave it alone.
     * 
     * @param Member|null $member   An optional {@link Member} object. 
     * @return boolean
     */
    public function canDeleteOnBlockDelete(Member $member = null)
    {
        return false;
    }

    /**
     * @todo
     */
    public function BlockView()
    {
        return $this->owner->getFilename();
    }
    
    /**
     * @return boolean
     */
    private function isImage()
    {
        return in_array($this->owner->class, ClassInfo::subclassesFor('Image'));
    }

    /**
     * @return boolean
     */
    private function isFile()
    {
        return !$this->isImage() && in_array($this->owner->class, ClassInfo::subclassesFor('File'));
    }
    
}
