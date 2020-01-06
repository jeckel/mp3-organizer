<?php

namespace App\Service;

class StringHelper
{
    protected const ARTICLES = [
        'the',
        'les',
    ];

    /**
     * @param string $text
     * @param bool   $removeArticles
     * @return string
     */
    public function normalize(string $text, bool $removeArticles = false): string
    {
        $text = strtolower($this->cleanString($text));
        if ($removeArticles) {
            foreach (self::ARTICLES as $article) {
                if (strpos($text, $article) === 0) {
                    $text = substr($text, strlen($article));
                    break;
                }
            }
        }

        return trim($text);
    }

    /**
     * @param string $text
     * @return string
     */
    public function cleanString(string $text): string
    {
        $utf8 = array(
            '/[áàâãªä]/u'   =>   'a',
            '/[ÁÀÂÃÄ]/u'    =>   'A',
            '/[ÍÌÎÏİ]/u'     =>  'I',
            '/[íìîï]/u'     =>   'i',
            '/[éèêë]/u'     =>   'e',
            '/[ÉÈÊË]/u'     =>   'E',
            '/[óòôõºö]/u'   =>   'o',
            '/[ÓÒÔÕÖ]/u'    =>   'O',
            '/ş/u'          =>   's',
            '/Ş/u'          =>   'S',
            '/[úùûü]/u'     =>   'u',
            '/[ÚÙÛÜ]/u'     =>   'U',
            '/ç/'           =>   'c',
            '/Ç/'           =>   'C',
            '/ñ/'           =>   'n',
            '/Ñ/'           =>   'N',
            '/–/'           =>   '-', // UTF-8 hyphen to "normal" hyphen
            '/[’‘‹›‚]/u'    =>   ' ', // Literally a single quote
            '/[“”«»„]/u'    =>   ' ', // Double quote
            '/ /'           =>   ' ', // nonbreaking space (equiv. to 0x160)
        );

        return preg_replace(
            '/[^A-Za-z0-9\- ]/',
            '',
            preg_replace(array_keys($utf8), array_values($utf8), $text)
        );
    }
}
