## Storm Inherit relations

This plugin provides a behavior for [WinterCMS's Storm library](https://github.com/wintercms/storm) based database models.

Default, when you extend base models, you loose all relationships defined in the
parent model. Circumstances exist where you don't wish to lose access to those relationships
but to improve upon it, continueing working on a core concept, adding features
as you go up the inheritance tree where the needed objects differ.

### For example(skip if you just want to know how to work with it)

In Magento you have AttributeValues. Each Attribute value has 
a relation AttributeName.  
This would be one base model with a belongsTo relationship, 
we'll call this `AttributeValue`.

Then, a value can belong to a different type of Model. A `Product`, a `Quote`, 
a `Customer`, etc...   
Values belonging one type cannot belong to another type, 
we'll call this `OwnedBy[Type]`.  
This would be a `Model` extending the `AttributeValue`, 
adding the `$belongsTo` relationship to it's "owner type"

Then there are multiple different datatypes possible, each stored within 
their own table(Don't ask why, Magento chose this method)  
So integer values have their table, datetime values have their tables, 
decimals have their tables, etc...  
These value types would then extend the `OwnedBy[Type]`, changing just the 
table name that stores the values.  
We would call this `OwnedBy[Type][ValueType]`
If you do the math you can see that this is an exponential amount of files
to generate and modify.

**Alternative solution**

This would be a use case where inheriting relationships from the parents 
throughout the layers would be ideal.  
Of course you can modify the constructors to read the data from the parent 
classes, add it to the current class.  
That would mean copy pasting this code like 25 times, 
and wouldn't be very [DRY](https://en.wikipedia.org/wiki/Don%27t_repeat_yourself)

**My solution**

I'm a coder, I'm lazy and don't like copy pasting the same thing ove rand over.
I would propose having an implementation that you can inject in a class you deem needing it.   
For example the utmost base class AttributeValue, this ensures all 
the children have it, and it doesn't need to be copy pasted over and 
over again.

## Using the Relationship Inheritance.

### Installation

```shell
composer require tschallacka/storm-inherit-relations ^1.0
```

### Usage

In a model in which you wish to glean the relationships defined in 
the parents add this to your `$implements` array:

```php
public $implement = [\Tschallacka\StormInheritRelations\Behavior\InheritRelations];
```

Model example:

```php
<?php
namespace Tschallacka\Example\Attribute;

use Winter\Storm\Database\Model;
use Tschallacka\StormInheritRelations\Behavior\InheritRelations;

class AttributeValue extends Model
{
    public $primaryKey = 'value_id';

    public $implement = [
        InheritRelations::class
    ];

    public $belongsTo = [
        'attribute' => AttributeName::class,
    ];
}
```
