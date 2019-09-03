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

namespace Ekino\DataProtectionBundle\Encryptor;

use Ekino\DataProtectionBundle\Exception\EncryptionException;

/**
 * @author Rémi Marseille <remi.marseille@ekino.com>
 */
interface EncryptorInterface
{
    /**
     * @param string $data
     *
     * @throws EncryptionException
     *
     * @return string
     */
    public function encrypt(string $data): string;

    /**
     * @param string $data
     *
     * @throws EncryptionException
     *
     * @return string
     */
    public function decrypt(string $data): string;
}
