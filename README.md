EkinoDataProtectionBundle
=========================

[![Latest Stable Version](https://poser.pugx.org/ekino/data-protection-bundle/v/stable)](https://packagist.org/packages/ekino/data-protection-bundle)
[![Build Status](https://travis-ci.org/ekino/EkinoDataProtectionBundle.svg?branch=master)](https://travis-ci.org/ekino/EkinoDataProtectionBundle)
[![Coverage Status](https://coveralls.io/repos/ekino/EkinoDataProtectionBundle/badge.svg?branch=master&service=github)](https://coveralls.io/github/ekino/EkinoDataProtectionBundle?branch=master)
[![Total Downloads](https://poser.pugx.org/ekino/data-protection-bundle/downloads)](https://packagist.org/packages/ekino/data-protection-bundle)

This is a *work in progress*, so if you'd like something implemented please
feel free to ask for it or contribute to help us!

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

    encrypt_logs:     true  # default
    use_sonata_admin: false # default
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

## Decrypt the logs

This bundle provides a [Sonata Admin][3] panel to decrypt your logs that would have
been encrypted by the above processor. To use it, enable it in configuration: 

````yaml
ekino_data_protection:
    use_sonata_admin: true
````

Then, you will be able to add the following route `admin_app_logs_decrypt_encrypt` into 
your menu for example. This route provides a form with only one field in which you
can fill in only the encrypted part of the log or a full text containing several logs.
In case of several encrypted logs, each decrypted result will be displayed in a 
dedicated tab.

## Decrypt your secrets at runtime

This bundle provides a processor using the configured encryptor to decrypt a
secret at runtime. This allows you to not reveal your secrets and easy
rotate them without flushing the cache.

To use it, just use the prefix `ekino_encrypted` as this example shows:

```
# .env

DATABASE_URL=d6NhbhWDBVpj5l3gYD5BiKLeYxJllx7Lf8hJXhtoJ70=
```

```yaml
# config/packages/doctrine.yaml

doctrine:
    dbal:
        url: '%env(ekino_encrypted:DATABASE_URL)%'
```

## Encrypt texts using the CLI

To encrypt a text, run the following command:

`bin/console ekino-data-protection:encrypt myText`, optionally with `--secret mySecret` and/or `--method myCipher`

## Anonymize your database

This bundle provides an easy way to anonymize your database. This feature can be useful when you get down some 
production database to your staged environments for example and you care about who access to real client/user data
according to GDPR reglementation.

Efficiency is also very important when you need to anonymize big volumetry. This bundle aims to generate at most one 
query per table.

### Annotation configuration

This bundle provides 2 annotations:

`@AnonyizedEntity()` to specify which entity should be considered to be anonymized

- action option: anonymize(default)|truncate
- exceptWhereClause option: to keep some data unchanged, will be added as WHERE clause at the end of generated query


`@AnonyizedProperty()` to specify which entity field should be considered to be anonymized

- type option: static(default)|composed|expression

    - static type: same value will be applied everywhere
    - composed type: a string composed of an another `<field>` (ex : test-<id>@test.com) 
    - expression type: a valid expression where another field can be used or not (ex : CONCAT(FLOOR(1 + (RAND() * 1000)), id))

- value option: string representing static|composed|expression format

### Process

The command for anonymizing will :
- gather anonymizing configuration by reading annotation and build AnonymizedMetadata
- validate AnonymizedMetadata (see `Ekino\DataProtectionBundle\Meta\AnonymizedMetadataValidator`)
- build queries
- execute queries

### Usage

To anonymize your database, run the following command:

`bin/console ekino-data-protection:anonymize` will display the queries that have been generated
`bin/console ekino-data-protection:anonymize --force` will display & execute the queries that have been generated


[1]: https://php.net/manual/en/function.openssl-get-cipher-methods.php
[2]: https://github.com/Seldaek/monolog
[3]: https://github.com/sonata-project/SonataAdminBundle
