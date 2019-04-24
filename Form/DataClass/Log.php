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

namespace Ekino\DataProtectionBundle\Form\DataClass;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Log.
 *
 * @author Benoit Mazière <benoit.maziere@ekino.com>
 */
class Log
{
    const ACTION_ENCRYPT = 'encrypt';
    const ACTION_DECRYPT = 'decrypt';

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    private $content;

    /**
     * @var string
     *
     * @Assert\Choice(
     *      choices = {
     *          Log::ACTION_ENCRYPT,
     *          Log::ACTION_DECRYPT,
     *      }
     * )
     */
    private $action;

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return self
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAction(): ?string
    {
        return $this->action;
    }

    /**
     * @param string $action
     *
     * @return self
     */
    public function setAction(string $action): self
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDecryptAction(): bool
    {
        return static::ACTION_DECRYPT === $this->action;
    }

    /**
     * @return bool
     */
    public function isEncryptAction(): bool
    {
        return static::ACTION_ENCRYPT === $this->action;
    }
}
