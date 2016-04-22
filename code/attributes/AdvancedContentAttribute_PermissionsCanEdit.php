<?php

/**
 * Simply allows CMS authors to restrict viewing of this block to one or more CMS security groups.
 * 
 * @package silverstripe-advancedcontent
 * @subpackage attributes
 * @author Russell Michell 2016 <russ@theruss.com>
 */
class AdvancedContentAttribute_PermissionsCanEdit extends AdvancedContentAttribute
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
        return 'Block Edit Permissions';
    }

    /**
     * @inheritdoc
     */
    public function UserControl($useField = 'TextField', $useName = '', array $config = [])
    {
        $field = parent::UserControl('ListboxField', 'EditGroups');
        $field->setSource(Group::get()->map()->toArray());
        $field->setMultiple(true);

        return $field;
    }

    /**
     * @inheritdoc
     */
    public function canEdit(Member $member = null)
    {
        if (!$member) {
            return parent::canEdit($member);
        }
        
        $memberId = $member->getField('ID');
        $groupIds = $this->contentObject->getManyManyComponents('EditGroups')->column();
        
        return in_array($memberId, $groupIds);
    }

}
