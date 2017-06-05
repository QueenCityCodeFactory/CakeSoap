# CakeSoap

## Requirements
* CakePHP 3.1+

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require queencitycodefactory/cakesoap
```

You can also add `"queencitycodefactory/cakesoap" : "dev-master"` to `require` section in your application's `composer.json`. Use for latest updates.

-- or --

You can also add `"queencitycodefactory/cakesoap" : "~3.3"` to `require` section in your application's `composer.json`. Stable Release.

## Usage

Include the CakeSoap library files:
```php
    use CakeSoap\Network\CakeSoap;
```

### Sample Request

```php
    $soap = new CakeSoap([
        'wsdl' => 'http://mydomain.com/api?wsdl',
        'userAgent' => 'MySoapClientAgent'
    ]);

    $response = $soap->sendRequest($action, [
        'SomeData' => [
            'SomeSubData' => 'item1',
            'SomeMoreSubData' => 'item2'
        ]
    ]);
```
