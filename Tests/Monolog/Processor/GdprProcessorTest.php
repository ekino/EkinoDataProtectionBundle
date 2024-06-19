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
use Monolog\Level;
use Monolog\LogRecord;
use PHPUnit\Framework\TestCase;

/**
 * Class GdprProcessorTest.
 *
 * @author Rémi Marseille <remi.marseille@ekino.com>
 * @author Benoit Mazière <benoit.maziere@ekino.com>
 * @author Rolland Csatari <rolland.csatari@ekino.com>
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

        $originalRecord = new LogRecord(
            new \DateTimeImmutable('2024-06-18'),
            'main',
            Level::Debug,
            'The log context includes private data.',
            [
                0              => 'numeric index',
                'foo'          => 'bar',
                'private_data' => ['foo' => 'baz'],
                'data_private' => ['foo' => 'baz'],
            ],
        );

        $processedRecord = (new GdprProcessor($encryptor))($originalRecord);

        $this->assertSame($originalRecord->datetime, $processedRecord->datetime);
        $this->assertSame($originalRecord->channel, $processedRecord->channel);
        $this->assertSame($originalRecord->level, $processedRecord->level);
        $this->assertSame($originalRecord->message, $processedRecord->message);
        $this->assertSame($originalRecord->extra, $processedRecord->extra);
        $this->assertSame($originalRecord->formatted, $processedRecord->formatted);

        $this->assertSame([
            0              => 'numeric index',
            'foo'          => 'bar',
            'private_data' => 'encrypted_data',
            'data_private' => ['foo' => 'baz'],
        ], $processedRecord->context);
    }
}
