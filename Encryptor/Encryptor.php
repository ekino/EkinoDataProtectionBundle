<?php

/*
 * This file is part of the ekino/data-protection-bundle project.
 *
 * (c) Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\DataProtectionBundle\Encryptor;

/**
 * Encrypt data using the given cipher method.
 *
 * @author Rémi Marseille <remi.marseille@ekino.com>
 * @author Benoit Mazière <benoit.maziere@ekino.com>
 */
class Encryptor implements EncryptorInterface
{
    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $secret;

    /**
     * Enryptor constructor.
     *
     * @param string $method
     * @param string $secret
     */
    public function __construct(string $method, string $secret)
    {
        $this->method = $method;
        $this->secret = $secret;
    }

    /**
     * {@inheritdoc}
     */
    public function encrypt(string $data): string
    {
        $ivSize     = openssl_cipher_iv_length($this->method);
        $iv         = openssl_random_pseudo_bytes($ivSize);
        $cipherText = openssl_encrypt($data, $this->method, $this->secret, OPENSSL_RAW_DATA, $iv);

        return base64_encode($iv.$cipherText);
    }

    /**
     * {@inheritdoc}
     */
    public function decrypt(string $data): string
    {
        $data       = base64_decode($data);
        $ivSize     = openssl_cipher_iv_length($this->method);
        $iv         = mb_substr($data, 0, $ivSize, '8bit');
        $cipherText = mb_substr($data, $ivSize, null, '8bit');

        return openssl_decrypt($cipherText, $this->method, $this->secret, OPENSSL_RAW_DATA, $iv);
    }
}
