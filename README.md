# translate
Small project to manage and display translations

## Motivation
I was looking for a very simple library to manage and display translations from a json file. Here is mine.

## Installation
You can install it through composer:
> composer require bertprod/translate

You'll need to set an ini configuration file. You'll find an example file in the `config` directory.

## System requirements
PHP >= 7.3 but latest stable version is highly recommanded

## Usage
Don't instantiate Sentence class, use factory.

~~~php
<?php
use BertProd\Translate\SentenceFactory;
?>
~~~

Currently, only ini files are supported for configuration. Sentences must be stored in json file.
You can use the file `file/sentence-example.json` as a base.

~~~php
<?php
$iniFile = '[PATH_TO_INI_CONFIGURATION_FILE]';
$sentenceFile = '[PATH_TO_JSON_SENTENCES_FILE]';

$sentence = $sentenceFactory->createFromIni($iniFile, $sentenceFile);
?>
~~~

You can get a sentence stored in the json file by using

~~~php
<?php
$sentence->getTranslation('LABEL_HELLO_WORLD');
?>
~~~

You can request sentences from an other language by switching it:

~~~php
<?php
$sentence->setCurrentLang('nl');
?>
~~~

If no translation is found, you'll get translation from default language (the one set in configuration file)

if directive `store_current_lang_to_session` in configuration file is set to `1`, the language you use will be stored in session (of course session value will be updated each time you call the method `setCurrentLang()`).
To not interfere with your script, this library do not use `session_start();` or `session_destroy();` you'll have to call these yourself.

## Testing
### Unit testing
You can run unit testing through PHPUnit:
> vendor/bin/phpunit

### Code sniffer
Code follow PSR12, to run test:
> vendor/bin/phpcs --standard=PSR12 src/

## License
MIT

## Credits
- Bertrand Andres
