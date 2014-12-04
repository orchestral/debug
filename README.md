Artisan Debug Profiler for Laravel
==============

Debug Component is commandline profiling package for Laravel, It was based from Laravel 4.1 commandline profiling tool which was merged with `php artisan tail`.

[![Latest Stable Version](https://img.shields.io/github/release/orchestral/debug.svg?style=flat)](https://packagist.org/packages/orchestra/debug)
[![Total Downloads](https://img.shields.io/packagist/dt/orchestra/debug.svg?style=flat)](https://packagist.org/packages/orchestra/debug)
[![MIT License](https://img.shields.io/packagist/l/orchestra/debug.svg?style=flat)](https://packagist.org/packages/orchestra/debug)
[![Build Status](https://img.shields.io/travis/orchestral/debug/master.svg?style=flat)](https://travis-ci.org/orchestral/debug)
[![Coverage Status](https://img.shields.io/coveralls/orchestral/debug/master.svg?style=flat)](https://coveralls.io/r/orchestral/debug?branch=master)
[![Scrutinizer Quality Score](https://img.shields.io/scrutinizer/g/orchestral/debug/master.svg?style=flat)](https://scrutinizer-ci.com/g/orchestral/debug/)

## Quick Installation

To install through composer, simply put the following in your `composer.json` file:

```json
{
	"require": {
		"orchestra/debug": "3.0.*"
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

## Resources

* [Documentation](http://orchestraplatform.com/docs/latest/components/debug)
* [Change Log](http://orchestraplatform.com/docs/latest/components/debug/changes#v3-0)
