<?php

namespace Odan\Twig;

use InvalidArgumentException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\ExtensionInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Twig translation extension.
 */
final class TwigTranslationExtension implements ExtensionInterface
{
    /**
     * The translator.
     *
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * The constructor.
     *
     * @param TranslatorInterface $translator The translator
     *
     * @throws InvalidArgumentException
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('__', [$this, 'translate']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        $twigFunction = new TwigFunction('__', [$this, 'translate']);
        $twigFunction->setArguments([]);

        return [$twigFunction];
    }

    /**
     * Translate callback.
     *
     * @return mixed
     */
    public function translate()
    {
        $args = func_get_args();
        $parameters = array_slice($args, 1);

        return $this->translator->trans($args[0], $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeVisitors()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getTests()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getOperators()
    {
        return [];
    }
}
