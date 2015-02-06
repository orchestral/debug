Artisan Debug Profiler for Laravel
==============

Debug Component is commandline profiling package for Laravel, It was based from Laravel 4.1 commandline profiling tool which was merged with `php artisan tail`.

[![Latest Stable Version](https://img.shields.io/github/release/orchestral/debug.svg?style=flat)](https://packagist.org/packages/orchestra/debug)
[![Total Downloads](https://img.shields.io/packagist/dt/orchestra/debug.svg?style=flat)](https://packagist.org/packages/orchestra/debug)
[![MIT License](https://img.shields.io/packagist/l/orchestra/debug.svg?style=flat)](https://packagist.org/packages/orchestra/debug)
[![Build Status](https://img.shields.io/travis/orchestral/debug/2.1.svg?style=flat)](https://travis-ci.org/orchestral/debug)
[![Coverage Status](https://img.shields.io/coveralls/orchestral/debug/2.1.svg?style=flat)](https://coveralls.io/r/orchestral/debug?branch=2.1)
[![Scrutinizer Quality Score](https://img.shields.io/scrutinizer/g/orchestral/debug/2.1.svg?style=flat)](https://scrutinizer-ci.com/g/orchestral/debug/)

## Table of Content

* [Version Compatibility](#version-compatibility)
* [Installation](#installation)
* [Configuration](#configuration)
* [Usage](#usage)
* [Resources](#resources)

## Version Compatibility

Laravel    | Debug
:----------|:----------
 4.0.x     | 2.0.x
 4.1.x     | 2.1.x

## Installation

To install through composer, simply put the following in your `composer.json` file:

```json
{
	"require": {
		"orchestra/debug": "2.1.*"
	}
}
```

And then run `composer install` from the terminal.

### Quick Installation

Above installation can also be simplify by using the following command:

    composer require "orchestra/debug=2.1.*"

## Configuration

Add following service providers in `app/config/app.php`.

```php
'providers' => array(

	// ...

	'Orchestra\Debug\DebugServiceProvider',
	'Orchestra\Debug\CommandServiceProvider',
),
```

### Aliases

You could also create an alias for `Orchestra\Debug\Facades\Profiler` in `app/config/app.php`.

```php
'aliases' => array(

    // ...

	'Profiler' => 'Orchestra\Debug\Facades\Profiler',
),
```

## Usage

### Enabling Profiler

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
* [Change Log](http://orchestraplatform.com/docs/latest/components/debug/changes#v2-1)
