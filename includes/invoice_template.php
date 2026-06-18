<?php
/**
 * Invoice Template Helper
 * Generates formatted invoice text for WhatsApp and clipboard copy
 */

function generateInvoicePendaftaran($nama, $nia, $cabang, $tanggal, $no_hp) {
    $no_invoice = 'INV/' . date('Y') . '/' . date('m') . '/' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
    
    $text = "═══════════════════════════\n";
    $text .= "  INVOICE SWIFT SWIMMING CLUB\n";
    $text .= "═══════════════════════════\n\n";
    $text .= "No. Invoice : $no_invoice\n";
    $text .= "Tanggal     : " . date('d F Y', strtotime($tanggal)) . "\n";
    $text .= "Kepada      : $nama\n";
    $text .= "NIA         : $nia\n";
    $text .= "Cabang      : $cabang\n\n";
    $text .= "─── Rincian Biaya ───\n";
    $text .= "Pendaftaran    : Rp 100.000\n";
    $text .= "SPP (8 Sesi)   : Rp 225.000\n";
    $text .= "─────────────────────\n";
    $text .= "TOTAL          : Rp 325.000\n\n";
    $text .= "Pembayaran dapat dilakukan via:\n";
    $text .= "🏦 Transfer Bank (hubungi admin)\n";
    $text .= "📱 Tunai saat latihan pertama\n\n";
    $text .= "Terima kasih telah bergabung!\n";
    $text .= "Swift Swimming Club\n";
    $text .= "═══════════════════════════";
    
    return $text;
}

function generateInvoiceSPP($nama, $nia, $cabang, $bulan, $tahun, $jumlah_absensi, $no_hp) {
    $nama_bulan = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];
    $bln = $nama_bulan[$bulan] ?? $bulan;
    $no_invoice = 'SPP/' . $tahun . '/' . $bulan . '/' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

    $text = "═══════════════════════════\n";
    $text .= "  TAGIHAN SPP SWIFT SC\n";
    $text .= "═══════════════════════════\n\n";
    $text .= "No. Tagihan : $no_invoice\n";
    $text .= "Periode     : $bln $tahun\n";
    $text .= "Tanggal     : " . date('d F Y') . "\n";
    $text .= "Kepada      : $nama\n";
    $text .= "NIA         : $nia\n";
    $text .= "Cabang      : $cabang\n\n";
    $text .= "─── Rincian ───\n";
    $text .= "Absensi bulan ini : {$jumlah_absensi}x hadir\n";
    $text .= "SPP (8 Sesi)      : Rp 225.000\n";
    $text .= "─────────────────────\n";
    $text .= "TOTAL TAGIHAN     : Rp 225.000\n\n";
    $text .= "⚠️ Mohon segera melakukan pembayaran agar sesi latihan bulan depan tidak terganggu.\n\n";
    $text .= "Pembayaran dapat dilakukan via:\n";
    $text .= "🏦 Transfer Bank (hubungi admin)\n";
    $text .= "📱 Tunai saat latihan\n\n";
    $text .= "Terima kasih!\n";
    $text .= "Swift Swimming Club\n";
    $text .= "═══════════════════════════";
    
    return $text;
}

function formatPhoneWA($no_hp) {
    // Convert 08xx to 628xx for wa.me
    $phone = preg_replace('/[^0-9]/', '', $no_hp);
    if (substr($phone, 0, 1) === '0') {
        $phone = '62' . substr($phone, 1);
    }
    return $phone;
}
?>
