<?php
/**
 * Decorates {@link File}. This ensures the module has a common API to work with for native and module-specific DataObjects.
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

    /**
     * Dedicated method to show a title, useful for search results for example.
     *
     * @todo Extend DataExtension for common Native/Module object logic
     * @return string
     */
 //   public function Title()
 //   {
/*        if (!$block = $this->getBlock()) {
            return '';
        }

        return $block->ParentPage()->Title;*/
  //  }

    /**
     * Dedicated method to show a link, useful for search results for example.
     *
     * @todo Extend DataExtension for common Native/Module object logic
     * @return string
     */
//    public function Link()
//    {
/*        if (!$block = $this->getBlock()) {
            return '';
        }

        return $block->ParentPage()->Link();*/
  //  }
    
    /**
     * @todo
     * Needs to be 
     */
    public function Content()
    {
        return $this->owner->getFilename();
    }
    
}
