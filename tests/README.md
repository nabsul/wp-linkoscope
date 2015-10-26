This directory contains various tests for the LinkoScope application.

Tests in `codeception` directory are developed with [Codeception PHP Testing Framework](http://codeception.com/).

After creating the basic application, follow these steps to prepare for the tests:

1. Install Codeception if it's not yet installed:

   ```
   composer global require "codeception/codeception=2.0.*"
   composer global require "codeception/specify=*"
   composer global require "codeception/verify=*"
   ```

   If you've never used Composer for global packages run `composer global status`. It should output:

   ```
   Changed current directory to <directory>
   ```

  Then add `<directory>/vendor/bin` to you `PATH` environment variable. Now we're able to use `codecept` from command
  line globally.

2. Install faker extension by running the following from template root directory where `composer.json` is:

   ```
   composer require --dev yiisoft/yii2-faker:*
   ```
   
3. Build the test suites:

   ```
   codecept build
   ```

4. Install Selenium and start it

5. Configuration

   You'll need to configure the web server root path in codeception/acceptance.suite.yml.
   
   You'll also need to copy config.template.json to config.json and fill in the required information.

6. Now you can run the tests with the following command:

   ```
   # run acceptance tests
   codecept run acceptance
   ```
