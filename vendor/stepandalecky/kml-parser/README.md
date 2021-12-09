# Quickstart

[![Latest Stable Version](https://poser.pugx.org/stepandalecky/kml-parser/v/stable)](https://packagist.org/packages/stepandalecky/kml-parser)
[![License](https://poser.pugx.org/stepandalecky/kml-parser/license)](https://packagist.org/packages/stepandalecky/kml-parser)

The most intuitive KML parser. Each part of the file is represented by a special class,
so everything you need is accessible via predictable and hinted methods.

## Installation
Using [composer](https://getcomposer.org/):
```
composer require stepandalecky/kml-parser
```

## Usage
A simple example, how to use the parser:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://www.opengis.net/kml/2.2" id="test">
  <Document>
    <name>Document name</name>
    <description>Description</description>
    <Style id="icon-1899-0288D1-normal">
      # ...
    </Style>
    <Style id="icon-1899-0288D1-highlight">
      # ...
    </Style>
    <StyleMap id="icon-1899-0288D1">
      # ...
    </StyleMap>
    <Folder>
      <name>Unnamed layer</name>
      # ...
    </Folder>
  </Document>
</kml>
```

```php
use StepanDalecky\KmlParser\Parser;

$parser = Parser::fromFile('file.xml');
// $parser = Parser::fromString($xmlString);

$kml = $parser->getKml();
$document = $kml->getDocument();

$styles = $document->getStyles();
echo $styles[0]->getId(); // icon-1899-0288D1-normal

$folders = $document->getFolders();
echo $folders[0]->getName(); // Unnamed layer
```

For more examples see the tests.

## Unsupported KML Elements?

Custom development of this library is not planned anymore.
There are two ways to deal with elements that have not been covered yet:

1. Fork this library, add elements you need and create a merge request back to this library.
2. Use emergency method `Entity::getElement()`.
