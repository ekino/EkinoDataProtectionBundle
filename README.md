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

todo...
