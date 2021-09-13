<?php

use BertProd\Translate\Sentence;
use BertProd\Translate\SentenceFactory;
use PHPUnit\Framework\TestCase;

final class SentenceFactoryTest extends TestCase
{
    private const ROOT_DIR = __DIR__.'/..';
    private const CONFIG_DIR = __DIR__.'/config';

    protected function setUp() : void
    {
        if ('cli' === PHP_SAPI) {
            $_SESSION = [];
        }
    }

    public function testCreateFromIni ()
    {
        $sentenceFactory = new SentenceFactory();

        $iniFile = self::CONFIG_DIR.'/sentence1.ini';
        $sentenceFile = self::ROOT_DIR.'/file/sentence-example.json';

        $sentence = $sentenceFactory->createFromIni($iniFile, $sentenceFile);

        $this->assertInstanceOf(Sentence::class, $sentence);

        $this->assertSame('en', $sentence->getCurrentLang());

        $sentence->setCurrentLang('fr');
        $this->assertSame('fr', $sentence->getCurrentLang());
    }

    public function testCurrentLangFromSession ()
    {
        $sentenceFactory = new SentenceFactory();

        $iniFile = self::CONFIG_DIR.'/sentence1.ini';
        $sentenceFile = self::ROOT_DIR.'/file/sentence-example.json';

        $config = parse_ini_file($iniFile, true);

        $_SESSION['BERTPROD_SENTENCE_LANG'] = 'de';

        $sentence = $sentenceFactory->createFromIni($iniFile, $sentenceFile);
        
        $this->assertSame('de', $sentence->getCurrentLang());
    }

    public function testCreateFromIniWithWrongIniFile ()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Requested ini file does not exist');

        $sentenceFactory = new SentenceFactory();

        $iniFile = self::ROOT_DIR.'/config/non-exists-sentence.ini-dist';
        $sentenceFile = self::ROOT_DIR.'/file/sentence-example.json';

        $sentence = $sentenceFactory->createFromIni($iniFile, $sentenceFile);
    }

    public function testCreateFromIniWithWrongSentenceFile ()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Sentence file does not exist');

        $sentenceFactory = new SentenceFactory();

        $iniFile = self::CONFIG_DIR.'/sentence1.ini';
        $sentenceFile = self::ROOT_DIR.'/file/non-existing-sentence-example.json';

        $sentence = $sentenceFactory->createFromIni($iniFile, $sentenceFile);
    }

    public function testGetTranslation ()
    {
        $sentenceFactory = new SentenceFactory();

        $iniFile = self::CONFIG_DIR.'/sentence1.ini';
        $sentenceFile = self::ROOT_DIR.'/file/sentence-example.json';

        $sentence = $sentenceFactory->createFromIni($iniFile, $sentenceFile);

        $this->assertSame('Hello world!', $sentence->getTranslation('LABEL_HELLO_WORLD'));

        // Repitition is made to check if coverage take it from cache.
        $this->assertSame('Hello world!', $sentence->getTranslation('LABEL_HELLO_WORLD'));

        // Same operation, but with default language:
        // First, switch to an other language:
        $sentence->setCurrentLang('nl');
        $this->assertSame('Other stuff', $sentence->getTranslation('LABEL_OTHER_LABEL'));
        $this->assertSame('Other stuff', $sentence->getTranslation('LABEL_OTHER_LABEL'));

        $this->assertSame('', $sentence->getTranslation('LABEL_FOR_SOMETHING_ELSE'));
        $this->assertSame('', $sentence->getTranslation('LABEL_FOR_SOMETHING_ELSE'));

    }

    public function testGetWrongTranslation ()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Required label NON_EXISTING_LABEL not found');

        $sentenceFactory = new SentenceFactory();

        $iniFile = self::CONFIG_DIR.'/sentence1.ini';
        $sentenceFile = self::ROOT_DIR.'/file/sentence-example.json';

        $sentence = $sentenceFactory->createFromIni($iniFile, $sentenceFile);

        $sentence->getTranslation('NON_EXISTING_LABEL');
    }
}
