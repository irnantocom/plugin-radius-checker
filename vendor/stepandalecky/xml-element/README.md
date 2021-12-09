# Quickstart

[![Latest Stable Version](https://poser.pugx.org/stepandalecky/xml-element/v/stable)](https://packagist.org/packages/stepandalecky/xml-element)
[![License](https://poser.pugx.org/stepandalecky/xml-element/license)](https://packagist.org/packages/stepandalecky/xml-element)

Read XML in a more convenient way.

_StepanDalecky/xml-element_ is:
* comfortable to use,
* easy to iterate on,
* keeping XML structure under control,
* predictable.

## Installation
Using [composer](https://getcomposer.org/):
```
composer require stepandalecky/xml-element
```

## Usage

```xml
<grandpa name="Splinter" species="rat">
	<father name="Donatello">
		<sohn>me</sohn>
	</father>
	<uncle name="Michelangelo"></uncle>
	<uncle name="Leonardo"></uncle>
</grandpa>
```

```php
use StepanDalecky\XmlElement\Element;

$grandpa = Element::fromString($xmlString);

$grandpa->getAttributes(); // returns ['name' => 'Splinter', 'species' => 'rat']
$grandpa->getAttribute('name'); // returns 'Splinter'

$grandpa->getChild('father')
	->getChild('sohn')
	->getValue(); // returns 'me'
$grandpa->getChild('mother'); // throws an exception - not found
$grandpa->hasChild('mother'); // returns false
$grandpa->getChild('uncle'); // throws an exception - more children found
$grandpa->hasChild('uncle'); // returns true

$grandpa->getChildren('uncle'); // returns an array consisting of two elements
```