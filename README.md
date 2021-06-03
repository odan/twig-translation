# Twig Translation Extension

A Twig Translation Extension.

[![Latest Version on Packagist](https://img.shields.io/github/release/odan/twig-translation.svg)](https://github.com/odan/twig-translation/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE)
[![Build Status](https://github.com/odan/twig-translation/workflows/build/badge.svg)](https://github.com/odan/twig-translation/actions)
[![Code Coverage](https://scrutinizer-ci.com/g/odan/twig-translation/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/odan/twig-translation/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/odan/twig-translation/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/odan/twig-translation/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/odan/twig-translation.svg)](https://packagist.org/packages/odan/twig-translation)

**Please read this first!**

The [symfony/twig-bridge](https://github.com/symfony/twig-bridge) also provides a 
Twig 3 [TranslationExtension](https://github.com/symfony/twig-bridge/blob/5.x/Extension/TranslationExtension.php) 
to translate messages with the [trans](https://symfony.com/doc/current/translation.html#translations-in-templates) filter.
For this reason the `odan/twig-translation` component is just redundant and will be deprecated in the near future.
I strongly recommend you to use the `symfony/twig-bridge` TranslationExtension instead. 

Here you can find an installation guide:

* https://odan.github.io/2020/04/17/slim4-twig-templates.html#translations

## Requirements

* PHP 7.3+ or 8.0+

## Installation

```
composer require odan/twig-translation
```

### Registering the extension

This example uses the [symfony/translation](https://github.com/symfony/translation) component:

```
composer require symfony/translation 
```

Register the Twig Extension:

```php
$loader = new \Twig\Loader\FilesystemLoader('/path/to/templates');
$twig = new \Twig\Environment($loader, array(
    'cache' => '/path/to/twig-cache',
));

$translator = new \Symfony\Component\Translation\Translator(
    'en_US',
    new MessageFormatter(new IdentityTranslator()),
    null
);

$translator->addLoader('mo', new MoFileLoader());

$twig->addExtension(new \Odan\Twig\TwigTranslationExtension($translator));
```

## Slim 4 integration

To install the [symfony/translation](https://github.com/symfony/translation) component, run:

```
composer require symfony/translation 
```

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

Add a new container definition:

```php
<?php

use Odan\Twig\TwigTranslationExtension;
use Psr\Container\ContainerInterface;
use Symfony\Component\Translation\Formatter\MessageFormatter;
use Symfony\Component\Translation\IdentityTranslator;
use Symfony\Component\Translation\Loader\MoFileLoader;
use Symfony\Component\Translation\Translator;

// ...

return [
    // ...

    Translator::class => function (ContainerInterface $container) {
        $settings = $container->get('settings')['locale'];

        $translator = new Translator(
            $settings['locale'],
            new MessageFormatter(new IdentityTranslator()),
            $settings['cache'],
            $settings['debug']
        );

        $translator->addLoader('mo', new MoFileLoader());

        // Optional: Inject the translator instance into the __() function
        // __($translator);

        return $translator;
    },

    Twig::class => function (ContainerInterface $container) {
        $twig = Twig::create('/path/to/templates', []);
    
        // Add extension
        $translator = $container->get(Translator::class);
        $twig->addExtension(new TwigTranslationExtension($translator));
    
        // Add more extension ...
    
        return $twig;
    },

];
```

### Create a global translation function

This step is **optional**, but recommend if you want to translate messages directly in PHP.

Create the file `src/Utility/translate.php` and copy / paste this content:

```php
<?php

use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Translate text.
 *
 * @param string|TranslatorInterface $message The message being translated or the translator
 * @param string|int|float|bool ...$context The context arguments
 *
 * @return string The translated message
 */
function __($message, ...$context): string
{
    /** @var TranslatorInterface $translator */
    static $translator = null;
    if ($message instanceof TranslatorInterface) {
        $translator = $message;

        return '';
    }

    $translated = $translator->trans($message);
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

Run: `composer update`

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

## Similar libraries

The [symfony/twig-bridge](https://github.com/symfony/twig-bridge) provides `TranslationExtension` for Twig 3+.

**Read more**

* [Slim 4 - Twig Translations](https://odan.github.io/2020/04/17/slim4-twig-templates.html#translations)

## License

* MIT

