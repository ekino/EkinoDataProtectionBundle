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

namespace Ekino\DataProtectionBundle\Tests\Monolog\Processor;

use Ekino\DataProtectionBundle\Encryptor\EncryptorInterface;
use Ekino\DataProtectionBundle\Monolog\Processor\GdprProcessor;
use PHPUnit\Framework\TestCase;

/**
 * Class GdprProcessorTest.
 *
 * @author Rémi Marseille <remi.marseille@ekino.com>
 * @author Benoit Mazière <benoit.maziere@ekino.com>
 */
class GdprProcessorTest extends TestCase
{
    /**
     * Test __invoke method.
     */
    public function testProcessor(): void
    {
        $encryptor = $this->createMock(EncryptorInterface::class);
        $encryptor->expects($this->once())->method('encrypt')->willReturn('encrypted_data');

        $processor = new GdprProcessor($encryptor);

        $this->assertSame(['context' => [
            'foo'          => 'bar',
            'private_data' => 'encrypted_data',
        ]], $processor->__invoke([
            'context' => [
                'foo'          => 'bar',
                'private_data' => [
                    'foo' => 'baz',
                ],
            ],
        ]));
    }
}
