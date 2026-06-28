<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Exception;
use App\Models\Perusahaan;
use App\Models\Barang;
use App\Models\SuratJalan;
use App\Models\PesananPenjualan;
use App\Models\SuratPenawaranHarga;

class AsistenKeuangan extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static string $view = 'filament.pages.asisten-keuangan';
    protected static ?string $title = 'Asisten Keuangan AI';
    protected static ?int $navigationSort = -100;

    public array $percakapan = [];
    public string $pertanyaan = '';
    public bool $isLoading = false;

    public function mount(): void
    {
        $this->percakapan[] = ['role' => 'assistant', 'content' => 'Halo! Saya adalah asisten keuangan AI Anda. Anda bisa bertanya tentang data atau memberi perintah seperti "Buatkan surat jalan untuk PT ABC...".'];
    }

    public function submit()
    {
        $pertanyaanUser = trim($this->pertanyaan);
        if (empty($pertanyaanUser))
            return;

        $this->percakapan[] = ['role' => 'user', 'content' => $pertanyaanUser];
        $this->isLoading = true;
        $this->pertanyaan = '';

        try {
            $jawabanAI = $this->getAIResponse($pertanyaanUser);
            $this->percakapan[] = ['role' => 'assistant', 'content' => $jawabanAI];
        } catch (Exception $e) {
            $this->percakapan[] = ['role' => 'assistant', 'content' => 'Maaf, terjadi kesalahan: ' . $e->getMessage()];
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Fungsi utama untuk memproses pertanyaan ke AI.
     */
    // app/Filament/Pages/AsistenKeuangan.php

    protected function getAIResponse(string $pertanyaan): string
    {
        $openaiKey = config('services.openai.key');
        if (!$openaiKey) {
            throw new Exception("Kunci API OpenAI belum diatur.");
        }

        $strukturDb = $this->getSchemaAsString();
        $definisiFungsi = $this->getFunctionDefinitions();

        // --- PROMPT BARU YANG LEBIH TEGAS DAN DETAIL ---
        $systemPrompt = <<<EOT
    Anda adalah AI Dispatcher yang cerdas dan patuh pada format. Tugas Anda adalah mengubah permintaan user menjadi JSON untuk memanggil fungsi ATAU menjadi query SQL murni.

    ATURAN KETAT:
    1.  JIKA permintaan adalah perintah (membuat, input, tambah), JAWAB HANYA DENGAN JSON.
    2.  JIKA permintaan adalah pertanyaan (siapa, berapa, tampilkan), JAWAB HANYA DENGAN SQL QUERY.
    3.  JANGAN PERNAH menambahkan kata-kata pembuka atau penutup seperti "Tentu", "Berikut adalah JSON-nya", atau penjelasan apa pun.
    4.  JANGAN PERNAH menggunakan markdown seperti ```json atau ```sql.
    5.  JAWABAN ANDA HARUS BERUPA JSON MURNI ATAU SQL MURNI.

    Struktur Database:
    $strukturDb

    Definisi Fungsi yang Tersedia:
    $definisiFungsi

    CONTOH:
    User: "buatkan surat jalan untuk PT ABC, item 5 laptop"
    Jawaban Anda: {"action": "create_surat_jalan", "parameters": {"nama_perusahaan": "PT ABC", "items": [{"nama_barang": "Laptop", "kuantitas": 5}]}}

    User: "siapa pelanggan terbaru kita?"
    Jawaban Anda: SELECT nama, alamat FROM perusahaans ORDER BY created_at DESC LIMIT 1;
    EOT;

        $client = new Client(['timeout' => 60, 'verify' => false]);
        $responseAI = $client->post('https://api.openai.com/v1/chat/completions', [
            'headers' => ['Authorization' => 'Bearer ' . $openaiKey, 'Content-Type' => 'application/json'],
            'json' => [
                'model' => 'gpt-4o-mini',
                // Kita pisahkan antara instruksi sistem dan pertanyaan user
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $pertanyaan]
                ]
            ]
        ]);

        $jawabanAI = json_decode($responseAI->getBody()->getContents(), true)['choices'][0]['message']['content'] ?? '';

        // Cek apakah jawaban AI adalah JSON (perintah untuk menjalankan fungsi)
        $decodedJson = json_decode($jawabanAI, true);
        if (json_last_error() === JSON_ERROR_NONE && isset($decodedJson['action'])) {
            $action = $decodedJson['action'];
            $parameters = $decodedJson['parameters'] ?? [];

            if ($action === 'create_surat_jalan') {
                return $this->executeCreateSuratJalan($parameters);
            } elseif ($action === 'create_pesanan_penjualan') {
                return $this->executeCreatePesananPenjualan($parameters);
            } elseif ($action === 'create_penawaran_harga') {
                return $this->executeCreatePenawaranHarga($parameters);
            }
            // --- TAMBAHKAN BLOK ELSEIF INI ---
            elseif ($action === 'create_barang') {
                return $this->executeCreateBarang($parameters);
            } elseif ($action === 'create_perusahaan') {
                return $this->executeCreatePerusahaan($parameters);
            }
            // --- SAMPAI SINI ---

            return "Maaf, saya tidak mengenali fungsi tersebut.";
        }

        // Jika bukan JSON, anggap itu adalah SQL
        preg_match('/SELECT\s.+?;?$/is', trim($jawabanAI), $matches);
        $sql = $matches[0] ?? null;

        if (!$sql) {
            // Jika AI tetap memberikan jawaban naratif, tampilkan saja langsung
            return $jawabanAI;
        }

        $data = DB::select(rtrim($sql, ';'));
        if (empty($data)) {
            return 'Saya tidak menemukan data yang sesuai dengan pertanyaan Anda.';
        }

        // Proses jawaban naratif
        return $this->generateNarrativeResponse($pertanyaan, $data, $openaiKey);
    }

    /**
     * Fungsi untuk mengeksekusi pembuatan Surat Jalan.
     */
    private function executeCreateSuratJalan(array $params): string
    {
        return DB::transaction(function () use ($params) {
            $perusahaan = Perusahaan::where('nama', 'like', '%' . $params['nama_perusahaan'] . '%')->first();
            if (!$perusahaan) {
                throw new Exception("Perusahaan '{$params['nama_perusahaan']}' tidak ditemukan di database.");
            }

            $suratJalan = SuratJalan::create([
                'perusahaan_id' => $perusahaan->id,
                'po_no' => $params['po_no'] ?? null,
                'tanggal' => now(),
                'keterangan' => $params['keterangan'] ?? 'Dibuat oleh Asisten AI',
            ]);

            foreach ($params['items'] as $item) {
                $barang = Barang::where('nama', 'like', '%' . $item['nama_barang'] . '%')->first();
                if (!$barang) {
                    throw new Exception("Barang '{$item['nama_barang']}' tidak ditemukan.");
                }
                $suratJalan->items()->create([
                    'barang_id' => $barang->id,
                    'kuantitas' => $item['kuantitas'],
                    'satuan' => $item['satuan'] ?? 'Unit',
                ]);
                $barang->decrement('stok', $item['kuantitas']);
            }

            return "Berhasil! Surat Jalan dengan nomor **{$suratJalan->nomor}** untuk **{$perusahaan->nama}** telah dibuat.";
        });
    }

    /**
     * Fungsi untuk mengeksekusi pembuatan Pesanan Penjualan.
     */
    private function executeCreatePesananPenjualan(array $params): string
    {
        return DB::transaction(function () use ($params) {
            $perusahaan = Perusahaan::where('nama', 'like', '%' . $params['nama_perusahaan'] . '%')->first();
            if (!$perusahaan)
                throw new Exception("Perusahaan '{$params['nama_perusahaan']}' tidak ditemukan.");

            $itemsData = $params['items'] ?? [];
            if (empty($itemsData))
                throw new Exception("Daftar barang tidak boleh kosong.");

            $subtotal = 0;
            foreach ($itemsData as $item) {
                $barang = Barang::where('nama', 'like', '%' . $item['nama_barang'] . '%')->first();
                if (!$barang)
                    throw new Exception("Barang '{$item['nama_barang']}' tidak ditemukan.");
                $subtotal += ((int) $item['kuantitas'] * (float) $barang->harga) - (float) ($item['diskon'] ?? 0);
            }

            $pesanan = PesananPenjualan::create([
                'perusahaan_id' => $perusahaan->id,
                'po_no' => $params['po_no'] ?? null,
                'tanggal_pesanan' => now(),
                'tanggal_pengiriman' => $params['tanggal_pengiriman'] ?? now()->addDay(),
                'keterangan' => $params['keterangan'] ?? 'Dibuat oleh Asisten AI',
                'subtotal' => $subtotal,
                'total' => $subtotal, // Disederhanakan, bisa ditambah PPN dll.
                'terbilang' => function_exists('terbilang') ? ucwords(terbilang($subtotal)) : '',
            ]);

            foreach ($itemsData as $item) {
                $barang = Barang::where('nama', 'like', '%' . $item['nama_barang'] . '%')->first();
                $kuantitas = (int) $item['kuantitas'];
                $harga = (float) $barang->harga;
                $diskon = (float) ($item['diskon'] ?? 0);
                $pesanan->items()->create([
                    'barang_id' => $barang->id,
                    'kuantitas' => $kuantitas,
                    'harga' => $harga,
                    'diskon' => $diskon,
                    'total_harga' => ($kuantitas * $harga) - $diskon,
                ]);
            }

            return "Berhasil! Pesanan Penjualan dengan nomor **{$pesanan->nomor_surat}** untuk **{$perusahaan->nama}** telah dibuat.";
        });
    }

    /**
     * Fungsi untuk mengeksekusi pembuatan Penawaran Harga.
     */
    private function executeCreatePenawaranHarga(array $params): string
    {
        return DB::transaction(function () use ($params) {
            $perusahaan = Perusahaan::where('nama', 'like', '%' . $params['nama_perusahaan'] . '%')->first();
            if (!$perusahaan)
                throw new Exception("Perusahaan '{$params['nama_perusahaan']}' tidak ditemukan.");

            $penawaran = SuratPenawaranHarga::create([
                'perusahaan_id' => $perusahaan->id,
                'tanggal' => now(),
                'pembayaran' => $params['pembayaran'],
                'keterangan' => $params['keterangan'] ?? 'Dibuat oleh Asisten AI',
            ]);

            foreach ($params['items'] as $item) {
                $barang = Barang::where('nama', 'like', '%' . $item['nama_barang'] . '%')->first();
                if (!$barang)
                    throw new Exception("Barang '{$item['nama_barang']}' tidak ditemukan.");
                $penawaran->items()->create([
                    'barang_id' => $barang->id,
                    'kuantitas' => $item['kuantitas'],
                    'harga' => $barang->harga,
                    'diskon' => $item['diskon'] ?? 0,
                ]);
            }

            return "Berhasil! Penawaran Harga dengan nomor **{$penawaran->nomor}** untuk **{$perusahaan->nama}** telah dibuat.";
        });
    }

    // app/Filament/Pages/AsistenKeuangan.php

    private function executeCreateBarang(array $params): string
    {
        // Validasi parameter yang wajib ada
        if (empty($params['kode']) || empty($params['nama']) || !isset($params['harga']) || !isset($params['stok'])) {
            throw new Exception("Untuk membuat barang baru, saya memerlukan informasi kode, nama, harga, dan stok.");
        }

        // Cek apakah kode barang sudah ada untuk menghindari duplikat
        if (Barang::where('kode', $params['kode'])->exists()) {
            throw new Exception("Barang dengan kode '{$params['kode']}' sudah ada di database.");
        }

        $barang = Barang::create([
            'kode' => $params['kode'],
            'nama' => $params['nama'],
            'kategori' => $params['kategori'] ?? 'Umum',
            'harga' => (float) $params['harga'],
            'stok' => (int) $params['stok'],
        ]);

        return "Berhasil! Barang baru **{$barang->nama}** dengan kode **{$barang->kode}** telah ditambahkan ke database.";
    }

    // app/Filament/Pages/AsistenKeuangan.php

    private function executeCreatePerusahaan(array $params): string
    {
        // Validasi parameter yang wajib ada
        if (empty($params['nama']) || empty($params['alamat'])) {
            throw new Exception("Untuk membuat perusahaan baru, saya memerlukan informasi nama dan alamat.");
        }

        $perusahaan = Perusahaan::create([
            'nama' => $params['nama'],
            'alamat' => $params['alamat'],
            'telepon' => $params['telepon'] ?? null,
            'email' => $params['email'] ?? null,
            'npwp' => $params['npwp'] ?? null,
        ]);

        return "Berhasil! Perusahaan baru **{$perusahaan->nama}** telah ditambahkan ke database.";
    }

    /**
     * Mendefinisikan fungsi-fungsi yang bisa dipahami oleh AI.
     */
    private function getFunctionDefinitions(): string
    {
        return <<<EOT
- `create_surat_jalan(nama_perusahaan: string, po_no: string | null, items: array<{nama_barang: string, kuantitas: int, satuan: string | null}>, keterangan: string | null)`: Membuat dokumen Surat Jalan (Delivery Order) baru.
- `create_pesanan_penjualan(nama_perusahaan: string, po_no: string | null, tanggal_pengiriman: string | null, items: array<{nama_barang: string, kuantitas: int, diskon: float | null}>, keterangan: string | null)`: Membuat dokumen Pesanan Penjualan (Sales Order) baru.
- `create_penawaran_harga(nama_perusahaan: string, pembayaran: string, items: array<{nama_barang: string, kuantitas: int, diskon: float | null}>, keterangan: string | null)`: Membuat dokumen Penawaran Harga (Sales Quotation) baru.
- `create_barang(kode: string, nama: string, kategori: string, harga: float, stok: int)`: Menambahkan data master barang baru ke database.
- `create_perusahaan(nama: string, alamat: string, telepon: string | null, email: string | null, npwp: string | null)`: Menambahkan data master perusahaan atau pelanggan baru.
EOT;
    }

    // Fungsi-fungsi helper di bawah ini tetap sama seperti kode Anda sebelumnya
    private function getSchemaAsString(): string
    {
        $tables = [
            'perusahaans' => ['id', 'nama', 'alamat', 'telepon', 'email', 'npwp'],
            'barangs' => ['id', 'kode', 'nama', 'kategori', 'harga', 'stok'],
            'pesanan_penjualans' => ['id', 'nomor_surat', 'perusahaan_id', 'tanggal_pesanan', 'total'],
            'surat_jalans' => ['id', 'nomor', 'perusahaan_id', 'tanggal'],
            'surat_penawaran_hargas' => ['id', 'nomor', 'perusahaan_id', 'tanggal', 'pembayaran']
        ];
        $schemaString = "Struktur tabel relevan:\n";
        foreach ($tables as $tableName => $columns) {
            $schemaString .= "- Tabel `$tableName` memiliki kolom: `" . implode("`, `", $columns) . "`.\n";
        }
        return $schemaString;
    }

    private function generateNarrativeResponse(string $userQuestion, array $data, string $apiKey): string
    {
        $dataJson = json_encode($data, JSON_PRETTY_PRINT);
        $narrativePrompt = <<<EOT
        Anda adalah asisten yang ramah. Berdasarkan data berikut, jawab pertanyaan user.
        Gunakan bahasa Indonesia yang natural dan jangan tampilkan data dalam format JSON mentah.
        
        Pertanyaan User: "$userQuestion"
        Data dari Database:
        $dataJson

        Jawaban Naratif:
        EOT;

        $client = new Client(['verify' => false]);
        $response = $client->post('https://api.openai.com/v1/chat/completions', [
            'headers' => ['Authorization' => 'Bearer ' . $apiKey, 'Content-Type' => 'application/json'],
            'json' => ['model' => 'gpt-4o-mini', 'messages' => [['role' => 'user', 'content' => $narrativePrompt]]]
        ]);
        return json_decode($response->getBody()->getContents(), true)['choices'][0]['message']['content'] ?? 'Tidak bisa membuat jawaban naratif.';
    }
}