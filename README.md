# Twig Translation Extension

A Twig Translation Extension.

[![Latest Version on Packagist](https://img.shields.io/github/release/odan/twig-translation.svg)](https://github.com/odan/twig-translation/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)
[![Build Status](https://travis-ci.org/odan/twig-translation.svg?branch=master)](https://travis-ci.org/odan/twig-translation)
[![Code Coverage](https://scrutinizer-ci.com/g/odan/twig-translation/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/odan/twig-translation/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/odan/twig-translation/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/odan/twig-translation/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/odan/twig-translation.svg)](https://packagist.org/packages/odan/twig-translation)

## Requirements

* PHP 7.1+

## Installation

```
composer require odan/twig-translation
```

## Integration

### Register the Twig Extension

```php
$loader = new \Twig\Loader\FilesystemLoader('/path/to/templates');
$twig = new \Twig\Environment($loader, array(
    'cache' => '/path/to/twig-cache',
));

$twig->addExtension(new \Odan\Twig\TwigTranslationExtension());
```

## Register a callback function

Create a global callback function with the name `__`.

This example uses the [symfony/translation](https://github.com/symfony/translation) component:

### Installation

```php
composer require symfony/translation 
composer require symfony/config
```

Add this function to a file like `src/Utility/translate.php`:

```php
<?php

use Symfony\Component\Translation\Translator;

/**
 * Text translation.
 *
 * @param string|Translator $message The message or the translator instance
 *
 * @return string The translated message
 *
 * <code>
 * echo __('Hello');
 * echo __('There are %s users logged in.', 7);
 * </code>
 */
function __($message): string
{
    /** @var Translator $translator */
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

Register the composer autoloader in composer.json:

```json
"autoload": {
    "files": [
        "src/Utility/translate.php"
    ]
},
```

## Slim 4 integration

Add settings:

```php
// Locale settings
$settings['locale'] = [
    'path' => '/path/to/resources/locale',
    'cache' => '/path/to/locale-cache',
    'locale' => 'en_US',
    'domain' => 'messages',
    // Should be set to false in production
    'debug' => false,
];
```

Add a new container entry:

```php
use League\Container\Container;
use Slim\Views\Twig;
use Symfony\Component\Translation\Formatter\MessageFormatter;
use Symfony\Component\Translation\IdentityTranslator;
use Symfony\Component\Translation\Loader\MoFileLoader;
use Symfony\Component\Translation\Translator;

$container = new Container();

// ...

$container->share(Translator::class, static function (Container $container) {
    $settings = $container->get('settings')['locale'];

    $translator = new Translator(
        $settings['locale'],
        new MessageFormatter(new IdentityTranslator()),
        $settings['cache']
    );

    $translator->addLoader('mo', new MoFileLoader());

    // Set translator instance
    __($translator);

    return $translator;
})->addArgument($container);
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

### Parsing Twig files

You need to iterate and compile all your Twig templates.
The compilation step generates the PHP cache files that can be parsed from Poedit.
This script is only an example and must be adapted to your individual environment.

Twig settings:

```php
// Twig settings
$settings['twig'] = [
    'path' => '/path/to/twig/templates',
    // Should be set to true in production
    'cache_enabled' => true,
    'cache_path' => '/path/to/twig-cache', // <---
];
```

File: `bin/parse-twig.php`

```php
use Odan\Twig\TwigCompiler;
use Slim\App;
use Slim\Views\Twig;

// Bootstrap Slim application

/** @var ContainerInterface $container */
$container = (require __DIR__ . '/../config/bootstrap.php')->getContainer();

/** @var App $app */
$app = $container->get(App::class);

// Read twig settings
$settings = $container->get('settings')['twig'];
$cachePath = (string)$settings['cache_path'];

$twig = $container->get(Twig::class)->getEnvironment();

// Compile twig templates (*.twig) to PHP code
$compiler = new TwigCompiler($twig, $cachePath, true);
$compiler->compile();

echo "Done\n";

return 0;
```

To run this script just enter: `php bin/parse-twig.php`

Here you can find a more advanced [Symfony command example](https://github.com/odan/slim4-skeleton/blob/master/src/Console/TwigCompilerCommand.php).
