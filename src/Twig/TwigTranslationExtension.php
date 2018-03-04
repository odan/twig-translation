<?php

namespace Odan\Twig;

use Twig_Extensions_Extension_I18n;
use Twig_SimpleFilter;
use Twig_SimpleFunction;

/**
 * Class TwigTranslationExtension
 */
class TwigTranslationExtension extends Twig_Extensions_Extension_I18n
{

    /**
     * The translator callback function.
     *
     * @var callable|string
     */
    private $translator = '__';

    /**
     * The constructor.
     *
     * @param string|callable|null $translator A callable implementing the translation.
     * If null, the "__" function will be used.
     */
    public function __construct($translator = null)
    {
        $this->translator = $translator ? $translator : '__';
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter('__', [$this, '__']),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        $translator = new Twig_SimpleFunction('__', [$this, '__']);
        $translator->setArguments([]);

        return array($translator);
    }

    /**
     * Translate callback.
     *
     * @return mixed
     */
    public function __()
    {
        return call_user_func_array($this->translator, func_get_args());
    }
}
