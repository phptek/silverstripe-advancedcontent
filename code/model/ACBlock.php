<?php

/**
 * Advanced Content Block.
 * Wraps or "proxies" all native and module-specific DataObject subclasses for manipulation via the ACE.
 * 
 * @package silverstripe-advancedcontent
 * @subpackage model
 * @author Russell Michell 2016 <russ@theruss.com>
 * @todo Add a cleanup routine in onBeforeWrite for unused, stored attribute data. Use YML config to _enable_ this behaviour.
 */
class ACBlock extends DataObject
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
     */
    private static $db = [
        'BlockID'       => 'Int',
        'BlockClass'    => 'Varchar(255)',
        'BlockSort'     => 'Int',
        'Attributes'    => 'SimpleJSONText' // All "Attributes" are stored to a JSON structure
    ];

    /**
     * An ACBlock can be related to a "parent" Page or DataObject
     * @var array
     */
    private static $has_one = [
        'ParentPage' => 'SiteTree',
        'ParentData' => 'DataObject'
    ];

    /**
     * @var string
     */
    private static $default_sort = 'BlockSort';

    /**
     * @var array
     */
    private static $defaults = [
        'BlockSort' => 1
    ];

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

        $blockTypes = $this->acService->getBlockTypes()->map('ClassName', 'SingularName');
        $blockTypeField = DropdownField::create(
            'BlockClass',
            'Block type',
            $blockTypes
        );
        
        $fields->addFieldToTab('Root.Main', $blockTypeField);

        $blockRecordClass = $this->getField('BlockClass');
        if($this->exists() && $blockRecordClass) {
            // Now that a block class selection exists, we don't need it anymore do we..
            $fields->dataFieldByName('BlockClass')
                ->setDisabled(true)
                ->setReadonly(true)
                ->setEmptyString('dummy'); // The only way to disable validation
            
            $cmsFields = $blockRecordClass::create()->getCMSFields()->toArray();
            $attFields = $this->attributeControls(AdvancedContentAttribute::ADV_ATTR_TYPE_FORM)->toArray();
            $blockFields = FieldList::create(array_merge($cmsFields, $attFields));
            
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
     * Set the "Attributes" field value to our JSON storage. Used in onBeforeWrite().
     *
     * @return null|void
     */
    private function setAttributeFieldValue()
    {
        // Get all our attribute objects
        $proxiedAttrs = $this->getProxiedObjectAttributes();
        $data = [];
        foreach ($proxiedAttrs as $attrObjArr) {
            foreach ($attrObjArr as $attrObj) {
                $dataKey = $attrObj->getFieldName();
                $dataVal = $this->getRequest()->postVar($dataKey);
                    $data[] = $this->dbObject('Attributes')->setValueForKey($dataKey, $dataVal);
            }
        }
        
        // This works because setValueForKey() always takes the _entire_ field contents into account and returns it
        $this->setField('Attributes', end($data));
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
        return $this->dbObject('Attributes')->getValueForKey($attrName);
    }

    /**
     * Returns the object that ths ACBlock proxies.
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
     * @return SS_HTTPRequest $request
     * @todo This is bad
     */
    public function getRequest()
    {
        return Controller::curr()->getRequest();
    }

    /**
     * @param Member $member
     * @return boolean
     * @todo Once we implement a "Permission" attribute.
     */
    public function canView($member = null)
    {
        return true;
    }

    /**
     * @param Member $member
     * @return boolean
     * @todo Once we implement a "Permission" attribute.
     */
    public function canEdit($member = null)
    {
        return true;
    }

    /**
     * @param Member $member
     * @return boolean
     * @todo Once we implement a "Permission" attribute.
     */
    public function canDelete($member = null)
    {
        return true;
    }

    /**
     * Returns an array of {@link AdvancedContentAttribute} subclasses enabled via YML for the related block DataObject.
     * 
     * @return mixed
     */
    public function getProxiedObjectAttributes()
    {
        $proxied = $this->getProxiedObject();
        $attrsEnabled = $proxied->config()->attributes_enabled;
        $attrsDisabled = $proxied->config()->attributes_disabled ?: [];
        
        $list = [];
        if ($attrsEnabled) {
            foreach ($attrsEnabled as $attrClass) {
                // Attributes are enabled by default in module YML, but may also be _disabled_ in userland YML
                if(!in_array($attrClass, $attrsDisabled)) {
                    $attrObj = $attrClass::create();
                    $attrObj->setValueForUserControl($this->getAttributeValueFor($attrObj->getFieldName()));
                    $list[$proxied->class][] = $attrObj;
                }
            }
        }
        
        return $list;
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
        $proxiedAttrs = $this->getProxiedObjectAttributes();
        foreach ($proxiedAttrs as $proxiedClass => $attrList) {
            foreach ($attrList as $attrObj) {
                if (($attrObj->getType() == $context)) {
                    $list->push($attrObj->UserControl());
                }
            }
        }
        
        if ($list->count()) {
            $list->unshift(HeaderField::create(
                'AttributeHeading',
                _t('AdvancedContent.ACBlock.Labels.Attributes.Heading', 'Block attributes'),
                4
            ));
        }
        
        return $list;
    }

    /**
     * Template method used to render each block in a frontend list.
     *
     * @return mixed null|HTMLText
     * 
     */
    public function BlockView()
    {
        if ($proxiedBlock = $this->getProxiedObject()) {
            return $proxiedBlock->BlockView();
        }
        
        return null;
    }

}
