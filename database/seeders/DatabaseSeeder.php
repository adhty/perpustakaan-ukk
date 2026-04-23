<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\{User, Kategori, Buku};

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ─────────────────────────────────────
        User::create([
            'name'      => 'Administrator',
            'username'  => 'admin',
            'email'     => 'admin@perpustakaan.sch.id',
            'password'  => Hash::make('password'),
            'role'      => 'admin',
            'is_active' => true,
        ]);

        // ── Siswa Demo ────────────────────────────────
        $siswa = [
            ['Budi Santoso',   'budi.santoso',   '2024001', 'XII RPL 1', '081234567890'],
            ['Siti Rahayu',    'siti.rahayu',    '2024002', 'XII RPL 2', '081234567891'],
            ['Ahmad Fauzi',    'ahmad.fauzi',    '2024003', 'XI RPL 1',  '081234567892'],
            ['Dewi Lestari',   'dewi.lestari',   '2024004', 'XI RPL 2',  '081234567893'],
            ['Rizky Pratama',  'rizky.pratama',  '2024005', 'X RPL 1',   '081234567894'],
        ];

        foreach ($siswa as [$name, $username, $nis, $kelas, $hp]) {
            User::create([
                'name'      => $name,
                'username'  => $username,
                'email'     => $username . '@siswa.sch.id',
                'password'  => Hash::make('password'),
                'role'      => 'siswa', 
                'nis'       => $nis,
                'kelas'     => $kelas,
                'no_hp'     => $hp,
                'is_active' => true,
            ]);
        }

        // ── Kategori ──────────────────────────────────
        $kategoris = [
            ['Pemrograman',    'Buku-buku tentang pemrograman dan pengembangan perangkat lunak'],
            ['Matematika',     'Buku pelajaran matematika SMA/SMK'],
            ['Bahasa Indonesia','Buku bahasa dan sastra Indonesia'],
            ['IPA',            'Ilmu Pengetahuan Alam (Fisika, Kimia, Biologi)'],
            ['IPS',            'Ilmu Pengetahuan Sosial'],
            ['Fiksi',          'Novel, cerpen, dan karya fiksi lainnya'],
            ['Referensi',      'Kamus, ensiklopedia, dan buku referensi'],
            ['Kejuruan',       'Buku-buku kejuruan teknik dan vokasi'],
        ];

        foreach ($kategoris as [$nama, $ket]) {
            Kategori::create(['nama_kategori' => $nama, 'keterangan' => $ket]);
        }

        // ── Buku ──────────────────────────────────────
        $bukus = [
            ['BK001', 'Pemrograman Web dengan PHP & MySQL', 'Agus Saputra', 'Informatika', 2023, 1, 3, '978-602-0000-01-1', 'A1'],
            ['BK002', 'Laravel: Framework PHP Modern', 'Ahmad Fauzi', 'Andi Publisher', 2023, 1, 2, '978-602-0000-02-2', 'A2'],
            ['BK003', 'Belajar Python untuk Pemula', 'Eko Kurniawan', 'Elex Media', 2022, 1, 3, '978-602-0000-03-3', 'A3'],
            ['BK004', 'Algoritma dan Struktur Data', 'Rinaldi Munir', 'Informatika', 2022, 1, 2, '978-602-0000-04-4', 'A4'],
            ['BK005', 'Matematika Kelas XII', 'Sukino', 'Erlangga', 2023, 2, 5, '978-602-0000-05-5', 'B1'],
            ['BK006', 'Matematika SMK Kelas XI', 'Sartono Wirodikromo', 'Erlangga', 2022, 2, 4, '978-602-0000-06-6', 'B2'],
            ['BK007', 'Bahasa Indonesia untuk SMA/SMK', 'Kemdikbud', 'Kemendikbud', 2023, 3, 5, '978-602-0000-07-7', 'C1'],
            ['BK008', 'Fisika Terapan SMK', 'Haris Suprapto', 'Yudhistira', 2022, 4, 3, '978-602-0000-08-8', 'D1'],
            ['BK009', 'Kimia Dasar', 'Petrucci', 'Erlangga', 2022, 4, 2, '978-602-0000-09-9', 'D2'],
            ['BK010', 'Sejarah Indonesia Modern', 'M.C. Ricklefs', 'Serambi', 2022, 5, 3, '978-602-0000-10-0', 'E1'],
            ['BK011', 'Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', 2005, 6, 2, '978-602-0000-11-1', 'F1'],
            ['BK012', 'Bumi Manusia', 'Pramoedya Ananta Toer', 'Lentera Dipantara', 2005, 6, 2, '978-602-0000-12-2', 'F2'],
            ['BK013', 'Kamus Besar Bahasa Indonesia', 'Tim Redaksi KBBI', 'Balai Pustaka', 2022, 7, 1, '978-602-0000-13-3', 'G1'],
            ['BK014', 'Teknik Jaringan Komputer', 'Dede Sopandi', 'Informatika', 2023, 8, 3, '978-602-0000-14-4', 'H1'],
            ['BK015', 'Desain Grafis dengan Adobe', 'Jubilee Enterprise', 'Elex Media', 2023, 8, 2, '978-602-0000-15-5', 'H2'],
        ];

        foreach ($bukus as [$kode, $judul, $pengarang, $penerbit, $tahun, $katId, $stok, $isbn, $rak]) {
            Buku::create([
                'kode_buku'      => $kode,
                'judul'          => $judul,
                'pengarang'      => $pengarang,
                'penerbit'       => $penerbit,
                'tahun_terbit'   => $tahun,
                'kategori_id'    => $katId,
                'stok'           => $stok,
                'stok_tersedia'  => $stok,
                'isbn'           => $isbn,
                'rak'            => $rak,
            ]);
        }

        $this->command->info('✅ Seeder selesai! 1 admin, 5 siswa, 8 kategori, 15 buku berhasil dibuat.');
    }
}
