<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('site_settings')->insert([
            [
                'logo' => 'test',
                'phone' => 'test',
                'email' => 'test',
                'address' => 'test',
                'facebook' => 'test',
                'twitter' => 'test',
                'copyright' => 'test',
            ],
        ]);

        DB::table('users')->insert([
            // Admin 
            [
                'name' => 'Admin',
                'username' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('123'),
                'role' => 'admin',
                'status' => '1',
            ],
            // Instructor 
            [
                'name' => 'Instructor',
                'username' => 'instructor',
                'email' => 'instructor@gmail.com',
                'password' => Hash::make('123'),
                'role' => 'instructor',
                'status' => '1',
            ],
            // User Data 
            [
                'name' => 'User',
                'username' => 'user',
                'email' => 'user@gmail.com',
                'password' => Hash::make('123'),
                'role' => 'user',
                'status' => '1',
            ],
        ]);


        // category
        DB::table('categories')->insert([
            [
                'category_name' => 'Pajak PPH 25',
                'category_slug' => 'pajak-pph-25'
            ],
            [
                'category_name' => 'Pajak PPH 21',
                'category_slug' => 'pajak-pph-21'
            ],
            [
                'category_name' => 'Pajak Dasar',
                'category_slug' => 'pajak-dasar'
            ],
            [
                'category_name' => 'Pajak UMKM',
                'category_slug' => 'pajak-umkm'
            ],
            [
                'category_name' => 'Pajak Internasional',
                'category_slug' => 'pajak-internasional'
            ],
            [
                'category_name' => 'Pajak Pertambahan Nilai',
                'category_slug' => 'pajak-pertambahan-nilai'
            ],
            [
                'category_name' => 'Pajak Bumi dan Bangunan',
                'category_slug' => 'pajak-bumi-dan-bangunan'
            ],
            [
                'category_name' => 'Pajak Kendaraan',
                'category_slug' => 'pajak-kendaraan'
            ],
            [
                'category_name' => 'Pajak Perusahaan',
                'category_slug' => 'pajak-perusahaan'
            ],
            [
                'category_name' => 'Pajak Final',
                'category_slug' => 'pajak-final'
            ],
        ]);

        // sub category
        DB::table('sub_categories')->insert([
            ['category_id' => 1, 'subcategory_name' => 'Pelatihan Pajak A', 'subcategory_slug' => 'pelatihan-pajak-a'],
            ['category_id' => 1, 'subcategory_name' => 'Pelatihan Pajak B', 'subcategory_slug' => 'pelatihan-pajak-b'],
            ['category_id' => 1, 'subcategory_name' => 'Workshop PPH 21', 'subcategory_slug' => 'workshop-pph-21'],
            ['category_id' => 1, 'subcategory_name' => 'Simulasi Pajak UMKM', 'subcategory_slug' => 'simulasi-pajak-umkm'],
            ['category_id' => 1, 'subcategory_name' => 'Kelas Pajak Online', 'subcategory_slug' => 'kelas-pajak-online'],

            ['category_id' => 2, 'subcategory_name' => 'Dasar PPH', 'subcategory_slug' => 'dasar-pph'],
            ['category_id' => 2, 'subcategory_name' => 'Pemahaman Pajak Final', 'subcategory_slug' => 'pemahaman-pajak-final'],
            ['category_id' => 2, 'subcategory_name' => 'Studi Kasus PPN', 'subcategory_slug' => 'studi-kasus-ppn'],
            ['category_id' => 2, 'subcategory_name' => 'Praktik Pajak Aplikasi', 'subcategory_slug' => 'praktik-pajak-aplikasi'],
            ['category_id' => 2, 'subcategory_name' => 'Kursus Pajak UMKM', 'subcategory_slug' => 'kursus-pajak-umkm'],

            ['category_id' => 3, 'subcategory_name' => 'Audit Pajak 1', 'subcategory_slug' => 'audit-pajak-1'],
            ['category_id' => 3, 'subcategory_name' => 'Audit Pajak 2', 'subcategory_slug' => 'audit-pajak-2'],
            ['category_id' => 3, 'subcategory_name' => 'Audit Pajak 3', 'subcategory_slug' => 'audit-pajak-3'],
            ['category_id' => 3, 'subcategory_name' => 'Audit Pajak 4', 'subcategory_slug' => 'audit-pajak-4'],
            ['category_id' => 3, 'subcategory_name' => 'Audit Pajak 5', 'subcategory_slug' => 'audit-pajak-5'],

            ['category_id' => 4, 'subcategory_name' => 'Sertifikasi Brevet A', 'subcategory_slug' => 'sertifikasi-brevet-a'],
            ['category_id' => 4, 'subcategory_name' => 'Sertifikasi Brevet B', 'subcategory_slug' => 'sertifikasi-brevet-b'],
            ['category_id' => 4, 'subcategory_name' => 'Sertifikasi Brevet C', 'subcategory_slug' => 'sertifikasi-brevet-c'],
            ['category_id' => 4, 'subcategory_name' => 'Brevet Pajak Online', 'subcategory_slug' => 'brevet-pajak-online'],
            ['category_id' => 4, 'subcategory_name' => 'Brevet Khusus UMKM', 'subcategory_slug' => 'brevet-khusus-umkm'],

            ['category_id' => 5, 'subcategory_name' => 'Tutorial SPT Tahunan', 'subcategory_slug' => 'tutorial-spt-tahunan'],
            ['category_id' => 5, 'subcategory_name' => 'Pengisian e-Faktur', 'subcategory_slug' => 'pengisian-e-faktur'],
            ['category_id' => 5, 'subcategory_name' => 'e-Bupot Pemula', 'subcategory_slug' => 'e-bupot-pemula'],
            ['category_id' => 5, 'subcategory_name' => 'PPN untuk Startup', 'subcategory_slug' => 'ppn-untuk-startup'],
            ['category_id' => 5, 'subcategory_name' => 'Pajak Freelancer', 'subcategory_slug' => 'pajak-freelancer'],

            ['category_id' => 6, 'subcategory_name' => 'Pajak UMKM A', 'subcategory_slug' => 'pajak-umkm-a'],
            ['category_id' => 6, 'subcategory_name' => 'Pajak UMKM B', 'subcategory_slug' => 'pajak-umkm-b'],
            ['category_id' => 6, 'subcategory_name' => 'Pajak UMKM C', 'subcategory_slug' => 'pajak-umkm-c'],
            ['category_id' => 6, 'subcategory_name' => 'Pajak UMKM D', 'subcategory_slug' => 'pajak-umkm-d'],
            ['category_id' => 6, 'subcategory_name' => 'Pajak UMKM E', 'subcategory_slug' => 'pajak-umkm-e'],

            ['category_id' => 7, 'subcategory_name' => 'Regulasi Pajak Internasional', 'subcategory_slug' => 'regulasi-pajak-internasional'],
            ['category_id' => 7, 'subcategory_name' => 'Double Tax Avoidance', 'subcategory_slug' => 'double-tax-avoidance'],
            ['category_id' => 7, 'subcategory_name' => 'Pajak untuk Ekspor', 'subcategory_slug' => 'pajak-untuk-ekspor'],
            ['category_id' => 7, 'subcategory_name' => 'Transfer Pricing', 'subcategory_slug' => 'transfer-pricing'],
            ['category_id' => 7, 'subcategory_name' => 'Kebijakan Pajak Global', 'subcategory_slug' => 'kebijakan-pajak-global'],

            ['category_id' => 8, 'subcategory_name' => 'PPN & PPh untuk Perusahaan', 'subcategory_slug' => 'ppn-pph-untuk-perusahaan'],
            ['category_id' => 8, 'subcategory_name' => 'Strategi Pajak Perusahaan', 'subcategory_slug' => 'strategi-pajak-perusahaan'],
            ['category_id' => 8, 'subcategory_name' => 'Tax Planning', 'subcategory_slug' => 'tax-planning'],
            ['category_id' => 8, 'subcategory_name' => 'Laporan Keuangan Pajak', 'subcategory_slug' => 'laporan-keuangan-pajak'],
            ['category_id' => 8, 'subcategory_name' => 'Audit Internal Perpajakan', 'subcategory_slug' => 'audit-internal-perpajakan'],

            ['category_id' => 9, 'subcategory_name' => 'Simulasi Pajak Kendaraan', 'subcategory_slug' => 'simulasi-pajak-kendaraan'],
            ['category_id' => 9, 'subcategory_name' => 'Jenis Pajak Kendaraan', 'subcategory_slug' => 'jenis-pajak-kendaraan'],
            ['category_id' => 9, 'subcategory_name' => 'e-Samsat Tutorial', 'subcategory_slug' => 'e-samsat-tutorial'],
            ['category_id' => 9, 'subcategory_name' => 'Pajak Mobil Bekas', 'subcategory_slug' => 'pajak-mobil-bekas'],
            ['category_id' => 9, 'subcategory_name' => 'Pajak Motor Tahunan', 'subcategory_slug' => 'pajak-motor-tahunan'],

            ['category_id' => 10, 'subcategory_name' => 'Final Tax Freelance', 'subcategory_slug' => 'final-tax-freelance'],
            ['category_id' => 10, 'subcategory_name' => 'Final Tax UMKM', 'subcategory_slug' => 'final-tax-umkm'],
            ['category_id' => 10, 'subcategory_name' => 'Final Tax Tutorial', 'subcategory_slug' => 'final-tax-tutorial'],
            ['category_id' => 10, 'subcategory_name' => 'Final Tax untuk Startup', 'subcategory_slug' => 'final-tax-untuk-startup'],
            ['category_id' => 10, 'subcategory_name' => 'Final Tax e-Filing', 'subcategory_slug' => 'final-tax-e-filing'],
        ]);


        DB::table('smtp_settings')->insert([
            [
                'mailer' => 'mailer',
                'host' => 'host',
                'port' => 'port',
                'username' => 'username',
                'password' => 'password',
                'encryption' => 'encryption',
                'from_address' => 'from_address',
            ],
        ]);
    }
}
