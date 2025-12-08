<?php

namespace App\Services;

use App\Models\Word;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class WordbaseService
{
    public function __construct(
        private AnagramService $anagramService
    ) {}

    /**
     * Takes in a URL (that SHOULD contain a wordbase) and imports data on a per-batch basis
     * if AnagramService can't parse the URL properly, replaces current data in DB with an empty import
     *
     * @param string $url
     *
     * @return int number of added words
     */
    public function run(string $url)
    {
        $client = Http::timeout(30);

        // bandaid fix for missing certs
        if (!config('services.wordbase.verify_ssl', true)) {
            $client = $client->withoutVerifying();
        }
        $response = $client->get($url);

        if (!$response->successful()) {
            throw new RuntimeException('Failed to download remote wordbase.');
        }

        Word::truncate();

        // 1024 seems to be a good batch size
        $batch = [];
        $batchSize = 1024;
        $processed = 0;

        foreach (preg_split("/\r\n|\r|\n/", $response->body()) as $line) {
            $word = $this->anagramService->makeParseable($line);

            if ($word === '') {
                continue;
            }

            $batch[] = [
                'word' => $word,
                'signature' => $this->anagramService->signature($word),
            ];

            if (count($batch) >= $batchSize) {
                Word::insert($batch);
                $processed += count($batch);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            Word::insert($batch);
            $processed += count($batch);
        }

        return $processed;
    }
}
