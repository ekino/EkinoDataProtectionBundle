<?php

declare(strict_types=1);

/*
 * This file is part of the ekino/data-protection-bundle project.
 *
 * (c) Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\DataProtectionBundle\Annotations;

/**
 * Class AnonymizedProperty.
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @author Benoit Mazière <benoit.maziere@ekino.com>
 */
final class AnonymizedProperty
{
    public const TYPE_STATIC     = 'static';
    public const TYPE_COMPOSED   = 'composed';
    public const TYPE_EXPRESSION = 'expression';
    private const TYPE_CHOICES   = [self::TYPE_STATIC, self::TYPE_COMPOSED, self::TYPE_EXPRESSION];

    /**
     * @var mixed|null
     */
    private $value;

    /**
     * Can be of type static (fixed value) or composed (mix of static & existing field value).
     *
     * @var string
     */
    private $type = self::TYPE_STATIC;

    /**
     * @var string
     */
    private $fieldName;

    /**
     * @var string
     */
    private $columnName;

    public function __construct(iterable $options)
    {
        foreach ($options as $key => $value) {
            if (!property_exists($this, $key)) {
                throw new \InvalidArgumentException(sprintf('Option "%s" does not exist', $key));
            }

            $this->$key = $value;
        }

        $this->validateType();
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function setFieldName(string $fieldName): self
    {
        $this->fieldName = $fieldName;

        return $this;
    }

    public function getColumnName(): string
    {
        return $this->columnName;
    }

    public function setColumnName(string $columnName): self
    {
        $this->columnName = $columnName;

        return $this;
    }

    public function isStatic(): bool
    {
        return static::TYPE_STATIC === $this->type;
    }

    public function isComposed(): bool
    {
        return static::TYPE_COMPOSED === $this->type;
    }

    public function isExpression(): bool
    {
        return static::TYPE_EXPRESSION === $this->type;
    }

    public function extractComposedFieldFromValue(): string
    {
        preg_match('/<(\w*)>/', $this->value, $matches);

        return $matches[1] ?? '';
    }

    public function explodeComposedFieldValue(): array
    {
        preg_match('/(.*)<(\w*)>(.*)/', $this->value, $matches);

        return $matches ?? [];
    }

    private function validateType(): void
    {
        if (!\in_array($this->type, static::TYPE_CHOICES, true)) {
            throw new \InvalidArgumentException(sprintf('Type "%s" is not allowed. Allowed types are: %s',
                $this->type, implode(', ', static::TYPE_CHOICES)));
        }
    }
}
