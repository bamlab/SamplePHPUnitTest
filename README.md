Sample Tests in PHP
===================

This is a sample of how to do unit test on a ream example.
Every step of the process is marked with a tag.

## Installation

```bash
git clone --recurse-submodules https://github.com/bamlab/SamplePHPUnitTest.git

```

The project include a vagrant box to run the test. You will need virtualbox, vagrant and ansible. At soon as it's done, run :
```bash
cd vagrant
vagrant up
vagrant ssh
```

You are now in the vagrant. To complete the installation, run :
```bash
cd /var/www
composer install
```

## Run the tests

To run the test, you will need to be in the vagrant ;
```bash
cd vagrant
vagrant up
vagrant ssh
```

Then run :
```bash
cd /var/www
./bin/phpunit -c config
```
