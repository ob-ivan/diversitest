DiversiTest
===========
Run your tests on a spectre of dependencies' versions.

Installation
------------
Add it as a composer dependency:

```
composer require ob-ivan/diversitest
```

Configuration
-------------
In your project root create a file named `diversitest.yaml` like follows:

```yaml
package_manager: 'composer require $package $version'
test_runner: 'vendor/bin/phpunit'
packages:
    dependency_name_1:
        - ^1.2
        - ~2.0.3
    dependency_name_2:
        - ^3.3
        - ^4.0
```

In the `package_manager` line enter the command for installing packages with designated versions. In the `test_runner`
line enter the command for running your test suite.

Under the `packages` line list all packages whose versions you want to vary. List the versions as a simple array.

Running
-------
After you've installed `diversitest` and provided a config file, run it with this command:

```
vendor/bin/diversitest
```

This will copy your working directory to a temporary folder, then for each combination of package versions you listed it
will run the command from `package_manager` key followed by the command from `test_runner` key.

The output is sent to your display. It's up to you to handle it.

Running with an alternative PHP executable
------------------------------------------
If your PHP executable is not `php` but something like `php7.4`, you can run the `diversitest` likes this:

```
php7.4 vendor/bin/diversitest
```
