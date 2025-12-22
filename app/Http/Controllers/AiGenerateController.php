<?php

namespace App\Http\Controllers;

use App\Models\AiGenerate;
use App\Models\Pokdarwis;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
class AiGenerateController extends Controller
{
    /**
     * ==========
     * KONFIGURASI
     * ==========
     * Bisa diganti lewat .env
     */


    private function maxWords(): int   { return (int) env('AI_MAX_WORDS', 120); }
    private function minWords(): int   { return (int) env('AI_MIN_WORDS', 50); }
    private function maxChars(): int   { return (int) env('AI_MAX_CHARS', 900); }
    private function tokenFactor(): float { return (float) env('AI_MAX_TOKENS_FACTOR', 1.6); }

    private function model(): string
    {
        return config('services.openai.model', env('OPENAI_MODEL', 'gpt-4o'));
    }

    private function apiKey(): string
    {
        return (string) config('services.openai.key', env('OPENAI_API_KEY', ''));
    }

    /**
     * ================
     * HELPER FUNGSI DRY
     * ================
     */

    /** Cari pokdarwis_id dari input, product, atau user yang login */
    private function resolvePokdarwisId(Request $req, ?int $explicitPdwId, ?int $productId): ?int
    {
        if ($explicitPdwId) {
            return $explicitPdwId;
        }
        if ($productId) {
            return Product::whereKey($productId)->value('pokdarwis_id');
        }
        if ($req->user()) {
            if (isset($req->user()->pokdarwis_id) && $req->user()->pokdarwis_id) {
                return (int) $req->user()->pokdarwis_id;
            }
            return Pokdarwis::where('user_id', $req->user()->id)->value('id');
        }
        return null;
    }

    /** Prompt sistem dengan batas kata */
    private function buildSystemPrompt(string $lang, int $minWords, int $maxWords): string
    {
        if ($lang === 'en') {
            return "You are a tourism/SME copywriter. WRITE AT MOST {$maxWords} words (ideal {$minWords}-{$maxWords}). Persuasive and honest, no emojis or contact info. One paragraph.";
        }
        return "Kamu copywriter pariwisata/UMKM. TULIS MAKSIMUM {$maxWords} kata (ideal {$minWords}–{$maxWords}). Persuasif dan jujur, tanpa emoji & tanpa info kontak. Satu paragraf.";
    }

    /** Potong aman berdasarkan kata */
    private function truncateByWords(string $text, int $maxWords): string
    {
        $text = trim($text);
        if ($text === '') return $text;

        $words = preg_split('/\s+/u', $text);
        if (!$words) return $text;

        if (count($words) <= $maxWords) return $text;

        $cut = array_slice($words, 0, $maxWords);
        $out = rtrim(implode(' ', $cut));
        if (!preg_match('/[.!?…]$/u', $out)) {
            $out .= '…';
        }
        return $out;
    }

    /** Potong aman berdasarkan karakter (opsional pagar kedua) */
    private function truncateByChars(string $text, int $maxChars): string
    {
        $text = trim($text);
        if (mb_strlen($text) <= $maxChars) return $text;

        $cut = mb_substr($text, 0, $maxChars);
        $lastSpace = mb_strrpos($cut, ' ');
        if ($lastSpace !== false) {
            $cut = mb_substr($cut, 0, $lastSpace);
        }
        $cut = rtrim($cut);
        if (!preg_match('/[.!?…]$/u', $cut)) {
            $cut .= '…';
        }
        return $cut;
    }

    /** Hitung perkiraan max_tokens untuk output */
    private function maxTokensForWords(int $maxWords, float $factor): int
    {
        return (int) ceil($maxWords * $factor);
    }

