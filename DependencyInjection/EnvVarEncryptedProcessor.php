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

namespace Ekino\DataProtectionBundle\DependencyInjection;

use Ekino\DataProtectionBundle\Encryptor\EncryptorInterface;
use Symfony\Component\DependencyInjection\EnvVarProcessorInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

@trigger_error(
    'The '.__NAMESPACE__.'\EnvVarEncryptedProcessor class is deprecated, use symfony secrets management instead',
    E_USER_DEPRECATED
);

/**
 * @author Rémi Marseille <remi.marseille@ekino.com>
 *
 * @deprecated use symfony secrets instead.
 * @see https://github.com/symfony/symfony/pull/33997
 */
final class EnvVarEncryptedProcessor implements EnvVarProcessorInterface
{
    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * EnvVarEncryptedProcessor constructor.
     *
     * @param EncryptorInterface $encryptor
     */
    public function __construct(EncryptorInterface $encryptor)
    {
        $this->encryptor = $encryptor;
    }

    /**
     * {@inheritdoc}
     */
    public function getEnv($prefix, $name, \Closure $getEnv)
    {
        if ('ekino_encrypted' === $prefix) {
            return $this->encryptor->decrypt($getEnv($name));
        }

        throw new RuntimeException(sprintf('Unsupported env var prefix "%s".', $prefix));
    }

    /**
     * {@inheritdoc}
     */
    public static function getProvidedTypes()
    {
        return [
            'ekino_encrypted' => 'string',
        ];
    }
}
