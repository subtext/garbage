<?php

namespace Subtext\Garbage\Services;

use Symfony\Component\Translation\Formatter\MessageFormatterInterface;
use Symfony\Component\Translation\Formatter\ChoiceMessageFormatterInterface;
use Symfony\Component\Translation\MessageSelector;

class MessageFormatter implements MessageFormatterInterface, ChoiceMessageFormatterInterface
{
    /**
     * @var MessageSelector
     */
    private $selector;

    /**
     * MessageFormatter constructor.
     *
     * @param MessageSelector|null $selector
     */
    public function __construct(MessageSelector $selector = null)
    {
        $this->selector = $selector ?: new MessageSelector();
    }

    /**
     * {@inheritdoc}
     */
    public function format($message, $locale, array $parameters = []): string
    {
        $values = array_values($parameters);
        return sprintf($message, ...$values);
    }

    /**
     * {@inheritdoc}
     */
    public function choiceFormat($message, $number, $locale, array $parameters = []): string
    {
        $parameters[] = $number;
        return $this->format(
            $this->selector->choose($message, $number, $locale),
            $locale,
            $parameters
        );
    }
}