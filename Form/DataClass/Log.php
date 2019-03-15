<?php

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
    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    private $content;

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
}
