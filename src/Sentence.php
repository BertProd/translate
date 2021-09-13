<?php
/**
 * Don't instantiate this class, use SentenceFactory.
 */
namespace BertProd\Translate;

use InvalidArgumentException;

final class Sentence
{
    public const CONFIG_DEFAULT_LANG = 'default_lang';
    public const CONFIG_STORE_CURRENT_LANG_TO_SESSION = 'store_current_lang_to_session';
    public const CONFIG_SESSION_VARIABLE_NAME = 'session_variable_name';

    private array $config = [
        self::CONFIG_DEFAULT_LANG => '',
        self::CONFIG_STORE_CURRENT_LANG_TO_SESSION => '',
        self::CONFIG_SESSION_VARIABLE_NAME => ''
    ];

    private string $currentLang = '';

    private string $sentenceFile = '';

    private array $cache = [];

    public function __construct(array $pConfig, string $pSentenceFile)
    {
        $this->config[self::CONFIG_DEFAULT_LANG] = $pConfig[self::CONFIG_DEFAULT_LANG];
        $this->config[self::CONFIG_STORE_CURRENT_LANG_TO_SESSION] = $pConfig[self::CONFIG_STORE_CURRENT_LANG_TO_SESSION];

        if (true === $this->config[self::CONFIG_STORE_CURRENT_LANG_TO_SESSION]) {
            $this->config[self::CONFIG_SESSION_VARIABLE_NAME] = $pConfig[self::CONFIG_SESSION_VARIABLE_NAME];
        }

        $this->setCurrentLang($this->getCurrentLang());
        $this->sentenceFile = $pSentenceFile;
    }

    public function setCurrentLang (string $pLang) : void
    {
        $this->currentLang = $pLang;

        if (true === $this->config[self::CONFIG_STORE_CURRENT_LANG_TO_SESSION]) {
            $_SESSION[$this->config[self::CONFIG_SESSION_VARIABLE_NAME]] = $pLang;
        }
    }

    public function getCurrentLang() : string
    {
        if ('' !== $this->currentLang) {
            return $this->currentLang;
        }

        if (
                false === $this->config[self::CONFIG_STORE_CURRENT_LANG_TO_SESSION]
            ||  !array_key_exists($this->config[self::CONFIG_SESSION_VARIABLE_NAME], $_SESSION)
            ||  ('' === $_SESSION[$this->config[self::CONFIG_SESSION_VARIABLE_NAME]])
        ) {
            return $this->config[self::CONFIG_DEFAULT_LANG];
        }

        return $_SESSION[$this->config[self::CONFIG_SESSION_VARIABLE_NAME]];
    }

    public function getTranslation (string $pLabel) : string
    {
        $cachedTranslation = $this->fetchTranslationFromCache($pLabel);

        if ('' !== $cachedTranslation) {
            return $cachedTranslation;
        }

        $data = json_decode(file_get_contents($this->sentenceFile), true);

        if (!array_key_exists($pLabel, $data)) {
            throw new InvalidArgumentException('Required label '.$pLabel.' not found');
        }

        $this->cache[$pLabel] = $data[$pLabel];

        return $this->fetchTranslationFromCache($pLabel);
    }

    private function fetchTranslationFromCache(string $pLabel) : string
    {
        if (!array_key_exists($pLabel, $this->cache)) {
            return '';
        }

        $currentLang = $this->getCurrentLang();

        if (array_key_exists($currentLang, $this->cache[$pLabel])) {
            return $this->cache[$pLabel][$currentLang];
        }

        if (array_key_exists($this->config[self::CONFIG_DEFAULT_LANG], $this->cache[$pLabel])) {
            return $this->cache[$pLabel][$this->config[self::CONFIG_DEFAULT_LANG]];
        }

        return '';
    }
}
