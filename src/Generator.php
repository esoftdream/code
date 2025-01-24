<?php

namespace Esoftdream\Code;

use CodeIgniter\I18n\Time;

class Generator
{
    /**
     * Hitung count berdasarkan tabel yang digunakan
     */
    private function getCountToday(string $table_name, string $table_column): int
    {
        $today = Time::now()->toLocalizedString('yyyy-MM-dd');

        $builder = db_connect()->table($table_name);
        $builder->where('DATE(' . $table_column . ')', $today);

        return $builder->countAllResults();
    }

    /**
     * Undocumented function
     *
     * @param string|null $prefix       Custom prefix
     * @param string|null $table_name   Nama tabel untuk count
     * @param string|null $table_column Nama kolom untuk count
     * @param string|null $kode         Kode saat ini untuk menambahkan suffix
     */
    public function generate(string $table_name, string $table_column, ?string $prefix = null, ?string $kode = null): string
    {
        if (! $prefix) {
            $prefix = random_string('alpha', 4);
        }

        if (ENVIRONMENT !== 'production') {
            $prefix = 'D' . random_string('alpha', 2) . $prefix;
        }

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
