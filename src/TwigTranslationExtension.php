<?php

namespace Odan\Twig;

use InvalidArgumentException;
use Twig\Extensions\I18nExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Class TwigTranslationExtension.
 */
class TwigTranslationExtension extends I18nExtension
{
    /**
     * The translator callback function.
     *
     * @var callable|string
     */
    private $translator;

    /**
     * The constructor.
     *
     * @param callable|string|null $translator A callable implementing the translation.
     * If null, the "__" function will be used.
     *
     * @throws InvalidArgumentException
     */
    public function __construct($translator = null)
    {
        $this->translator = $translator ?: '__';

        if (!is_callable($this->translator)) {
            throw new InvalidArgumentException('Translator must be a valid callable');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('__', [$this, '__']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        $translator = new TwigFunction('__', [$this, '__']);
        $translator->setArguments([]);

        return [$translator];
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
