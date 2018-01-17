Artisan Debug Profiler for Laravel
==============

Debug Component is commandline profiling package for Laravel, It was based from Laravel 4.1 commandline profiling tool which was merged with `php artisan tail`.

[![Build Status](https://travis-ci.org/orchestral/debug.svg?branch=master)](https://travis-ci.org/orchestral/debug)
[![Latest Stable Version](https://poser.pugx.org/orchestra/debug/v/stable)](https://packagist.org/packages/orchestra/debug)
[![Total Downloads](https://poser.pugx.org/orchestra/debug/downloads)](https://packagist.org/packages/orchestra/debug)
[![Latest Unstable Version](https://poser.pugx.org/orchestra/debug/v/unstable)](https://packagist.org/packages/orchestra/debug)
[![License](https://poser.pugx.org/orchestra/debug/license)](https://packagist.org/packages/orchestra/debug)

## Table of Content

* [Version Compatibility](#version-compatibility)
* [Installation](#installation)
* [Configuration](#configuration)
* [Usage](#usage)
* [Resources](#resources)
* [Changelog](https://github.com/orchestral/debug/releases)

## Version Compatibility

Laravel    | Debug
:----------|:----------
 4.x.x     | 2.x.x
 5.0.x     | 3.0.x
 5.1.x     | 3.1.x
 5.2.x     | 3.2.x
 5.3.x     | 3.3.x
 5.4.x     | 3.4.x
 5.5.x     | 3.5.x
 5.6.x     | 3.6.x@dev

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
Profiler::start();
```

> This normally would goes in your development environment such as `local` environment, in the case `app/Providers/AppServiceProvider.php` would be an ideal location to include the command.

### Viewing the Profiler

To view the profiler, run the following command in your terminal:

    php artisan debug

