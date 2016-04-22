<?php

/**
 * Simply allows CMS authors to restrict viewing of this block to one or more CMS security groups.
 * 
 * @package silverstripe-advancedcontent
 * @subpackage attributes
 * @author Russell Michell 2016 <russ@theruss.com>
 * @todo Ensure versioned DataObjects are used and set canPublish() accordingly
 */
class AdvancedContentAttribute_PermissionsCanView extends AdvancedContentAttribute
{
    /**
     * @inheritdoc
     */
    public function getType()
    {
        return self::ADV_ATTR_TYPE_FORM;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return 'Block View Permissions';
    }

    /**
     * @inheritdoc
     */
    public function UserControl($useField = 'TextField', $useName = '', array $config = [])
    {
        $field = parent::UserControl('ListboxField', 'ViewGroups');
        $field->setSource(Group::get()->map()->toArray());
        $field->setMultiple(true);

        return $field;
    }

    /**
     * @inheritdoc
     */
    public function canView(Member $member = null)
    {
        if (!$member) {
            return parent::canView($member);
        }
        
        $memberId = $member->getField('ID');
        $groupIds = $this->contentObject->getManyManyComponents('ViewGroups')->column();
        
        return in_array($memberId, $groupIds);
    }

}
