<?php

namespace Tests\Feature;

use App\Exceptions\TranslationFailed;
use App\Services\DeepLTranslator;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class DeepLTranslatorTest extends TestCase
{
    public function test_free_keys_translate_indonesian_into_english_through_the_free_endpoint(): void
    {
        Http::fake(['api-free.deepl.com/v2/translate' => Http::response(['translations' => [
            ['detected_source_language' => 'ID', 'text' => 'Online Store'],
            ['detected_source_language' => 'ID', 'text' => 'Payment'],
        ]])]);

        $translations = (new DeepLTranslator('kunci-uji:fx'))->toEnglish(['Toko Online', 'Pembayaran']);

        $this->assertSame(['Online Store', 'Payment'], $translations);
        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api-free.deepl.com/v2/translate'
            && $request->hasHeader('Authorization', 'DeepL-Auth-Key kunci-uji:fx')
            && $request['text'] === ['Toko Online', 'Pembayaran']
            && $request['source_lang'] === 'ID'
            && $request['target_lang'] === 'EN-US');
    }

    public function test_pro_keys_use_the_pro_endpoint(): void
    {
        Http::fake(['api.deepl.com/v2/translate' => Http::response(['translations' => [
            ['detected_source_language' => 'ID', 'text' => 'Online Store'],
        ]])]);

        (new DeepLTranslator('kunci-pro'))->toEnglish(['Toko Online']);

        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api.deepl.com/v2/translate');
    }

    /**
     * @return array<string, array{int, string}>
     */
    public static function deeplFailures(): array
    {
        return [
            'invalid key' => [403, 'API key DeepL tidak valid. Periksa DEEPL_API_KEY di file .env.'],
            'quota exceeded' => [456, 'Kuota terjemahan DeepL bulan ini sudah habis.'],
            'rate limited' => [429, 'Layanan DeepL sedang sibuk. Coba lagi beberapa saat lagi.'],
            'server error' => [500, 'Terjemahan gagal. Coba lagi beberapa saat lagi.'],
        ];
    }

    #[DataProvider('deeplFailures')]
    public function test_deepl_errors_become_readable_messages(int $status, string $message): void
    {
        Sleep::fake();
        Http::fake(['api-free.deepl.com/v2/translate' => Http::response(['message' => 'DeepL error'], $status)]);

        $this->expectException(TranslationFailed::class);
        $this->expectExceptionMessage($message);

        (new DeepLTranslator('kunci-uji:fx'))->toEnglish(['Toko Online']);
    }

    public function test_busy_deepl_is_retried_once_before_giving_up(): void
    {
        Sleep::fake();
        Http::fake(['api-free.deepl.com/v2/translate' => Http::response(['message' => 'Too many requests'], 429)]);

        try {
            (new DeepLTranslator('kunci-uji:fx'))->toEnglish(['Toko Online']);
        } catch (TranslationFailed) {
            // The failure itself is covered by the data provider test.
        }

        Http::assertSentCount(2);
        Sleep::assertSleptTimes(1);
    }

    public function test_unreachable_deepl_reports_a_connection_problem(): void
    {
        Sleep::fake();
        Http::fake(['api-free.deepl.com/*' => Http::failedConnection()]);

        $this->expectException(TranslationFailed::class);
        $this->expectExceptionMessage('Tidak dapat terhubung ke DeepL. Periksa koneksi internet server lalu coba lagi.');

        (new DeepLTranslator('kunci-uji:fx'))->toEnglish(['Toko Online']);
    }

    public function test_nothing_is_sent_when_there_is_nothing_to_translate(): void
    {
        Http::fake(['api-free.deepl.com/*' => Http::response(['translations' => []])]);

        $translations = (new DeepLTranslator('kunci-uji:fx'))->toEnglish([]);

        $this->assertSame([], $translations);
        Http::assertNothingSent();
    }
}
