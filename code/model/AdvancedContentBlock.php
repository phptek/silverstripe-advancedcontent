<?php

/**
 * Advanced Content Block.
 * Wraps / proxies all native SilverStripe and module-specific DataObject subclasses, for manipulation via the GridField.
 * 
 * @package silverstripe-advancedcontent
 * @subpackage model
 * @author Russell Michell 2016 <russ@theruss.com>
 * @todo Add a cleanup routine in onBeforeWrite for unused, stored attribute data. Use YML config to _enable_ this behaviour.
 */
class AdvancedContentBlock extends DataObject
{

    /**
     * The label
     * 
     * @var string
     */
    private static $singular_name = 'Block';

    /**
     * @var string
     */
    private static $plural_name = 'Blocks';
    
    /**
     * @var array
     * @todo Rename "BlockXX" fields to "Partner" or "SSObject" etc
     */
    private static $db = [
        'BlockID'       => 'Int',
        'BlockClass'    => 'Varchar(255)',
        'BlockSort'     => 'Int',
        'AttributeData' => 'SimpleJSONText' // All "Attributes" are stored to a JSON structure
    ];

    /**
     * An AdvancedContentBlock can be related to a "parent" Page or DataObject
     * @var array
     */
    private static $has_one = [
        'ParentPage' => 'SiteTree',
        'ParentData' => 'DataObject'
    ];

    /**
     * Group relations used when "Permissions" attribute is configured.
     * 
     * @var array
     */
    private static $many_many = [
        'ViewGroups'    => 'Group',
        'EditGroups'    => 'Group'
    ];

    /**
     * @var string
     */
    private static $default_sort = 'BlockSort';

    /**
     * @return array
     */
    public function summaryFields()
    {
        return [
            'summaryForBlockClass'  => 'Block type',
            'summaryForBlockLabel'  => 'Label',
            'Created'               => 'Created',
            'LastEdited'            => 'Last edited'
        ];
    }

    /**
     * @return string
     */
    protected function summaryForBlockClass()
    {
        $blockClass = $this->getField('BlockClass');
        $blockTypes = $this->acService->getBlockTypes();
        
        return $blockTypes
            ->find('ClassName', $blockClass)
            ->SingularName;
    }

    /**
     * @return string
     */
    protected function summaryForBlockLabel()
    {
        return $this->getTitle();
    }

    /**
     * Merge together this object's native fields, those fields declared on this object's proxied object
     * as well as those declared on individual proxied-object's attribute(s).
     * 
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        // Remove _this_ object's redundant fields (Their values are set in onBeforeWrite()
        $fields->removeByName('BlockID');
        $fields->removeByName('BlockSort');
        $fields->removeByName('ParentPageID');
        $fields->removeByName('ParentDataID');
        $fields->removeByName('ViewGroups');
        $fields->removeByName('EditGroups');

        $blockTypes = $this->acService->getBlockTypes()->map('ClassName', 'SingularName');
        $blockTypeField = DropdownField::create(
            'BlockClass',
            'Block type',
            $blockTypes
        );
        
        $fields->addFieldToTab('Root.Main', $blockTypeField);

        $blockRecordClass = $this->getField('BlockClass');
        if ($blockRecordClass && $this->exists()) {
            $blockRecord = $this->getProxiedObject();
            
            // Now that a block class selection exists, we don't need it anymore.
            $fields->dataFieldByName('BlockClass')
                ->setDisabled(true)
                ->setReadonly(true)
                ->setEmptyString('dummy'); // The only way to disable validation
            
            $cmsFields = $blockRecordClass::create()->getCMSFields();
            // Populate the proxied model field's data
            if ($blockRecord) {
                $cmsFields->setValues($blockRecord->toMap());
            }
            $attFields = $this->attributeControls(AdvancedContentAttribute::ADV_ATTR_TYPE_FORM)->toArray();
            $blockFields = FieldList::create(array_merge($cmsFields->toArray(), $attFields));
            
            // Add the block-class's fields along with those of its attribute(s)
            $fields->addFieldsToTab('Root.Main', $blockFields);
        }

        return $fields;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        if ($object = $this->getProxiedObject()) {
            return $object->getField('Title');
        }

        return '';
    }

    /**
     * Does this object have a related and named {@link AdvancedContentAttribute}?
     * 
     * @param string $attrName
     * @return boolean
     */
    public function hasAttribute($attrName)
    {
        $proxiedAttrs = $this->getAttributes();
        $hasAttr = false;
        foreach ($proxiedAttrs as $className => $instArray) {
            array_walk($instArray, function ($v, $k) use ($attrName, &$hasAttr) {
                list($base, $matchTarget) = explode('_', $v->class);
                if ($matchTarget === $attrName) {
                    // assignment on break
                    return $hasAttr = true;
                }
            });
        }
        
        return $hasAttr;
    }

