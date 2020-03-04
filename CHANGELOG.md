CHANGELOG
=========

master
------

* Improve message in case of encryption error
* Add command to encrypt secrets
* Drop support for PHP 7.1
* Add PHP 7.4 in CI
* Upgrade PhpUnit to 8
* Add command to anonymize database through annotation configuration

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
