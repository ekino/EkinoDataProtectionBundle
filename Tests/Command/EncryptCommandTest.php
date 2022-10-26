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

namespace Ekino\DataProtectionBundle\Tests\Command;

use Ekino\DataProtectionBundle\Command\EncryptCommand;
use Ekino\DataProtectionBundle\Tests\ReflectionHelperTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class EncryptCommandTest
 *
 * @author Christian Kollross <christian.kollross@ekino.com>
 */
class EncryptCommandTest extends TestCase
{
    use ReflectionHelperTrait;

    const CIPHER = 'aes-192-cfb8';

    /**
     * @var InputInterface|MockObject
     */
    private $input;

    /**
     * @var OutputInterface|MockObject
     */
    private $output;

    /**
     * @var EncryptCommand
     */
    private $command;

    protected function setUp(): void
    {
        $this->input   = $this->createMock(InputInterface::class);
        $this->output  = $this->createMock(OutputInterface::class);
        $this->command = new EncryptCommand(self::CIPHER, 'theApplicationSecret');
    }

    public function testExcecute(): void
    {
        $this->input->expects($this->once())->method('getArgument')->with('text')->willReturn('SomeText');
        $this->input->expects($this->exactly(2))->method('getOption')->withConsecutive(['secret'], ['method'])->willReturnOnConsecutiveCalls('theApplicationSecret', self::CIPHER);

        $this->output->expects($this->exactly(2))->method('writeln');

        $this->assertSame(0, $this->invokeMethod($this->command, 'execute', [$this->input, $this->output]));
    }

    public function testExcecuteWithBadCipher(): void
    {
        $this->input->expects($this->once())->method('getArgument')->with('text')->willReturn('SomeText');
        $this->input->expects($this->exactly(2))->method('getOption')->withConsecutive(['secret'], ['method'])->willReturnOnConsecutiveCalls('theApplicationSecret', 'NotACipher');

        $this->output->expects($this->never())->method('writeln');

        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('The method "NotACipher" is not available. Please choose one of the following methods: ');

        $this->invokeMethod($this->command, 'execute', [$this->input, $this->output]);
    }
}
