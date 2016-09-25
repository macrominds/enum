# A simple enum implementation for php that allows for typehinting.

[![Build Status](https://travis-ci.org/macrominds/enum.svg)](https://travis-ci.org/macrominds/enum)

## Usage

### Example

In order to create a blazingly simple Salutation enum, just create it as follows:

```
use macrominds\enum\Enum;
use macrominds\enum\Enumerations;


class Salutation extends Enum
{
	use Enumerations;
    protected static $enums = [
            'MRS' => 1,
            'MR' => 2,
            'MS' => 3
        ];
}
```

If you require complex epressions for your enum values, just take the alternative static method approach and you're fine.

```
//alternative approach

use macrominds\enum\Enum;
use macrominds\enum\Enumerations;

class AnyValueEnum extends Enum
{
	use Enumerations;

    protected static function enums(){
    	return [
    		'String' => 'string',
            'Integer' => 2,
            'Object' => Salutation::MR()
        ];
    }
}
```

That's it. You are now able to typehint your functions and you're sure that you get instances of your custom enum. See tests/EnumTest.php if you're in doubt. It shows which expectations you can make.

```
// example for type hinted function
public function save(Salutation $salutation) {
	saveToDB($salutation->value());
}

// example for fetching the enum from value
public function load($value) {
    // throws \Exception if $value is invalid
    return Salutation::fromValue($value);
}
```

### IDE Support

In order to support code completion, you may want to give hints about the resulting methods:

```
use macrominds\enum\Enum;
use macrominds\enum\Enumerations;

/**
 * @method static \App\Enums\Salutation MRS()
 * @method static \App\Enums\Salutation MR()
 * @method static \App\Enums\Salutation MS()
 */
class Salutation extends Enum
{
    use Enumerations;
    protected static $enums = [
            'MRS' => 1,
            'MR' => 2,
            'MS' => 3
        ];
}
```

## Installation

`composer require macrominds/enum`.

## TODO
- More source documentation.
- Make sure that configured names and values are unique: This is currently the developer's responsibility when creating a custom Enum.
- Make php5.6 compatible
