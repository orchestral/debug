Artisan Debug Profiler for Laravel
==============

Debug Component is commandline profiling package for Laravel, It was based from Laravel 4.1 commandline profiling tool which was merged with `php artisan tail`.

[![Latest Stable Version](https://img.shields.io/github/release/orchestral/debug.svg?style=flat-square)](https://packagist.org/packages/orchestra/debug)
[![Total Downloads](https://img.shields.io/packagist/dt/orchestra/debug.svg?style=flat-square)](https://packagist.org/packages/orchestra/debug)
[![MIT License](https://img.shields.io/packagist/l/orchestra/debug.svg?style=flat-square)](https://packagist.org/packages/orchestra/debug)
[![Build Status](https://img.shields.io/travis/orchestral/debug/master.svg?style=flat-square)](https://travis-ci.org/orchestral/debug)
[![Coverage Status](https://img.shields.io/coveralls/orchestral/debug/master.svg?style=flat-square)](https://coveralls.io/r/orchestral/debug?branch=master)
[![Scrutinizer Quality Score](https://img.shields.io/scrutinizer/g/orchestral/debug/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/orchestral/debug/)

## Table of Content

* [Version Compatibility](#version-compatibility)
* [Installation](#installation)
* [Configuration](#configuration)
* [Usage](#usage)
* [Resources](#resources)
* [Change Log](https://github.com/orchestral/debug/releases)

## Version Compatibility

Laravel    | Debug
:----------|:----------
 4.0.x     | 2.0.x
 4.1.x     | 2.1.x
 4.2.x     | 2.2.x
 5.0.x     | 3.0.x
 5.1.x     | 3.1.x
 5.2.x     | 3.2.x
 5.3.x     | 3.3.x
 5.4.x     | 3.4.x
 5.5.x     | 3.5.x@dev

## Installation

To install through composer, simply put the following in your `composer.json` file:

```json
{
	"require": {
		"orchestra/debug": "~3.0"
	}
}
```

And then run `composer install` from the terminal.

### Quick Installation

Above installation can also be simplify by using the following command:

    composer require "orchestra/debug=~3.0"

## Configuration

Add following service providers in `config/app.php`.

```php
'providers' => [

	// ...

	Orchestra\Debug\DebugServiceProvider::class,
	Orchestra\Debug\CommandServiceProvider::class,
],
```

### Aliases

You could also create an alias for `Orchestra\Support\Facades\Profiler` in `config/app.php`.

```php
'aliases' => [

    // ...

	'Profiler' => Orchestra\Support\Facades\Profiler::class,
],
```

## Usage

### Enabling Profiler

To enable the profiler, all you need to do is:

```php
Profiler::attachDebugger();
```

> This normally would goes in your development environment such as `local` environment, in the case `app/Providers/AppServiceProvider.php` would be an ideal location to include the command.

### Viewing the Profiler

To view the profiler, run the following command in your terminal:

```bash
php artisan debug
```

## Resources

* [Documentation](http://orchestraplatform.com/docs/latest/components/debug)