    /**
     * ===========
     * JSON ENDPOINT
     * ===========
     */
    public function generate(Request $req)
    {
        $data = $req->validate([
            'prompt'        => ['required','string','min:5','max:2000'],
            'pokdarwis_id'  => ['nullable','integer','exists:pokdarwis,id'],
            'product_id'    => ['nullable','integer','exists:products,id'],
            'language'      => ['nullable','in:id,en'],
        ]);

        $pokdarwisId = $this->resolvePokdarwisId(
            $req,
            $data['pokdarwis_id'] ?? null,
            $data['product_id'] ?? null
        );

        if (!$pokdarwisId) {
            return response()->json([
                'ok' => false,
                'message' => 'pokdarwis_id tidak boleh kosong. Kirimkan product_id yang valid atau pokdarwis_id.'
            ], 422);
        }

        $lang      = $data['language'] ?? 'id';
        $minWords  = $this->minWords();
        $maxWords  = $this->maxWords();
        $maxTokens = $this->maxTokensForWords($maxWords, $this->tokenFactor());
        $system    = $this->buildSystemPrompt($lang, $minWords, $maxWords);

        $apiKey = $this->apiKey();
        $model  = $this->model();

        try {
            $resp = Http::withToken($apiKey)
                ->timeout(40)
                // ->withOptions(['verify' => false]) // jika perlu saat dev di Windows/XAMPP
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'system', 'content' => $system],
                        ['role' => 'user',   'content' => $data['prompt']],
                    ],
                    'temperature' => 0.7,
                    'max_tokens'  => $maxTokens, // ⬅️ LIMIT OUTPUT
                ]);

            if (!$resp->ok()) {
                return response()->json([
                    'ok' => false,
                    'message' => $resp->json('error.message') ?? ('HTTP '.$resp->status()),
                ], 500);
            }

            $text = trim($resp->json('choices.0.message.content') ?? '');

            // 3) POST-PROCESS: potong aman
            $text = $this->truncateByWords($text, $maxWords);
            $text = $this->truncateByChars($text, $this->maxChars()); // opsional

            $row = AiGenerate::create([
                'prompt_text'  => $data['prompt'],
                'result_text'  => $text,
                'pokdarwis_id' => $pokdarwisId,
                'product_id'   => $data['product_id'] ?? null,
                'language'     => $lang,
                'model'        => $model,
            ]);

            return response()->json([
                'ok'       => true,
                'text'     => $text,
                'saved_id' => $row->id,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * ===========
     * INVOKABLE (GET form / POST generate + render view)
     * ===========
     */
    public function __invoke(Request $req)
    {
        if ($req->isMethod('get')) {
            return view('pokdarwis', ['result' => null, 'saved_id' => null]);
        }

        $data = $req->validate([
            'prompt'   => ['required','string','max:1000'],
            'language' => ['nullable','in:id,en'],
            'pokdarwis_id' => ['nullable','integer','exists:pokdarwis,id'],
            'product_id'   => ['nullable','integer','exists:products,id'],
        ]);

        $pokdarwisId = $this->resolvePokdarwisId(
            $req,
            $data['pokdarwis_id'] ?? null,
            $data['product_id'] ?? null
        );
        if (!$pokdarwisId) {
            return back()->withInput()->withErrors(['prompt' => 'Pokdarwis belum terdeteksi.']);
        }

        $lang      = $data['language'] ?? 'id';
        $minWords  = $this->minWords();
        $maxWords  = $this->maxWords();
        $maxTokens = $this->maxTokensForWords($maxWords, $this->tokenFactor());
        $system    = $this->buildSystemPrompt($lang, $minWords, $maxWords);

        $payload = [
            'model'       => $this->model(),
            'messages'    => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user',   'content' => $data['prompt']],
            ],
            'temperature' => 0.8,
            'max_tokens'  => $maxTokens, // ⬅️ LIMIT OUTPUT
        ];

        $resp = Http::withToken($this->apiKey())
            ->timeout(30)
            ->post('https://api.openai.com/v1/chat/completions', $payload);

        if ($resp->failed()) {
            $err = $resp->json();
            return back()->withInput()->withErrors(['prompt' => 'Gagal generate AI: '.json_encode($err)]);
        }

        $text = trim($resp->json('choices.0.message.content') ?? '');

        // 3) POST-PROCESS: potong aman
        $text = $this->truncateByWords($text, $maxWords);
        $text = $this->truncateByChars($text, $this->maxChars()); // opsional

        $row = AiGenerate::create([
            'prompt_text'  => $data['prompt'],
            'result_text'  => $text,
            'pokdarwis_id' => $pokdarwisId, // ⬅️ BUKAN user()->id
            'product_id'   => $data['product_id'] ?? null,
            'language'     => $lang,
            'model'        => $this->model(),
        ]);

        return view('pokdarwis', [
            'result'   => $text,
            'saved_id' => $row->id,
        ])->with('ok', true);
    }

    public function page()
    {
        return view('ai.generate');
    }

    public function index(Request $req)
    {
        $pokdarwisId = $this->resolvePokdarwisId($req, null, null);
        abort_if(!$pokdarwisId, 403, 'Pokdarwis tidak ditemukan.');

        $items = AiGenerate::where('pokdarwis_id', $pokdarwisId)
            ->latest()->paginate(15);

        // ganti ke view yang benar (contoh)
        return view('ai.index', compact('items'));
    }

    public function create() {}
    public function store(Request $request) {}
    public function show(AiGenerate $aiGenerate) {}
    public function edit(AiGenerate $aiGenerate) {}
    public function update(Request $request, AiGenerate $aiGenerate) {}
    public function destroy(AiGenerate $aiGenerate) {}
}
