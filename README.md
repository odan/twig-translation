# Twig Translation Extension

Twig Translation Extension for the [Poedit](https://poedit.net/) translations editor.

[![Latest Version on Packagist](https://img.shields.io/github/release/odan/twig-translation.svg)](https://github.com/odan/twig-translation/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)
[![Build Status](https://travis-ci.org/odan/twig-translation.svg?branch=master)](https://travis-ci.org/odan/twig-translation)
[![Code Coverage](https://scrutinizer-ci.com/g/odan/twig-translation/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/odan/twig-translation/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/odan/twig-translation/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/odan/twig-translation/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/odan/twig-translation.svg)](https://packagist.org/packages/odan/twig-translation)


## Installation

```
composer install odan/twig-translation
```

## Integration

### Register the Twig Extension

```php
$loader = new Twig_Loader_Filesystem('/path/to/templates');
$twig = new Twig_Environment($loader, array(
    'cache' => '/path/to/compilation_cache',
));

$twig->addExtension(new \Odan\Twig\TwigTranslationExtension());
```

### Slim Framework

In your `dependencies.php` or wherever you add your service factories:

```php
$twig->addExtension(new \Odan\Twig\TwigTranslationExtension());
```

## Register a callback function

Create a global callback function with the name `__`.

This example used the [symfony/translation](https://github.com/symfony/translation) component:

```php
/**
 * Text translation (I18n)
 *
 * @param string|Translator $message
 * @return string
 *
 * <code>
 * echo __('Hello');
 * echo __('There are %s users logged in.', 7);
 * </code>
 */
function __($message)
{
    /* @var Translator $translator */
    static $translator = null;
    if ($message instanceof Translator) {
        $translator = $message;
        return '';
    }

    $translated = $translator->trans($message);
    $context = array_slice(func_get_args(), 1);
    if (!empty($context)) {
        $translated = vsprintf($translated, $context);
    }
    return $translated;
}
```

## Usage

Translate a text:

```twig
{{ __('Yes') }}
```

Translate a text with a placeholder:

```twig
{{ __('Hello: %s', username) }}
```

Output (depends on the language):

```
Hello master
```

Translate a text with multiple placeholders:

```twig
{{ __('First name: %s, Last name: %s', firstName, lastName) }}
```

Output (depends on the language):

```
First name: John, Last name: Doe
```

Create a plural translation:

Example 1:
```twig
{% if count > 1 %}
    {{ count }} {{ __('Users') }}
{% else %}
    {{ count }} {{ __('User') }}
{% endif %}
```

Example 2:

```twig
{% if users|length > 1 %}
    {{ users|length }} {{ __('Users') }}
{% else %}
    {{ users|length }} {{ __('User') }}
{% endif %}
```

Create a complex plural translation:

```twig
{% if not count %}
    {{ __('No users') }}
{% elseif count = 1 %}
    {{ count }} {{ __('User') }}
{% else %}
    {{ count }} {{ __('Users') }}
{% endif %}
```

## Parsing with Poedit

### The workflow

1. Parse all twig files (`php bin/parse-twig.php`)
2. Start Poedit and open the .po file
3. Click the `Update` button to parse all PHP and Twig cache files
4. Translate the text and save the file.

### Poedit Setup

* Start Poedit and open the .po file
* Open the menu: `Catalogue` > `Properties...`
* Open the tab: `Source paths` 
  * Add a new path and point it to the twig cache 
  * The path must be relative to the base path e.g. `..\temp\twig-cache`
* Open the tab: `Source keyword` 
  * Add a new keyword with the name `__` (2 underscores)
* Click the `OK` button and `Update` the calalogue.

### Parsing the Twig files

You need to iterate and compile all your Twig templates.
The compilation step generates the PHP cache files that can be parsed from Poedit.
This script is only an example and must be adapted to your individual environment.

File: `bin/parse-twig.php`

```php
<?php

require_once __DIR__ . '/../config/bootstrap.php';

$container = app()->getContainer();

/* @var \Slim\Views\Twig $twigView */
$twigView = $container->get(\Slim\Views\Twig::class);

$settings = $container->get('settings');
$viewPath = $settings['twig']['path'];
$cachePath = $settings['twig']['cache_path'];

// Get the Twig Environment instance from the Twig View instance
$twig = $twigView->getEnvironment();
$twig->setCache($cachePath);

// Compile all Twig templates into cache directory
$compiler = new \Odan\Twig\TwigCompiler($twig, $cachePath);
$compiler->compile();

echo "Done\n";
```

To run this script just run: `php bin/parse-twig.php`
