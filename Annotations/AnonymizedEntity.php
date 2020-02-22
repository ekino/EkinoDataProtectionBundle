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
 * Class AnonymizedEntity.
 *
 * @Annotation
 * @Target({"CLASS"})
 * 
 * @author Benoit Mazière <benoit.maziere@ekino.com>
 */
final class AnonymizedEntity
{
    public const ACTION_ANONYMIZE = 'anonymize';
    public const ACTION_TRUNCATE  = 'truncate';
    private const ACTION_CHOICES  = [self::ACTION_ANONYMIZE, self::ACTION_TRUNCATE];

    /**
     * @var string
     */
    private $action = self::ACTION_ANONYMIZE;

    /**
     * Add where sql condition on which not apply anonymization.
     *
     * @var string|null
     */
    private $exceptWhereClause;

    public function __construct(iterable $options)
    {
        foreach ($options as $key => $value) {
            if (!property_exists($this, $key)) {
                throw new \InvalidArgumentException(sprintf('Option "%s" does not exist', $key));
            }

            $this->$key = $value;
        }

        $this->validateAction();
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getExceptWhereClause(): ?string
    {
        return $this->exceptWhereClause;
    }

    public function isTruncateAction(): bool
    {
        return static::ACTION_TRUNCATE === $this->action;
    }

    public function isAnonymizeAction(): bool
    {
        return static::ACTION_ANONYMIZE === $this->action;
    }

    private function validateAction(): void
    {
        if (!\in_array($this->action, static::ACTION_CHOICES, true)) {
            throw new \InvalidArgumentException(sprintf('Action "%s" is not allowed. Allowed actions are: %s',
                $this->action, implode(', ', static::ACTION_CHOICES)));
        }
    }
}
