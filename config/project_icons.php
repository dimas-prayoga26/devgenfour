<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Icon library
    |--------------------------------------------------------------------------
    |
    | Lucide icons (ISC licence) and their search tags, generated from the
    | lucide-static npm package with `php artisan app:build-icon-library`.
    |
    */

    'library' => resource_path('icons/lucide.json'),

    'fallback' => 'layout-grid',

    /*
    |--------------------------------------------------------------------------
    | Curated keywords
    |--------------------------------------------------------------------------
    |
    | Whole words or phrases (Indonesian and English) mapped to a Lucide icon.
    | They are checked in this order before the Lucide tags, so put the more
    | specific field first. After editing, run `php artisan app:refresh-project-icons`.
    |
    */

    'keywords' => [
        // Food and drink
        'restoran' => 'utensils',
        'restaurant' => 'utensils',
        'rumah makan' => 'utensils',
        'kuliner' => 'utensils',
        'katering' => 'utensils',
        'catering' => 'utensils',
        'makanan' => 'utensils',
        'food' => 'utensils',
        'kafe' => 'coffee',
        'cafe' => 'coffee',
        'kopi' => 'coffee',
        'coffee' => 'coffee',
        'teh' => 'coffee',
        'tea' => 'coffee',
        'kedai' => 'utensils',
        'warung' => 'utensils',
        'roti' => 'croissant',
        'kue' => 'croissant',
        'bakery' => 'croissant',

        // Health
        'rumah sakit' => 'hospital',
        'hospital' => 'hospital',
        'klinik' => 'stethoscope',
        'clinic' => 'stethoscope',
        'dokter' => 'stethoscope',
        'doctor' => 'stethoscope',
        'apotek' => 'pill',
        'farmasi' => 'pill',
        'pharmacy' => 'pill',
        'obat' => 'pill',
        'kesehatan' => 'heart-pulse',
        'health' => 'heart-pulse',
        'medis' => 'heart-pulse',
        'medical' => 'heart-pulse',

        // Education
        'sekolah' => 'school',
        'school' => 'school',
        'kampus' => 'graduation-cap',
        'universitas' => 'graduation-cap',
        'university' => 'graduation-cap',
        'campus' => 'graduation-cap',
        'pendidikan' => 'graduation-cap',
        'education' => 'graduation-cap',
        'kursus' => 'book-open',
        'course' => 'book-open',
        'pelatihan' => 'book-open',
        'training' => 'book-open',
        'belajar' => 'book-open',
        'learning' => 'book-open',
        'perpustakaan' => 'book-open',
        'buku' => 'book-open',
        'bookstore' => 'book-open',
        'library' => 'book-open',

        // Shopping
        'toko online' => 'shopping-cart',
        'online shop' => 'shopping-cart',
        'online store' => 'shopping-cart',
        'e-commerce' => 'shopping-cart',
        'commerce' => 'shopping-cart',
        'belanja' => 'shopping-cart',
        'kasir' => 'calculator',
        'point of sale' => 'calculator',
        'baju' => 'shirt',
        'fashion' => 'shirt',
        'pakaian' => 'shirt',
        'butik' => 'shirt',
        'boutique' => 'shirt',
        'konveksi' => 'shirt',

        // Finance and reporting
        'keuangan' => 'wallet',
        'finance' => 'wallet',
        'pembayaran' => 'wallet',
        'payment' => 'wallet',
        'koperasi' => 'hand-coins',
        'pinjaman' => 'hand-coins',
        'investasi' => 'hand-coins',
        'investment' => 'hand-coins',
        'bank' => 'landmark',
        'perbankan' => 'landmark',
        'akuntansi' => 'calculator',
        'accounting' => 'calculator',
        'pajak' => 'calculator',
        'laporan' => 'chart-column',
        'report' => 'chart-column',
        'dashboard' => 'chart-column',
        'analitik' => 'chart-column',
        'analytics' => 'chart-column',
        'statistik' => 'chart-column',
        'monitoring' => 'chart-column',

        // Property and industry
        'hotel' => 'hotel',
        'penginapan' => 'hotel',
        'homestay' => 'hotel',
        'villa' => 'hotel',
        'properti' => 'house',
        'property' => 'house',
        'perumahan' => 'house',
        'real estate' => 'house',
        'kos' => 'house',
        'kost' => 'house',
        'rumah' => 'house',
        'konstruksi' => 'hard-hat',
        'construction' => 'hard-hat',
        'kontraktor' => 'hard-hat',
        'pabrik' => 'factory',
        'manufaktur' => 'factory',
        'manufacturing' => 'factory',
        'industri' => 'factory',
        'industry' => 'factory',

        // Transport and logistics
        'logistik' => 'truck',
        'logistics' => 'truck',
        'pengiriman' => 'truck',
        'delivery' => 'truck',
        'ekspedisi' => 'truck',
        'kurir' => 'truck',
        'pindahan' => 'truck',
        'courier' => 'truck',
        'gudang' => 'warehouse',
        'warehouse' => 'warehouse',
        'inventaris' => 'package',
        'inventory' => 'package',
        'stok' => 'package',
        'tiket' => 'plane',
        'travel' => 'plane',
        'wisata' => 'map',
        'pariwisata' => 'map',
        'tourism' => 'map',
        'otomotif' => 'car',
        'automotive' => 'car',
        'mobil' => 'car',
        'transportasi' => 'bus',
        'transportation' => 'bus',
        'bengkel' => 'wrench',
        'servis' => 'wrench',
        'repair' => 'wrench',

        // Services
        'laundry' => 'washing-machine',
        'binatu' => 'washing-machine',
        'salon' => 'scissors',
        'barbershop' => 'scissors',
        'kecantikan' => 'sparkles',
        'beauty' => 'sparkles',
        'kosmetik' => 'sparkles',
        'skincare' => 'sparkles',
        'spa' => 'sparkles',
        'gym' => 'dumbbell',
        'fitness' => 'dumbbell',
        'kebugaran' => 'dumbbell',
        'olahraga' => 'trophy',
        'sport' => 'trophy',
        'sports' => 'trophy',
        'fotografi' => 'camera',
        'photography' => 'camera',
        'foto' => 'camera',
        'percetakan' => 'printer',
        'printing' => 'printer',
        'sablon' => 'printer',
        'reservasi' => 'calendar-days',
        'booking' => 'calendar-days',
        'janji temu' => 'calendar-days',
        'appointment' => 'calendar-days',
        'jadwal' => 'calendar-days',
        'event' => 'calendar-days',
        'acara' => 'calendar-days',
        'pernikahan' => 'heart',
        'wedding' => 'heart',
        'undangan' => 'heart',
        'hewan' => 'dog',
        'pet' => 'dog',

        // Nature and public sector
        'pertanian' => 'sprout',
        'perkebunan' => 'sprout',
        'agriculture' => 'sprout',
        'farm' => 'sprout',
        'perikanan' => 'fish',
        'fishery' => 'fish',
        'daur ulang' => 'recycle',
        'recycling' => 'recycle',
        'sampah' => 'recycle',
        'lingkungan' => 'leaf',
        'environment' => 'leaf',
        'energi' => 'zap',
        'energy' => 'zap',
        'listrik' => 'zap',
        'hukum' => 'scale',
        'legal' => 'scale',
        'notaris' => 'scale',
        'pemerintah' => 'landmark',
        'government' => 'landmark',
        'kementerian' => 'landmark',
        'desa' => 'landmark',
        'keamanan' => 'shield',
        'security' => 'shield',

        // People, media and marketing
        'absensi' => 'clipboard-check',
        'presensi' => 'clipboard-check',
        'attendance' => 'clipboard-check',
        'karyawan' => 'users',
        'sdm' => 'users',
        'komunitas' => 'users',
        'community' => 'users',
        'organisasi' => 'users',
        'keanggotaan' => 'users',
        'membership' => 'users',
        'pemasaran' => 'megaphone',
        'marketing' => 'megaphone',
        'promosi' => 'megaphone',
        'iklan' => 'megaphone',
        'pelanggan' => 'handshake',
        'crm' => 'handshake',
        'game' => 'gamepad-2',
        'film' => 'film',
        'video' => 'film',
        'musik' => 'headphones',
        'music' => 'headphones',
        'podcast' => 'mic',
        'radio' => 'mic',
        'berita' => 'newspaper',
        'news' => 'newspaper',
        'majalah' => 'newspaper',
        'blog' => 'newspaper',

        // Digital products
        'desain' => 'palette',
        'design' => 'palette',
        'ui' => 'palette',
        'ux' => 'palette',
        'branding' => 'palette',
        'kreatif' => 'palette',
        'creative' => 'palette',
        'company profile' => 'globe',
        'website perusahaan' => 'globe',
        'company website' => 'globe',
        'profil perusahaan' => 'globe',
        'landing page' => 'globe',
    ],

    /*
    |--------------------------------------------------------------------------
    | Generic keywords
    |--------------------------------------------------------------------------
    |
    | Broad words such as "toko" or "aplikasi" that say little about the field.
    | They are only used when neither the curated keywords nor the Lucide
    | tags found a match, so "Toko Bunga" still gets a flower icon.
    |
    */

    'generic_keywords' => [
        'toko' => 'store',
        'store' => 'store',
        'shop' => 'store',
        'retail' => 'store',
        'marketplace' => 'store',
        'penjualan' => 'store',
        'mobile' => 'smartphone',
        'android' => 'smartphone',
        'ios' => 'smartphone',
        'hosting' => 'server',
        'server' => 'server',
        'cloud' => 'cloud',
        'website' => 'globe',
        'web' => 'globe',
        'portal' => 'globe',
        'bisnis' => 'briefcase',
        'business' => 'briefcase',
        'perusahaan' => 'briefcase',
        'aplikasi' => 'code',
        'software' => 'code',
        'sistem' => 'code',
        'system' => 'code',
        'api' => 'code',
        'integrasi' => 'code',
        'integration' => 'code',
    ],

];
