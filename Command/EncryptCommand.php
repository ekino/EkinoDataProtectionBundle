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

namespace Ekino\DataProtectionBundle\Command;

use Ekino\DataProtectionBundle\Encryptor\Encryptor;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class EncryptCommand
 *
 * @author Christian Kollross <christian.kollross@ekino.com>
 */
#[AsCommand(name: 'ekino-data-protection:encrypt')]
final class EncryptCommand extends Command
{
    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $secret;

    public function __construct(string $method, string $secret)
    {
        $this->method = $method;
        $this->secret = $secret;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setDescription('Encrypt a given text (like a password)')
            ->addArgument('text', null, 'Text to encrypt')
            ->addOption('secret', 's', InputOption::VALUE_REQUIRED, 'Application secret', $this->secret)
            ->addOption('method', 'm', InputOption::VALUE_REQUIRED, 'Encryption cipher, see openssl_get_cipher_methods()',
                $this->method)
            ->setHelp('Usage: `bin/console ekino-data-protection:encrypt myText`, optionally with `--secret mySecret` and/or `--method myCipher`')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $text   = $input->getArgument('text');
        $secret = $input->getOption('secret');

        if (empty($secret)) {
            throw new \InvalidArgumentException('Secret must not be empty');
        }

        $method = $input->getOption('method');

        if (!\in_array($method, openssl_get_cipher_methods())) {
            throw new \InvalidArgumentException(sprintf('The method "%s" is not available. Please choose one of the following methods: %s', $method, implode(', ', openssl_get_cipher_methods())));
        }

        $output->writeln(sprintf("<info>Encryption parameters:</info>\nText:\t\"%s\"\nSecret:\t\"%s\"\nMethod:\t\"%s\"\n", $text, $secret, $method));
        $encryptor = new Encryptor($method, $secret);
        $output->writeln(sprintf('<info>Encrypted text:</info> %s', $encryptor->encrypt($text)));

        return 0;
    }
}
