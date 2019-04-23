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

namespace Ekino\DataProtectionBundle\Tests\DependencyInjection;

use Ekino\DataProtectionBundle\DependencyInjection\EnvVarEncryptedProcessor;
use Ekino\DataProtectionBundle\Encryptor\EncryptorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @author Rémi Marseille <remi.marseille@ekino.com>
 */
class EnvVarEncryptedProcessorTest extends TestCase
{
    /**
     * @var EncryptorInterface|MockObject
     */
    private $encryptor;

    /**
     * @var EnvVarEncryptedProcessor
     */
    private $processor;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->encryptor = $this->createMock(EncryptorInterface::class);
        $this->processor = new EnvVarEncryptedProcessor($this->encryptor);
    }

    /**
     * Asserts getEnv decrypts a secret.
     */
    public function testGetEnv(): void
    {
        $this->encryptor->expects($this->once())->method('decrypt')->willReturn('my_secret');

        $this->assertSame('my_secret',
            $this->processor->getEnv('ekino_encrypted', 'DATABASE_URL', function () {
                return 'd6NhbhWDBVpj5l3gYD5BiKLeYxJllx7Lf8hJXhtoJ70=';
            })
        );
    }

    /**
     * Asserts getEnv throws an exception with wrong prefix.
     *
     * @expectedException        RuntimeException
     * @expectedExceptionMessage Unsupported env var prefix "foo".
     */
    public function testGetEnvThrowException(): void
    {
        $this->processor->getEnv('foo', 'DATABASE_URL', function () {});
    }

    /**
     * Tests getProvidedTypes method.
     */
    public function testGetProvidedTypes(): void
    {
        $this->assertSame([
            'ekino_encrypted' => 'string',
        ], EnvVarEncryptedProcessor::getProvidedTypes());
    }
}
