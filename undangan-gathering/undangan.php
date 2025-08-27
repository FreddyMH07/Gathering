<?php

// =================================================================
// SCRIPT GENERATOR GAMBAR UNDANGAN DINAMIS
// Dibuat dengan PHP dan GD Library
// =================================================================

// --- PENGATURAN DASAR ---

// Header untuk memberitahu browser bahwa ini adalah file gambar PNG
header('Content-Type: image/png');
// Mengaktifkan error reporting untuk debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// --- PATH ASET (PENTING: Sesuaikan path jika perlu) ---
// Pastikan Anda sudah membuat folder 'assets' di direktori yang sama dengan file PHP ini.
$fontPath_Title = __DIR__ . '/assets/PlayfairDisplay-Italic.ttf';
$fontPath_Regular = __DIR__ . '/assets/Lato-Regular.ttf';
$fontPath_Bold = __DIR__ . '/assets/Lato-Bold.ttf';
$logoPath = __DIR__ . '/assets/logos.png'; // Gambar logo-logo perusahaan
$leafPath = __DIR__ . '/assets/leaf.png'; // Gambar hiasan daun transparan

// Cek apakah file aset ada, jika tidak, tampilkan pesan error
if (!file_exists($fontPath_Title) || !file_exists($logoPath) || !file_exists($leafPath)) {
    die("Error: File aset tidak ditemukan! Pastikan font, logo, dan hiasan daun ada di dalam folder 'assets'.");
}

// --- PENGATURAN KANVAS GAMBAR ---
$width = 1080;
$height = 1920; // Ukuran standar untuk Instagram/Facebook Story
$image = imagecreatetruecolor($width, $height);

// --- PENGATURAN WARNA ---
$white = imagecolorallocate($image, 255, 255, 255);
$cream = imagecolorallocate($image, 250, 249, 246);
$green_dark = imagecolorallocate($image, 0, 56, 41);
$gold = imagecolorallocate($image, 192, 162, 107);
$text_dark = imagecolorallocate($image, 51, 51, 51);
$text_brown = imagecolorallocate($image, 107, 79, 44);
$text_light = imagecolorallocate($image, 136, 136, 136);

// --- MEMBUAT BACKGROUND ---
// 1. Latar belakang putih cream
imagefilledrectangle($image, 0, 0, $width, $height, $cream);

// 2. Membuat pola geometris hijau di sisi kiri dan kanan
for ($i = -200; $i < $height + 200; $i += 50) {
    $points_left = [
        0, $i,
        250, $i - 150,
        200, $i + 150,
    ];
    $points_right = [
        $width, $i,
        $width - 250, $i - 150,
        $width - 200, $i + 150,
    ];
    // Membuat warna hijau dengan transparansi
    $green_transparent = imagecolorallocatealpha($image, 14, 114, 82, rand(90, 110));
    imagefilledpolygon($image, $points_left, 3, $green_transparent);
    imagefilledpolygon($image, $points_right, 3, $green_transparent);
}

// 3. Menambahkan hiasan daun
$leaf = imagecreatefrompng($leafPath);
// Salin daun ke pojok kiri atas
imagecopyresized($image, $leaf, -100, -120, 0, 0, 500, 500, imagesx($leaf), imagesy($leaf));
// Salin daun ke pojok kanan bawah (dengan membalik gambar)
imageflip($leaf, IMG_FLIP_BOTH);
imagecopyresized($image, $leaf, $width - 400, $height - 380, 0, 0, 500, 500, imagesx($leaf), imagesy($leaf));


// --- MENULIS TEKS KE GAMBAR ---

// Fungsi untuk menulis teks di tengah (horizontal)
function drawCenteredText($image, $y, $color, $font, $text, $size) {
    $width = imagesx($image);
    $bbox = imagettfbbox($size, 0, $font, $text);
    $x = ($width - ($bbox[2] - $bbox[0])) / 2;
    imagettftext($image, $size, 0, $x, $y, $color, $font, $text);
}

// Menulis konten undangan
drawCenteredText($image, 250, $text_light, $fontPath_Regular, "YOU'RE INVITED TO A", 24);
drawCenteredText($image, 320, $text_dark, $fontPath_Bold, "FAMILY BUSINESS GATHERING", 38);

// Tema Acara (Multi-baris)
imagettftext($image, 75, 0, 150, 500, $text_brown, $fontPath_Title, "Satu Warisan,");
imagettftext($image, 75, 0, 180, 620, $text_brown, $fontPath_Title, "Banyak Tangan:");
imagettftext($image, 50, 0, 200, 750, $text_brown, $fontPath_Title, "Menyatukan Langkah,");
imagettftext($image, 50, 0, 230, 830, $text_brown, $fontPath_Title, "Merawat Nilai.");

// Garis Emas Pemisah
imagefilledrectangle($image, 100, 950, $width - 100, 952, $gold);
imagefilledellipse($image, 100, 951, 10, 10, $gold);
imagefilledellipse($image, $width - 100, 951, 10, 10, $gold);

// Lokasi
drawCenteredText($image, 1020, $text_dark, $fontPath_Bold, "ARCICI SPORT CENTER", 36);
drawCenteredText($image, 1070, $text_light, $fontPath_Regular, "Jl. Cempaka Putih Barat XXVI, Cempaka Putih, Jakarta Pusat", 22);

// Detail Waktu
imagettftext($image, 30, 0, 200, 1200, $text_light, $fontPath_Regular, "SENIN", 0);
imagettftext($image, 36, 0, 200, 1260, $text_dark, $fontPath_Bold, "1 SEPTEMBER 2025", 0);

imagettftext($image, 90, 0, 480, 1250, $text_dark, $fontPath_Bold, "07.30", 0);
imagettftext($image, 36, 0, 780, 1210, $text_light, $fontPath_Regular, "WIB", 0);
imagettftext($image, 28, 0, 780, 1260, $text_light, $fontPath_Regular, "Selesai", 0);


// --- MENAMBAHKAN LOGO ---
$logos = imagecreatefrompng($logoPath);
$logo_width = imagesx($logos);
$logo_height = imagesy($logos);
$new_logo_width = 800; // Lebar baru untuk logo di undangan
$new_logo_height = ($new_logo_width / $logo_width) * $logo_height;
$logo_x = ($width - $new_logo_width) / 2;
$logo_y = 1400;
imagecopyresampled($image, $logos, $logo_x, $logo_y, 0, 0, $new_logo_width, $new_logo_height, $logo_width, $logo_height);


// --- OUTPUT GAMBAR FINAL ---
imagepng($image);

// --- MEMBERSIHKAN MEMORI ---
imagedestroy($image);
imagedestroy($logos);
imagedestroy($leaf);

?>
