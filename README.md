# Twig Translation Extension

Twig Translation Extension for the [Poedit](https://poedit.net/) translations editor.

[![Latest Version on Packagist](https://img.shields.io/github/release/odan/twig-translation.svg)](https://github.com/odan/twig-translation/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)
[![Build Status](https://travis-ci.org/odan/twig-translation.svg?branch=master)](https://travis-ci.org/odan/twig-translation)
[![Code Coverage](https://scrutinizer-ci.com/g/odan/twig-translation/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/odan/twig-translation/code-structure)
[![Quality Score](https://scrutinizer-ci.com/g/odan/twig-translation/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/odan/twig-translation/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/odan/twig-translation.svg)](https://packagist.org/packages/odan/twig-translation)


## Installation

```
composer install odan/twig-translation
```

## Integration

Create a global callback function with the name `__`.

This example used the [symfony/translation](https://github.com/symfony/translation) component:

```
/**
 * Text translation (I18n)
 *
 * @param string $message
 * @param ...$context
 * @return string
 *
 * <code>
 * echo __('Hello');
 * echo __('There are %s users logged in.', 7);
 * </code>
 */
function __($message)
{
    /* @var $translator Translator */
    $translator = container()->get(Translator::class);
    $translated = $translator->trans($message);
    $context = array_slice(func_get_args(), 1);
    if (!empty($context)) {
        $translated = vsprintf($translated, $context);
    }
    return $translated;
}
```

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

## Usage

Translate a text:

```twig
{{ __('Yes') }}
```

Translate a text with a placeholder:

{{ __('Hello: %s', username) }}

Output:

```
Hello master
```

Translate a text with multiple placeholders:

{{ __('First name: %s, Last name: %s', firstName, lastName) }}

Output: 

```
First name: John, Last name: Doe
```

## Parsing with Poedit

You have to iterate over all your Twig templates and force compilation. 
Then just add the local Twig cache patch to the Poedit Source path and update the catalog.

File: `bin/parse-twig.php`

```php
<?php

require_once __DIR__ . '/../config/bootstrap.php';

$container = container();

/* @var \Slim\Views\Twig $twig */
$twig = $container->get(\Slim\Views\Twig::class);

$settings = $container->get('settings');
$viewPath = $settings['view']['path'];
$cachePath = $settings['view']['cache_path'];

// Iterate over all your templates and force compilation
$twig->getEnvironment()->disableDebug();
$twig->getEnvironment()->enableAutoReload();
$twig->getEnvironment()->setCache($cachePath);

$directory = new RecursiveDirectoryIterator($viewPath, FilesystemIterator::SKIP_DOTS);
foreach (new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::SELF_FIRST) as $file) {
    /* @var SplFileInfo $file */
    if ($file->isFile() && $file->getExtension() === 'twig') {
        $templateName = substr($file->getPathname(), strlen($viewPath) + 1);
        $templateName = str_replace('\\', '/', $templateName);
        echo sprintf("Parse file: %s\n", $templateName);
        $twig->getEnvironment()->loadTemplate($templateName);
    }
}

echo "Done\n";
```
