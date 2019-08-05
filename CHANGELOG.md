CHANGELOG
=========

master
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
