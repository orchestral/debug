Artisan Debug Profiler for Laravel 4
==============

`Orchestra\Debug` is commandline profiling package for Laravel 4, It was based from Laravel 4.1 commandline profiling tool which was merged with `php artisan tail`.

## Quick Installation

To install through composer, simply put the following in your `composer.json` file:

```json
{
	"require": {
		"orchestra/debug": "2.0.*"
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
