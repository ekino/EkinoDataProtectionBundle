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

namespace Ekino\DataProtectionBundle\Monolog\Processor;

use Ekino\DataProtectionBundle\Encryptor\EncryptorInterface;
use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

/**
 * Pseudonymize sensitive data inside log contexts.
 *
 * @author Rémi Marseille <remi.marseille@ekino.com>
 * @author Benoit Mazière <benoit.maziere@ekino.com>
 * @author Rolland Csatari <rolland.csatari@ekino.com>
 */
class GdprProcessor implements ProcessorInterface
{
    public function __construct(private EncryptorInterface $encryptor)
    {
    }

    public function __invoke(LogRecord $record): LogRecord
    {
        foreach ($record->context as $key => &$val) {
            if (preg_match('#^private_#', (string) $key)) {
                $encoded = json_encode($val);
                if (false === $encoded) {
                    $encoded = "";
                }
                $val = $this->encryptor->encrypt($encoded);
            }
        }

        return $record;
    }
}
