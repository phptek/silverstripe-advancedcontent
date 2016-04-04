<?php

/**
 * Provides an "in-row" UI for suitable {@link AdvancedContentAttribute} objects to be shown and interacted-with,
 * without having to click into the GridField edit screen itself.
 * 
 * @package silverstripe-advancedcontent
 * @subpackage fields
 * @author Russell Michell 2016 <russ@theruss.com>
 */
class GridFieldAdvancedContentAttributeActions extends Object implements GridField_ColumnProvider
{

    /**
     * @var string
     */
    private static $attribute_action = 'showblockattributes';
    
    /**
     * Add an "Actions" column if it doesn't exist.
     * 
     * @param GridField $gridField
     * @param array $columns
     */
    public function augmentColumns($gridField, &$columns)
    {
        if (!in_array('Actions', $columns)) {
            $columns[] = 'Actions';
        }
    }

    /**
     * Return any special attributes that will be used for FormField::create_tag()
     * 
     * @param GridField $gridField
     * @param DataObject $record
     * @param string $columnName
     * @return array
     */
    public function getColumnAttributes($gridField, $record, $columnName)
    {
        return ['class' => 'col-content-attributes'];
    }

    /**
     * Add the title.
     * 
     * @param GridField $gridField
     * @param string $columnName
     * @return array
     */
    public function getColumnMetadata($gridField, $columnName)
    {
        if ($columnName == 'Actions') {
            return ['title' => ''];
        }
    }

    /**
     * The columns that are handled by this component.
     * 
     * @param GridField $gridField
     * @return array
     * @todo Return an empty array if there are no controls to show. This prevents an empty column showing.
     */
    public function getColumnsHandled($gridField)
    {
        return ['Actions'];
    }

    /**
     * The GridField actions that this component handles.
     * 
     * @param GridField $gridField
     * @return array
     */
    public function getActions($gridField)
    {
        return [self::$attribute_action];
    }

    /**
     * @param GridField $gridField
     * @param DataObject $record
     * @param string $columnName
     * @return string - the HTML for the column
     */
    public function getColumnContent($gridField, $record, $columnName)
    {
        $attributeActionField = GridField_FormAction::create(
            $gridField,
            'ShowBlockAttributes' . $record->ID, 
            false,
            self::$attribute_action,
            ['RecordID' => $record->ID]
        )
            ->addExtraClass('gridfield-button-showblockattributes') // TODO
            ->setAttribute('title', _t('GridAction.SHOWBLOCKATTR_DESCRIPTION','Show block attributes'))
            ->setAttribute('data-icon', 'cross-circle') // TODO
            ->setDescription(_t('GridAction.SHOWBLOCKATTR_DESCRIPTION','Show block attributes'));

        // Get all attribute form UIs available on a GridField
        $uiControls = $record->attributeControls(AdvancedContentAttribute::ADV_ATTR_TYPE_GRID);
        $uiActionLink = Controller::join_links(
            'BlockClass',
            $record->BlockClass,
            'Attributes',
            $attributeActionField->Field()->getValue()
        );
        
        $data = ArrayData::create([
            'AttributeControls' => $uiControls,
            'GridFieldAction'   => $attributeActionField->Field(),
            'ActionLink'        => $gridField->Link($uiActionLink)
        ]);

        return $data->renderWith('GridFieldAdvancedContentAttributeActions');
    }

    /**
     * Handle the actions and apply any changes to the GridField.
     * 
     * @param GridField $gridField
     * @param string $actionName
     * @param mixed $arguments
     * @param array $data - form data
     * @return void
     */
    public function handleAction(GridField $gridField, $actionName, $arguments, $data)
    {
        if ($actionName !== self::$attribute_action) {
            return;
        }
        
    }
}
