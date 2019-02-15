EkinoDataProtectionBundle
=========================

**This is a work in progress, so if you'd like something implemented please
feel free to ask for it or contribute to help us!**

# Purpose

This bundle protects the data of your project through encryption.

# Installation

## Step 1: add dependency

```bash
$ composer require ekino/data-protection-bundle
```

## Step 2: register the bundle

### Symfony 2 or 3:

```php
<?php

// app/AppKernel.php

public function registerBundles()
{
    $bundles = [
        // ...
        new Ekino\DataProtectionBundle\EkinoDataProtectionBundle(),
        // ...
    ];
}
```

### Symfony 4:

```php
<?php

// config/bundles.php

return [
    // ...
    Ekino\DataProtectionBundle\EkinoDataProtectionBundle::class => ['all' => true],
    // ...
];
```

## Step 3: configure the bundle

```yaml
ekino_data_protection:
    encryptor:
        method: aes-256-cbc # default
        secret: foo         # required

    encrypt_logs: true      # default
```

The `method` is one of [openssl_get_cipher_methods()][1].

# Usage

## Encrypt the logs

This bundle provides a processor for [Monolog][2] to encrypt your logs in order
to not be human-readable. To use it, just add the prefix `private_` on the
context key for each data you want to encrypt, for instance:

```php
<?php

$logger->critical('Something to be logged', [
    'a_non_sensitive_data' => 'foo',  // won't be encrypted
    'private_firstname'    => 'John', // will be encrypted
]);
```

Then the data can be decrypted in a secure area using the encryptor.

If you don't want it, you can disable it in the config:

```yaml
ekino_data_protection:
    encrypt_logs: false
```

[1]: https://php.net/manual/en/function.openssl-get-cipher-methods.php
[2]: https://github.com/Seldaek/monolog
