# Developer API

The module offers a developer API that allows developers to craft their own custom content blocks. The types of blocks that are possible
are pretty much limitless, but as a starter for 10:

* Arbitrary CMS configurable ORM queries
* 3rd party API data
* Multi-column layouts

## Creating a custom block

An advanced content block isn't just one class, it's a `DataObject` subclass that implements `AdvancedContentBlockProvider` related to
an instance of `AdvancedContentBlock`. Each "block" is also configurable via YML with 1 or more "Attributes", where an attribute subclasses
the abstract `AdvancedContentAttribute` class. (See `code/attributes` for pre-configured attributes).

In addition, a custom block should define a `$singular_name` as a private static (SilverStripe config variable) 
at which point the block will show in the "Block type" dropdown in the CMS.

### Example

    class MyObject extends Databject implements AdvancedContentBlockProvider
    {
        /**
         * @var string
         */
         private static $db = [
            'MyField' => 'Varchar(44)'
         ];
    
        /**
         * @var string
         */
         private static $singular_name = 'My Block';
         
         /**
          * @return FieldList
          */
          public function getCMSFields()
          {
            $this->addFieldToTab('Root.Main', TextField::create('MyField'));
          }
    }
    
## Creating a custom attribute

Each block can have 0, 1 or many attributes. An attribute is simply a discreet piece of functionality conferred upon
each block, if enabled. 

The module comes with some attibutes configured on all blocks by default. Each one can be explicitly disabled in your
project's YML config thus:

    # Out of the box, the AdvancedContentAttribute_BlockCSS attribute is enabled.
    # Here's how to disable it:
    MyObjectUsedAsABlock:
      attributes_disabled:
        - AdvancedContentAttribute_BlockCSS

As well as enabling and disabling attributes, developers can also create custom attributes. Simple create a new class
that subclasses the `AdvancedContentAttribute` abstract class, and ensure all necessary methods are declared.

You can see how attributes are called and used by looking at the `AdvancedContentBlock` class.
    
## Service

There is also a small service you can make arbitrary calls to, which allows developers access to arbitrary module logic.

As per the `Injector` docs, all services are injected either via YML-based config or using a private static. [../../_config/dependencies.yml](See dependencies.yml for an example).
