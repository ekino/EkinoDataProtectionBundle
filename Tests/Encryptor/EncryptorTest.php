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
use Ekino\DataProtectionBundle\Exception\EncryptionException;
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
     * @var string|bool $encryptData
     */
    public static $encryptData = true;

    /**
     * @var string
     */
    private $rawData = 'my raw data';

    /**
     * @var Encryptor
     */
    private $encryptor;

    /**
     * Initialize test EncryptorTest.
     */
    protected function setUp(): void
    {
        $this->encryptor = new Encryptor('aes-256-cbc', 'foo');
    }

    /**
     * Test encrypt & decrypt.
     */
    public function testEncryptAndDecrypt(): void
    {
        $encryptedData = $this->encryptor->encrypt($this->rawData);

        $this->assertSame($this->rawData, $this->encryptor->decrypt($encryptedData));
    }

    /**
     * Test encrypt not ok.
     */
    public function testEncryptNok(): void
    {
        self::$encryptData = false;

        $this->expectException(EncryptionException::class);
        $this->expectExceptionMessage('Unexpected failure in openssl_encrypt: ');

        $encryptedData = $this->encryptor->encrypt($this->rawData);
    }

    /**
     * Test decrypt not ok.
     */
    public function testDecryptNok(): void
    {
        $this->expectException(EncryptionException::class);
        $this->expectExceptionMessage('Unexpected failure in openssl_decrypt: ');

        $this->encryptor->decrypt('dummy-example-for-testing-purpose');
    }
}

namespace Ekino\DataProtectionBundle\Encryptor;

function openssl_encrypt($data, $method, $key, $options, $iv) {
    return \Ekino\DataProtectionBundle\Tests\Encryptor\EncryptorTest::$encryptData ? \openssl_encrypt($data, $method, $key, $options, $iv) : false;
}
