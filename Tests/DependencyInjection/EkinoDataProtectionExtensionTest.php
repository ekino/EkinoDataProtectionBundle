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

use Ekino\DataProtectionBundle\DependencyInjection\EkinoDataProtectionExtension;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Rémi Marseille <remi.marseille@ekino.com>
 */
class EkinoDataProtectionExtensionTest extends TestCase
{
    /**
     * @var EkinoDataProtectionExtension
     */
    private $extension;

    /**
     * @var ContainerBuilder|MockObject
     */
    private $containerBuilder;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->extension        = new EkinoDataProtectionExtension();
        $this->containerBuilder = $this->createMock(ContainerBuilder::class);
    }

    /**
     * Tests load with invalid configs.
     *
     * @param array  $configs
     * @param string $exceptionMessage
     *
     * @dataProvider getInvalidConfigs
     */
    public function testLoadWithInvalidConfigs(array $configs, string $exceptionMessage): void
    {
        try {
            $this->extension->load($configs, $this->containerBuilder);

            $this->fail(sprintf('Expecting %s with message \'%s\'', InvalidConfigurationException::class, $exceptionMessage));
        } catch (InvalidConfigurationException $e) {
            $this->assertSame($exceptionMessage, $e->getMessage());
        }
    }

    /**
     * Assert the encryptor is well configured.
     */
    public function testEncryptorConfig(): void
    {
        $encryptorDefinition = $this->createMockDefinition();
        $encryptorDefinition
            ->expects($this->exactly(2))
            ->method('replaceArgument')
            ->withConsecutive(
                [0, 'aes-256-xts'],
                [1, 'foo']
            )
            ->willReturnSelf();

        $this->containerBuilder
            ->expects($this->once())
            ->method('findDefinition')
            ->with($this->equalTo('ekino_data_protection.encryptor'))
            ->willReturn($encryptorDefinition);

        $this->extension->load([['encryptor' => ['method' => 'aes-256-xts', 'secret' => 'foo']]], $this->containerBuilder);
    }

    /**
     * Assert the encrypt_logs config is enabled/disabled.
     *
     * @param bool $enabled
     *
     * @dataProvider getEncryptLogs
     */
    public function testEncryptLogsEnabled(bool $enabled): void
    {
        $definition = $this->createMockDefinition();
        $definition->expects($this->exactly(2))->method('replaceArgument')->willReturnSelf();

        $this->containerBuilder->expects($this->once())->method('findDefinition')->willReturn($definition);
        $this->containerBuilder->expects($enabled ? $this->never() : $this->once())->method('removeDefinition')->with($this->equalTo('ekino_data_protection.monolog.processor.gdpr'));

        $this->extension->load([['encryptor' => ['secret' => 'foo'], 'encrypt_logs' => $enabled]], $this->containerBuilder);
    }

    /**
     * @return \Generator
     */
    public function getInvalidConfigs(): \Generator
    {
        yield [[[]],                                                            'The child node "encryptor" at path "ekino_data_protection" must be configured.'];
        yield [[['encryptor' => []]],                                           'The child node "secret" at path "ekino_data_protection.encryptor" must be configured.'];
        yield [[['encryptor' => ['method' => 'aes-256-xts']]],                  'The child node "secret" at path "ekino_data_protection.encryptor" must be configured.'];
        yield [[['encryptor' => ['method' => 'aes-256-xts', 'secret' => '']]],  'The path "ekino_data_protection.encryptor.secret" cannot contain an empty value, but got "".'];
        yield [[['encryptor' => ['secret' => 'foo'], 'encrypt_logs' => 'bar']], 'Invalid type for path "ekino_data_protection.encrypt_logs". Expected boolean, but got string.'];
        yield [[[
            'encryptor'        => ['secret' => 'foo'],
            'encrypt_logs'     => true,
            'use_sonata_admin' => 'bar',
        ]], 'Invalid type for path "ekino_data_protection.use_sonata_admin". Expected boolean, but got string.'];
    }

    /**
     * @return \Generator
     */
    public function getEncryptLogs(): \Generator
    {
        yield [true];
        yield [false];
    }

    /**
     * @return MockObject
     */
    private function createMockDefinition(): MockObject
    {
        return $this->createMock(Definition::class);
    }
}
