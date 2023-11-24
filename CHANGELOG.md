CHANGELOG
=========

master
------

* Bump PHP version from ^8.0 to ^8.1 and configure CI with 8.1, 8.2 and 8.3

v2.0.0
------

* Drop support for PHP 7
* Bump PHPStan from ^0.12 to ^1.0
* Allow Symfony 6
* Drop support for Symfony 3
* Support only sonata-project/admin-bundle ^4.0 and sonata-project/twig-extensions ^2.0

v1.3.1
------

* Upgrade friendsofphp/php-cs-fixer
* Improve PHPStan configuration file
* Raised to PHPStan level 8
* Add PHP 8.1 in CI
* Add native return types

v1.3.0
------

* Switch to the new security checker
* Migrate from Travis to GitHub Actions
* Allow PHP 8 and Symfony 5
* Fix depreciated PHP-CS rules

v1.2.0
------

* Improve message in case of encryption error
* Add command to encrypt secrets
* Drop support for PHP 7.1
* Add PHP 7.4 in CI
* Upgrade PhpUnit to 8
* Make symfony dependencies explicit, add sonata twig as dev dep & remove SonataCoreBundle occurrence

v1.1.0
------

* Allow monolog/monolog ~2.0

v1.0.0
------

* Add monolog processor to encrypt logs
* Add optional sonata admin to decrypt logs
* Add processor to decrypt secrets at runtime
* Add optional sonata admin to encrypt content
* Enable strict typing
* GdprProcessor: cast context keys to string to avoid FatalThrowableError with numeric keys
* Add TravisCI matrix
* Fix Coveralls tool export
* Fix badges
* Disable audit of Log (cf. https://sonata-project.org/bundles/doctrine-orm-admin/master/doc/reference/audit.html)
* Remove deprecation about configuration tree builder without a root node
* Throw EncryptionException when content can't be encrypted/decrypted and display flash message in admin
