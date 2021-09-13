<?php

namespace BertProd\Translate;

use InvalidArgumentException;

final class SentenceFactory
{
    private const SECTION_CONFIG = 'configuration';
    private const CONFIG_DEFAULT_LANG = 'default_lang';
    private const CONFIG_STORE_CURRENT_LANG_TO_SESSION = 'store_current_lang_to_session';
    private const CONFIG_SESSION_VARIABLE_NAME = 'session_variable_name';

    public function createFromIni(string $pPathIniFile, string $pSentenceFile) : Sentence
    {
        if (!file_exists($pPathIniFile)) {
            throw new InvalidArgumentException('Requested ini file does not exist');
        }

        if (!file_exists($pSentenceFile)) {
            throw new InvalidArgumentException('Sentence file does not exist');
        }

        $iniData = parse_ini_file($pPathIniFile, true);

        $storeToSession = '1' === $iniData[self::SECTION_CONFIG][self::CONFIG_STORE_CURRENT_LANG_TO_SESSION] ? true : false;

        $data = [
            Sentence::CONFIG_DEFAULT_LANG => $iniData[self::SECTION_CONFIG][self::CONFIG_DEFAULT_LANG],
            Sentence::CONFIG_STORE_CURRENT_LANG_TO_SESSION => $storeToSession,
            Sentence::CONFIG_SESSION_VARIABLE_NAME => $iniData[self::SECTION_CONFIG][self::CONFIG_SESSION_VARIABLE_NAME]
        ];

        return new Sentence($data, $pSentenceFile);
    }
}