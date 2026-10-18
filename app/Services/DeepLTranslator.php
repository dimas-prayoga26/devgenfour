<?php

namespace App\Services;

use App\Exceptions\TranslationFailed;
use Illuminate\Container\Attributes\Config;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class DeepLTranslator
{
    public function __construct(#[Config('services.deepl.key')] private ?string $key) {}

    /**
     * Determine whether an API key is available.
     */
    public function isConfigured(): bool
    {
        return filled($this->key);
    }

    /**
     * Translate Indonesian texts into English, returning them in the same order.
     *
     * @param  list<string>  $texts
     * @return list<string>
     *
     * @throws TranslationFailed
     */
    public function toEnglish(array $texts): array
    {
        if ($texts === []) {
            return [];
        }

        try {
            $response = Http::baseUrl($this->baseUrl())
                ->withHeaders(['Authorization' => 'DeepL-Auth-Key '.$this->key])
                ->acceptJson()
                ->connectTimeout(3)
                ->timeout(10)
                ->retry([300], 0, fn (Throwable $exception): bool => $exception instanceof ConnectionException
                    || ($exception instanceof RequestException && $this->isTemporary($exception->response)), throw: false)
                ->post('/v2/translate', [
                    'text' => $texts,
                    'source_lang' => 'ID',
                    'target_lang' => 'EN-US',
                ]);
        } catch (ConnectionException) {
            throw new TranslationFailed('Tidak dapat terhubung ke DeepL. Periksa koneksi internet server lalu coba lagi.');
        }

        if ($response->failed()) {
            Log::warning('DeepL translation failed.', [
                'status' => $response->status(),
                'message' => $response->json('message'),
                'trace_id' => $response->header('X-Trace-ID'),
            ]);

            throw new TranslationFailed($this->failureMessage($response));
        }

        return $response->collect('translations')->pluck('text')->all();
    }

    /**
     * Free keys end in ":fx" and must use the Free API host.
     */
    private function baseUrl(): string
    {
        return str_ends_with((string) $this->key, ':fx')
            ? 'https://api-free.deepl.com'
            : 'https://api.deepl.com';
    }

    private function isTemporary(Response $response): bool
    {
        return $response->serverError() || $response->status() === 429;
    }

    private function failureMessage(Response $response): string
    {
        return match ($response->status()) {
            403 => 'API key DeepL tidak valid. Periksa DEEPL_API_KEY di file .env.',
            456 => 'Kuota terjemahan DeepL bulan ini sudah habis.',
            429, 529 => 'Layanan DeepL sedang sibuk. Coba lagi beberapa saat lagi.',
            default => 'Terjemahan gagal. Coba lagi beberapa saat lagi.',
        };
    }
}
