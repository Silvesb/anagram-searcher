<?php

namespace App\Services;

use App\Models\Word;

class AnagramService
{
    /**
     * Trimming+lowercasing strings for better handling
     *
     * @param string $word
     * @return string
     */
    public function makeParseable(string $word): string
    {
        return mb_strtolower(trim($word));
    }

    /**
     * Generates signatures for words
     *
     * @param string $word
     * @return string
     */
    public function signature(string $word): string
    {
        $letters = preg_split('//u', $word, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        sort($letters);

        return implode('', $letters);
    }

    /**
     * Find words that share the same signature
     *
     * @param string $word
     * @return array
     */
    public function findAnagrams(string $word): array
    {
        $parseable = $this->makeParseable($word);

        if ($parseable === '') {
            return [];
        }

        $signature = $this->signature($parseable);

        return Word::query()
            ->where('signature', $signature)
            ->where('word', '!=', $parseable)
            ->pluck('word')
            ->unique()
            ->values()
            ->all();
    }
}
