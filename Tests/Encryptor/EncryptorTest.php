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

namespace Ekino\DataProtectionBundle\Tests\Encryptor;

use Ekino\DataProtectionBundle\Encryptor\Encryptor;
use PHPUnit\Framework\TestCase;

/**
 * Class EncryptorTest.
 *
 * @author Rémi Marseille <remi.marseille@ekino.com>
 * @author Benoit Mazière <benoit.maziere@ekino.com>
 */
class EncryptorTest extends TestCase
{
    /**
     * Test encrypt & decrypt.
     */
    public function testEncryptAndDecrypt(): void
    {
        $rawData       = 'my raw data';
        $encryptor     = new Encryptor('aes-256-cbc', 'foo');
        $encryptedData = $encryptor->encrypt($rawData);

        $this->assertSame($rawData, $encryptor->decrypt($encryptedData));
    }
}