    /**
     * Set the "Attributes" field value to our JSON storage. Used in onBeforeWrite().
     *
     * @return null|void
     */
    private function setAttributeFieldValue()
    {
        // Get all our attribute objects
        $proxiedAttrs = $this->getAttributes();
        
        $data = [];
        foreach ($proxiedAttrs as $attrObjArr) {
            foreach ($attrObjArr as $attrObj) {
                $dataKey = $attrObj->getFieldName();
                $dataVal = $this->getRequest()->postVar($dataKey);
                $data[$dataKey] = $dataVal;
            }
        }
        
        $json = $this->dbObject('AttributeData')->toJson($data);
        $this->setField('AttributeData', $json);
    }

    /**
     * Returns the current value for the JSON node keyed by $attrName (The returned name of the applicable
     * {@link AdvancedContentAttribute} subclass).
     * 
     * @param string $attrName
     * @return array
     */
    public function getAttributeValueFor($attrName)
    {
        return $this->dbObject('AttributeData')->getValueForKey($attrName);
    }

    /**
     * Returns the object that ths AdvancedContentBlock proxies.
     * @return mixed null|DataObject
     */
    public function getProxiedObject()
    {
        $proxiedBlockClass = $this->getField('BlockClass');
        $proxiedBlockID = $this->getField('BlockID');
        
        if ($proxiedBlockClass && $proxiedBlockID) {
            return DataObject::get_by_id($proxiedBlockClass, $proxiedBlockID);
        }
        
        return null;
    }

    /**
     * Write a record of the proxied object before writing to the current object.
     * "BlockClass" is already dealt with via {@link AdvancedContentBlockCreationController::handleAdd()}.
     * @return void
     */
    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        $blockRecordClass = $this->getField('BlockClass');
        
        if (!$blockRecord = $this->getProxiedObject()) {
            $blockRecord = $blockRecordClass::create();    
        }
        
        foreach ($this->getRequest()->postVars() as $field => $value) {
            if ($blockRecord->db($field)) {
                $blockRecord->setField($field, $value);
            }
        }

