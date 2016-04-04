<?php

/**
 * Simple field for storing, writing-to and querying JSON formatted data. The field is "simple" in that it does not 
 * allow for multidimensional data.
 * 
 * Note: All getXX(), first(). nth() and last() methods will return `null` if no result is found. This behaviour 
 * may change in future versions, but will likely be governed by config settings.
 * 
 * @package silverstripe-advancedcontent
 * @subpackage fields
 * @author Russell Michell 2016 <russ@theruss.com>
 * @todo Put into own package ala phptek/silverstripe-simplejsontext
 *     in MySQL or PostGres
 * @todo Add a deleteForKey() method
 */
class SimpleJSONText extends StringField
{
    /**
     * Returns an input field.
     *
     * @param string $name
     * @param null|string $title
     * @param string $value
     */
    public function __construct($name, $title = null, $value = '')
    {
        parent::__construct($name, $title, $value);
    }
    
    /**
     * Taken from {@link TextField}.
     * @see DBField::requireField()
     * @return void
     */
    public function requireField()
    {
        $parts = [
            'datatype'      => 'mediumtext',
            'character set' => 'utf8',
            'collate'       => 'utf8_general_ci',
            'arrayValue'    => $this->arrayValue
        ];

        $values = [
            'type'  => 'text',
            'parts' => $parts
        ];

        DB::require_field($this->tableName, $this->name, $values, $this->default);
    }

    /**
     * @param string $title
     * @return HiddenField
     */
    public function scaffoldSearchField($title = null)
    {
        return HiddenField::create($this->getName());
    }
    
    /**
     * @param string $title
     * @return HiddenField
     */
    public function scaffoldFormField($title = null)
    {
        return HiddenField::create($this->getName());
    }

    /**
     * Returns the value of this field as an associative array
     * @return array
     * @throws AdvancedContentException 
     */
    public function getValueAsArray()
    {
        if (!$value = $this->getValue()) {
            return [];
        }
        
        if (!$this->isJson($value)) {
            $msg = 'DB data is munged.';
            throw new SimpleJSONException($msg);
        }
        
        if (!$decoded = json_decode($value, true)) {
            return [];
        }

        if (!is_array($decoded)) {
            $decoded = (array) $decoded;
        }
        
        return $decoded;
    }

    /**
     * @param mixed string|int
     * @return mixed
     */
    public function getValueForKey($key)
    {
        $currentData = $this->getValueAsArray();
        if (isset($currentData[$key])) {
            return $currentData[$key];
        }

        return null;
    }

    /**
     * Set a value for a specific JSON key, and return the lot back so that it ca be written to the field by
     * calling logic.
     * 
     * @param mixed $key The JSON key who's value should be modified with $value.
     * @param array $value The array of data that should update existing field-data identified by $key.
     * @return string
     */
    public function setValueForKey($key, $value)
    {
        $currentData = $this->getValueAsArray();
        $currentData[$key] = $value;
        
        $isValid = is_array($currentData) && count($currentData);
        $array = [];
        
        // Cleanup
        if ($isValid) {
            array_walk_recursive($currentData, function ($v, $k) use (&$array) {
                $array[$k] = stripslashes($v);
            });
        }

        return $this->toJson($array);
    }

    /**
     * Utility method to determine whether the data is really JSON or not.
     * 
     * @param string $value
     * @return boolean
     */
    public function isJson($value)
    {
        return !is_null(json_decode($value, true));
    }

    /**
     * @param array $value
     * @return mixed null|string
     */
    public function toJson($value)
    {
        if (!is_array($value)) {
            $value = (array) $value;
        }
        
        $opts = (
            JSON_UNESCAPED_SLASHES
        );
        
        return json_encode($value, $opts);
    }
    
    /**
     * Return an array of the JSON key + value represented as first JSON node. 
     * 
     * @return mixed null|array
     */
    public function first()
    {
        $data = $this->getValueAsArray();
        
        if (!$data) {
            return null;
        }
        
        return array_slice($data, 0, 1, true);
    }

    /**
     * Return an array of the JSON key + value represented as last JSON node.
     *
     * @return mixed null|array
     */
    public function last()
    {
        $data = $this->getValueAsArray();

        if (!$data) {
            return null;
        }

        return array_slice($data, -1, 1, true);
    }

    /**
     * Return an array of the JSON key + value represented as the $n'th JSON node.
     *
     * @param int $n
     * @return mixed null|array
     * @throws SimpleJSONException
     */
    public function nth($n)
    {
        $data = $this->getValueAsArray();

        if (!$data) {
            return null;
        }
        
        if (!is_numeric($n)) {
            $msg = 'Argument passed to ' . __FUNCTION__ . ' must be numeric.';
            throw new SimpleJSONException($msg);
        }
        
        if (!isset(array_values($data)[$n])) {
            return null;
        }

        return array_slice($data, $n, 1, true);
    }

    /**
     * Return an array of the JSON key(s) + value(s) represented when $value is found in a JSON node's value
     *
     * @param string $value
     * @return mixed null|array
     * @throws SimpleJSONException
     */
    public function find($value)
    {
        $data = $this->getValueAsArray();

        if (!$data) {
            return null;
        }
        
        if (!is_scalar($value)) {
            $msg = 'Argument passed to ' . __FUNCTION__ . ' must be a scalar.';
            throw new SimpleJSONException($msg);
        }
        
        return null;

       // array_search($value, $data);
        //array_keys
    }

}

/**
 * @package silverstripe-advancedcontent
 * @author Russell Michell 2016 <russ@theruss.com>
 */
class SimpleJSONException extends Exception
{
}
