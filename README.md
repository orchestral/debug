Artisan Debug Profiler for Laravel 4
==============

`Orchestra\Debug` is commandline profiling package for Laravel 4, It was based from Laravel 4.1 commandline profiling tool which was merged with `php artisan tail`.

[![Latest Stable Version](https://poser.pugx.org/orchestra/debug/v/stable.png)](https://packagist.org/packages/orchestra/debug) 
[![Total Downloads](https://poser.pugx.org/orchestra/debug/downloads.png)](https://packagist.org/packages/orchestra/debug) 
[![Build Status](https://travis-ci.org/orchestral/debug.png?branch=master)](https://travis-ci.org/orchestral/debug) 
[![Coverage Status](https://coveralls.io/repos/orchestral/debug/badge.png?branch=master)](https://coveralls.io/r/orchestral/debug?branch=master) 
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/orchestral/debug/badges/quality-score.png?s=126736312eb50230c0a9216f032def44610f1647)](https://scrutinizer-ci.com/g/orchestral/debug/) 

## Quick Installation

To install through composer, simply put the following in your `composer.json` file:

```json
{
	"require": {
		"orchestra/debug": "2.1.*@dev"
	}
}
```

### Registering the Package

Next add the following service provider in `app/config/app.php`.

```php
'providers' => array(

	// ...
    
	'Orchestra\Debug\DebugServiceProvider',

	'Orchestra\Debug\CommandServiceProvider',
),
```
 
### Adding an Alias
 
You could also create an alias for `Orchestra\Debug\Facades\Profiler` in `app/config/app.php`.
 
```php
'alias' => array(
	'Profiler' => 'Orchestra\Debug\Facades\Profiler',
),
```

## Enabling Profiler

To enable the profiler, all you need to do is:

```php
Profiler::attachDebugger();
```

> This normally would goes in your development environment such as `local` environment, in the case `app/start/local.php` would be an ideal location to include the command.

### Viewing the Profiler

To view the profiler, run the following command in your terminal:

```bash
php artisan debug
```


 
 