        // Write it
        $proxiedId = $blockRecord->write();
        $this->setField('BlockID', $proxiedId);
        $this->setAttributeFieldValue();
    }

    /**
     * Ensure the related item is also deleted.
     */
    public function onBeforeDelete()
    {
        parent::onBeforeDelete();
        
        $proxied = $this->getProxiedObject();
        
        // Core SS types are decorated so we know we can call this
        $isCoreType = in_array($proxied->getField('ClassName'), $this->acService->ssCoreTypes());
        if($isCoreType && $proxied->canDeleteOnBlockDelete()) {
            $proxied->delete();
        }
    }

    /**
     * @return SS_HTTPRequest
     * @todo This is bad
     */
    public function getRequest()
    {
        return Controller::curr()->getRequest();
    }

    /**
     * @param Member $member
     * @return boolean
     */
    public function canView($member = null)
    {
        // No related object at this point, just return true
        if (!$proxied = $this->getProxiedObject()) {
            return true;
        }
        
        if (!$member) {
            $member = Member::currentUser();
        }
        
        if (Permission::check('ADMIN', 'any', $member)) {
            return true;
        }
        
        $attributes = $this->getAttributesFor($proxied->class);
        foreach ($attributes as $attribute) {
            // If even _one_ attribute returns false for its canView(), then the block is not viewable.
            if (!$attribute->canView($member)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * @param Member $member
     * @return boolean
     */
    public function canEdit($member = null)
    {
        // No related object at this point, just return true
        if (!$proxied = $this->getProxiedObject()) {
            return true;
        }
        
        $attributes = $this->getAttributesFor($proxied->class);
        foreach ($attributes as $attribute) {
            // If even _one_ attribute returns false for its canEdit(), then the block is not editable.
            if ($attribute->canEdit($member) !== true) {
                return false;
            }
        }

        return $this->canView($member);
    }

    /**
     * @param Member $member
     * @return boolean
     */
    public function canDelete($member = null)
    {
        // No related object at this point, just return true
        if (!$proxied = $this->getProxiedObject()) {
            return true;
        }
        
        $attributes = $this->getAttributesFor($proxied->class);
        foreach ($attributes as $attribute) {
            // If even _one_ attribute returns false for its canDelete(), then the block is not deletable.
            if ($attribute->canDelete($member) !== true) {
                return false;
            }
        }

        return $this->canView($member);
    }

    /**
     * Returns an array of {@link AdvancedContentAttribute} subclasses enabled via YML for the related block object.
     * 
     * @return array $list
     * @todo Cache
     */
    public function getAttributes()
    {
        if (!$proxied = $this->getProxiedObject()) {
            return [];
        }
        
        $attrsEnabled = $proxied->config()->attributes_enabled;
        $attrsDisabled = $proxied->config()->attributes_disabled ?: [];
        
        $list = [];
        if ($attrsEnabled) {
            foreach ($attrsEnabled as $attrClass) {
                // Attributes are enabled by default in module YML, but may also be _disabled_ in userland YML
                if(!in_array($attrClass, $attrsDisabled)) {
                    list($start, $end) = explode('_', $attrClass);
                    $attrObj = Injector::inst()->createWithArgs($attrClass, [$this, $end, null]);
                    $attrObj->setValueForUserControl($this->getAttributeValueFor($attrObj->getFieldName()));
                    $list[$proxied->class][] = $attrObj;
                }
            }
        }
        
        return $list;
    }

    /**
     * Return an array of {@link AdvancedContentAttribute} objects, configured on this block for the passed $className.
     * 
     * @param string $className
     * @return array
     */
    public function getAttributesFor($className)
    {
        $attributeData = $this->getAttributes();
        if (!isset($attributeData[$className])) {
            return [];
        }
        
        return $attributeData[$className];
    }
    
    /**
     * Template method for each of the {@link AdvancedContentAttributes} registered with this block's proxied object.
     * returns a list of UI controls (form fields), supplied by the attribute subclass itself.
     *
     * @param int $context 
     * @return FieldList
     * @see Defined constants on {@link AdvancedContentAttribute}.
     */
    public function attributeControls($context)
    {
        $list = FieldList::create();
        $proxiedAttrs = $this->getAttributes();
        
        foreach ($proxiedAttrs as $proxiedClass => $attrList) {
            foreach ($attrList as $attrObj) {
                $type = $attrObj->getType();
                $control = $attrObj->UserControl();
                if (($type != $context)) {
                    continue;
                }
                
                if ($control instanceof FieldList) {
                    foreach ($control as $field) {
                        $list->push($field);
                    }
                } else {
                    $list->push($control);
                }

            }
        }
        
        if ($list->count()) {
            $list->unshift(HeaderField::create(
                'AttributeHeading',
                _t('AdvancedContent.AdvancedContentBlock.Labels.Attributes.Heading', 'Block attributes'),
                4
            ));
        }
        
        return $list;
    }

    /**
     * Template method used to render each block within a frontend list.
     *
     * @return mixed null|HTMLText
     */
    public function BlockView()
    {
        if ($proxiedBlock = $this->getProxiedObject()) {
            // For search results and other contexts, cast as HTMLText so {@link Text::ContextSummary)} can be called.
            $content = $proxiedBlock->Content();
            if (!$content instanceof HTMLText) {
                $htmlTextObj = HTMLText::create();
                $htmlTextObj->setValue($content);
                $content = $htmlTextObj->getValue();
            }
            
            return $content;
        }

        return null;
    }

}
