<?php

namespace Esoftdream\Code;

use CodeIgniter\I18n\Time;
use CodeIgniter\Database\BaseConnection;
use Config\Database;

class Generator
{
    /**
     * Instance koneksi database
     *
     * @var BaseConnection
     */
    protected BaseConnection $db;

    /**
     * Inisialisasi koneksi database
     */
    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Menutup koneksi database saat objek dihancurkan
     */
    public function __destruct()
    {
        $this->db->close();
    }

    /**
     * Mengambil jumlah entri pada tabel tertentu berdasarkan tanggal hari ini.
     *
     * @param string $table_name   Nama tabel
     * @param string $table_column Nama kolom bertipe tanggal/waktu
     * @return int                 Jumlah entri hari ini
     */
    private function getCountToday(string $table_name, string $table_column): int
    {
        $today = Time::now()->toLocalizedString('yyyy-MM-dd');

        return $this->db
            ->table($table_name)
            ->where('DATE(' . $table_column . ')', $today)
            ->countAllResults();
    }

    /**
     * Generate kode unik berdasarkan tanggal dan jumlah entri hari ini.
     *
     * Format: PREFIX-YYMMDD-XXXX
     *
     * Jika parameter $kode disediakan, akan ditingkatkan sebagai suffix numerik:
     *   Contoh: WOTF-240607-0003 → WOTF-240607-0004
     *
     * @param string $table_name    Nama tabel untuk pengecekan jumlah
     * @param string $table_column  Nama kolom waktu/tanggal di tabel
     * @param string|null $prefix   Prefix kode (jika null akan dibuat acak)
     * @param string|null $kode     Kode yang sudah ada (untuk increment suffix)
     * @return string               Kode yang dihasilkan
     */
    public function generate(string $table_name, string $table_column, ?string $prefix = null, ?string $kode = null): string
    {
        helper('text');

        if (! $prefix) {
            $prefix = random_string('alpha', 4);
        }

        // Tambahkan penanda development di prefix jika bukan production
        if (ENVIRONMENT !== 'production') {
            $prefix = 'D' . random_string('alpha', 2) . $prefix;
        }

        // Jika kode sudah ada, buat suffix increment
        if ($kode) {
            // Ambil kode dasar dan suffix (potong bagian belakang setelah kode dasar)
            $kode_parts = explode('-', $kode);
            $kode_dasar = implode('-', array_slice($kode_parts, 0, 3)); // Ambil bagian dasar (misalnya WOTF-240907-0003)
            $suffix     = $kode_parts[3] ?? ''; // Ambil suffix jika ada

            // Jika suffix ada dan merupakan angka, tingkatkan suffix
            if (is_numeric($suffix)) {
                $new_suffix = str_pad((int) $suffix + 1, 1, '0', STR_PAD_LEFT);

                return $kode_dasar . '-' . $new_suffix;
            }

            // Jika tidak ada suffix, tambahkan suffix pertama (-1)
            return $kode . '-1';
        }

        $date     = Time::now()->toLocalizedString('yyMMdd');
        $sequence = $this->getCountToday($table_name, $table_column) + 1;

        return sprintf(
            '%s-%s-%04d',
            strtoupper($prefix),
            $date,
            $sequence
        );
    }
}
