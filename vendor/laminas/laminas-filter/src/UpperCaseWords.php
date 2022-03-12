<?php

namespace Laminas\Filter;

final class UpperCaseWords extends AbstractUnicode
{
    /**
     * {@inheritDoc}
     */
    protected $options = [
        'encoding' => null
    ];

    /**
     * Constructor
     *
     * @param string|array|\Traversable $encodingOrOptions OPTIONAL
     */
    public function __construct($encodingOrOptions = null)
    {
        if ($encodingOrOptions !== null) {
            if (static::isOptions($encodingOrOptions)) {
                $this->setOptions($encodingOrOptions);
            } else {
                $this->setEncoding($encodingOrOptions);
            }
        }
    }

    /**
     * {@inheritDoc}
     *
     * Returns the string $value, converting words to have an uppercase first character as necessary
     *
     * If the value provided is not a string, the value will remain unfiltered
     *
     * @param  string|mixed $value
     * @return string|mixed
     */
    public function filter($value)
    {
        if (! is_string($value)) {
            return $value;
        }

        $value = (string) $value;

        if ($this->options['encoding'] !== null) {
            return mb_convert_case($value, MB_CASE_TITLE, $this->options['encoding']);
        }

        return ucwords(strtolower($value));
    }
}