<?php

/**
 * An attribute is a micro-class containing the minimum of logic to feature a relevant form-field in a CMS block context.
 * By example an attribute might be "permissions", "embargo" "expiry", "cssstyle" etc and is stored in JSON format.
 * 
 * Each attribute provides an UserControl() method used to display a single form-field on each block in the CMS.
 * A block might have 0, 1 or more attribute(s) and can be enabled or disabled on a block-by-block basis through module
 * and userland YML.
 * 
 * The value for each attribute is stored in the related block's "Attributes" field in a simple, structured JSON format.
 * 
 * Questions:
 * 
 * Q: Why not use a SS DataExtension?
 * A:   - I wanted to enforce a specific API via abstract classes, which AFAIK you cannot do using {@link Extension}.
 *      - Extensions cannot be subclassed (Not as at SS 3.0 anyway)
 * Q: Why not use a similar approach to GridField config components instead of baking-in atttributes via YML
 * A: Excellent question. Let's add that to the roadmap then eh?
 *
 * @package silverstripe-advancedcontent
 * @subpackage attributes
 * @author Russell Michell 2016 <russ@theruss.com>
 */
abstract class AdvancedContentAttribute
{
    /**
     * Show this attribute's UI widget within a GridField row.
     * Not implemented yet.
     * 
     * @var int (Bitwise int)
     */
    const ADV_ATTR_TYPE_GRID = 1;

    /**
     * Show this attribute's UI widget within an object's EditField.
     *
     * @var int (Bitwise int)
     */
    const ADV_ATTR_TYPE_FORM = 2;

    /**
     * @var AdvancedContentBlock
     */
    protected $contentObject;

    /**
     * @var string
     */
    protected $attributeName = '';

    /**
     * @var string
     */
    protected $valueForUserControl = '';

    /**
     * @param AdvancedContentBlock $contentBlock
     * @param string $attrName The name of the instantiated attribute after the underscore in its class-name
     * @param mixed $value
     */
    public function __construct(AdvancedContentBlock $contentBlock, $attrName, $value = null)
    {
        $this->contentObject = $contentBlock;
        $this->attributeName = $attrName;
        if ($value) {
            $this->setValueForUserControl($value);
        }
    }

    /**
     * Inform the module what type this attribute is. 
     * Return value must be one of the ADV_ATTR_TYPE_XXX class constants.
     *
     * @return int
     */
    abstract public function getType();

    /**
     * The form-field's CMS label.
     * 
     * @return string
     */
    abstract public function getLabel();

    /**
     * A block is only able to be viewed if each attribute's canView() returns true.
     * 
     * @param Member $member
     * @return boolean
     */
    public function canView(Member $member = null)
    {
        return true;
    }

    /**
     * A block is only able to be edited if each attribute's canEdit() returns true.
     * 
     * @param Member $member
     * @return boolean
     */
    public function canEdit(Member $member = null)
    {
        return true;
    }

    /**
     * A block is only able to be deleted if each attribute's canDelete() returns true.
     * 
     * @param Member $member
     * @return boolean
     */
    public function canDelete(Member $member = null)
    {
        return true;
    }

    /**
     * @param string $value
     * @return AdvancedContentAttribute
     */
    public function setValueForUserControl($value)
    {
        $this->valueForUserControl = $value;
        
        return $this;
    }

    /**
     * @return string
     */
    public function getValueForUserControl()
    {
        return $this->valueForUserControl;
    }
    
    /**
     * Template method to show the UI widget for use on each block either in the GridField row itself, in the form 
     * scaffolded by getCMSFields() or both.
     * 
     * The display location for this field is decided by $this->getType().
     *
     * @param string $useField
     * @param string $useName   If a custom name is used here, then saving to JSON storage is skipped. This can be useful
     *                          when saving data to a relational (has|many) field. See the "PermissionsView" attribute 
     *                          for an example scenario.
     * @param array $config
     * @return mixed FormField | FieldList
     */
    public function UserControl($useField = 'TextField', $useName = '', array $config = [])
    {
        $fieldName = $useName ?: $this->getFieldName();
        $field = $useField::create($fieldName, $this->getLabel());
        $field->addExtraClass($this->getCSSClass());
        if (in_array($useField, ClassInfo::subclassesFor('TextField'))) {
            $field->setValue($this->getValueForUserControl());
        }
        
        if ($config && $field->hasMethod('setConfig')) {
            $field->setConfig($config);
        }

        return $field;
    }

    /**
     * @return string
     */
    public function getCSSClass()
    {
        return strtolower(preg_replace("#[^a-zA-Z]+#", '-', $this->attributeName));
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        return str_replace(' ', '', $this->getLabel());
    }
    
}
