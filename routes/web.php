<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuratJalanController;
use App\Http\Controllers\SuratPenawaranHargaController;
use App\Http\Controllers\FakturPenjualanController; 
use App\Http\Controllers\PesananPenjualanController;
use App\Http\Controllers\TandaTerimaPembayaranController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Di sini Anda hanya perlu mendaftarkan route untuk aksi kustom seperti
| mencetak PDF. Semua halaman CRUD lainnya (tabel, form) sudah
| ditangani oleh Laravel Filament.
|
*/

// Route untuk mencetak dokumen dari Tombol Aksi di Filament
Route::redirect('/', '/admin', 301);
Route::get('/pesanan/{id}/cetak', [PesananPenjualanController::class, 'cetak'])->name('pesanan.cetak'); //
Route::get('/faktur-penjualan/{id}/cetak', [FakturPenjualanController::class, 'cetak'])->name('faktur.cetak'); //
Route::get('/surat-jalan/cetak/{id}', [SuratJalanController::class, 'cetak'])->name('suratjalan.cetak'); //
Route::get('/penawaran-harga/{id}/pdf', [SuratPenawaranHargaController::class, 'pdf'])->name('penawaran.pdf'); //
Route::get('/tanda-terima-pembayaran/{id}/cetak', [TandaTerimaPembayaranController::class, 'cetak'])
     ->name('tanda-terima-pembayaran.cetak');
