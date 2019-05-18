# Poignant

[![Latest Stable Version](https://poser.pugx.org/mossengine/poignant/v/stable)](https://packagist.org/packages/mossengine/poignant)
[![Latest Unstable Version](https://poser.pugx.org/mossengine/poignant/v/unstable)](https://packagist.org/packages/mossengine/poignant)
[![License](https://poser.pugx.org/mossengine/poignant/license)](https://packagist.org/packages/mossengine/poignant)
[![composer.lock](https://poser.pugx.org/mossengine/poignant/composerlock)](https://packagist.org/packages/mossengine/poignant)

[![Build Status](https://travis-ci.org/Mossengine/Poignant.svg?branch=master)](https://travis-ci.org/Mossengine/Poignant)
[![codecov](https://codecov.io/gh/Mossengine/Poignant/branch/master/graph/badge.svg)](https://codecov.io/gh/Mossengine/Poignant)

[![Total Downloads](https://poser.pugx.org/mossengine/poignant/downloads)](https://packagist.org/packages/mossengine/poignant)
[![Monthly Downloads](https://poser.pugx.org/mossengine/poignant/d/monthly)](https://packagist.org/packages/mossengine/poignant)
[![Daily Downloads](https://poser.pugx.org/mossengine/poignant/d/daily)](https://packagist.org/packages/mossengine/poignant)

PHP Class to provide quick validation building and execution logic based on validation results. 


## Functions
### __constructor()
```php
<?php
// Currently no constructor but one will be here soon to support limits and settings.
```

## Installation

### With Composer

```
$ composer require mossengine/poignant
```

```json
{
    "require": {
        "mossengine/poignant": "^1.0.0"
    }
}
```

```php
<?php
// Require the autoloader, normal composer stuff
require 'vendor/autoload.php';

// Instantiate a FiveCode class
$classPoignant = new Mossengine\Poignant\Poignant;

// Execute an array of JCode directly into the class
$classPoignant = Mossengine\Poignant\Poignant::create()
    ->withName(function($condition) {
        return $condition->required();
    });
```


### Without Composer

Why are you not using [composer](http://getcomposer.org/)? Download [Jcode.php](https://github.com/Mossengine/FiveCode/blob/master/src/FiveCode.php) from the repo and save the file into your project path somewhere. This project does not support composerless environments.

### Build and Validate

```php
<?php
$results = null;

$arrayData = [
    'name' => 'Tom'
];

$classPoignant = Mossengine\Poignant\Poignant::create()
    ->withName(function($condition) {
        return $condition->required();
    })
    ->onFail($arrayData, function($mixedResults) use (&$results) {
        $results = $mixedResults;
        return 'fail';
    })
    ->onPass($arrayData, function($mixedResults) use (&$results) {
        $results = $mixedResults;
        return 'pass';
    });

echo json_encode($classPoignant->getResults());
// {"valid":"pass","invalid":null}
```