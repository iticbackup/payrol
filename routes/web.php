<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    // return view('welcome');
    return view('auth/login');
});

// Auth::routes();
Auth::routes([
    'register' => false, // Registration Routes...
    'reset' => false, // Password Reset Routes...
    'verify' => false, // Email Verification Routes...
]);

Route::group(['middleware' => 'auth'], function () {
    Route::get('home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::prefix('testing')->group(function () {
        Route::get('borongan', [App\Http\Controllers\TestingController::class, 'test_import_excel_borongan']);
        Route::post('borongan/simpan', [App\Http\Controllers\TestingController::class, 'test_import_excel_borongan_simpan'])->name('test_import_excel_borongan_simpan');
        // Route::get('borongan/{kodePengerjaan}/{jenisOperatorDetailId}/{jenisOperatorDetailPekerjaanId}/download', [App\Http\Controllers\TestingController::class, 'test_export_excel_borongan'])->name('test_export_excel_borongan_download');
    });

    // Route::prefix('periodes')->group(function() {
    //     Route::get('/', [App\Http\Controllers\PeriodeController::class, 'index'])->name('periode');
    // });

    Route::prefix('pengerjaan')->group(function () {
        Route::get('/', [App\Http\Controllers\PengerjaanController::class, 'index'])->name('pengerjaan');
        Route::post('simpan', [App\Http\Controllers\PengerjaanController::class, 'simpan'])->name('pengerjaan.simpan');
        Route::get('karyawan/{kode_pengerjaan}/{id}/{kode_payrol}', [App\Http\Controllers\PengerjaanController::class, 'karyawan_pengerjaan'])->name('pengerjaan.karyawan');
        Route::post('karyawan/{kode_pengerjaan}/{id}/{kode_payrol}/simpan', [App\Http\Controllers\PengerjaanController::class, 'karyawan_pengerjaan_simpan'])->name('pengerjaan.karyawan.simpan');
        
        Route::get('karyawan/{kode_pengerjaan}/{id}/popup_tambah_pegawai/{jenis_pekerja_id}', [App\Http\Controllers\PengerjaanController::class, 'tambah_karyawan_pengerjaan'])->name('pengerjaan.popup_tambah_pegawai');
        Route::post('karyawan/{kode_pengerjaan}/{id}/popup_tambah_pegawai/{jenis_pekerja_id}/simpan', [App\Http\Controllers\PengerjaanController::class, 'tambah_karyawan_pengerjaan_simpan'])->name('pengerjaan.popup_tambah_pegawai_simpan');
        
        Route::prefix('hasil_kerja')->group(function () {
            Route::get('/', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja'])->name('pengerjaan.hasil_kerja');
            Route::get('b_packing', [App\Http\Controllers\PengerjaanController::class, 'b_hasil_kerja_packing'])->name('pengerjaan.b_hasil_kerja_packing');
            Route::get('b_harian', [App\Http\Controllers\PengerjaanController::class, 'b_hasil_kerja_harian'])->name('pengerjaan.b_hasil_kerja_harian');
            Route::get('b_supir', [App\Http\Controllers\PengerjaanController::class, 'b_hasil_kerja_supir'])->name('pengerjaan.b_hasil_kerja_supir');
            // Route::get('create', [App\Http\Controllers\OperatorKaryawanController::class, 'create'])->name('operator_karyawan.create');

            Route::prefix('packing_lokal')->group(function () {
                Route::get('{id}/{kode_pengerjaan}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_packing'])->name('hasil_kerja.packingLokal');
                
                Route::get('{id}/{kode_pengerjaan}/input_bpjs_kesehatan', [App\Http\Controllers\PengerjaanController::class, 'input_bpjs_kesehatan'])->name('hasil_kerja.input_bpjs_kesehatan');
                Route::post('{id}/{kode_pengerjaan}/input_bpjs_kesehatan/simpan', [App\Http\Controllers\PengerjaanController::class, 'input_bpjs_kesehatan_simpan'])->name('hasil_kerja.input_bpjs_kesehatan.simpan');
                
                Route::get('{id}/{kode_pengerjaan}/{tanggal}/input_hasil', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_packing_view_hasil'])->name('hasil_kerja.packingLokal.view_hasil');
                Route::post('{id}/{kode_pengerjaan}/{tanggal}/input_hasil/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_packing_view_simpan'])->name('hasil_kerja.packingLokal.view_hasil.simpan');           
                Route::get('{id}/{kode_pengerjaan}/{jenisOperatorDetailId}/{jenisOperatorDetailPekerjaanId}/download', [App\Http\Controllers\PengerjaanController::class, 'export_excel_borongan'])->name('hasil_kerja.export_excel_borongan_packing');
                Route::post('{id}/{kode_pengerjaan}/{jenisOperatorDetailId}/{jenisOperatorDetailPekerjaanId}/upload', [App\Http\Controllers\PengerjaanController::class, 'import_excel_borongan'])->name('hasil_kerja.import_excel_borongan_packing');
                
                Route::get('{id}/{kode_pengerjaan}/{nik}/input_hasil_karyawan/{month}/{year}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_karyawan_packing_view'])->name('hasil_kerja.packingLokal.view_hasil_karyawan');
                Route::post('{id}/{kode_pengerjaan}/{nik}/input_hasil_karyawan/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_karyawan_packing_view_simpan'])->name('hasil_kerja.packingLokal.view_hasil_karyawan.simpan');
            });
            Route::prefix('bandrol_lokal')->group(function () {
                Route::get('{id}/{kode_pengerjaan}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_bandrol_lokal'])->name('hasil_kerja.bandrolLokal');
                Route::get('{id}/{kode_pengerjaan}/{tanggal}/input_hasil', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_bandrol_lokal_view_hasil'])->name('hasil_kerja.bandrolLokal.view_hasil');
                Route::post('{id}/{kode_pengerjaan}/{tanggal}/input_hasil/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_bandrol_view_simpan'])->name('hasil_kerja.bandrolLokal.view_hasil.simpan');
                
                Route::get('{id}/{kode_pengerjaan}/{nik}/input_hasil_karyawan/{month}/{year}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_karyawan_bandrol_view'])->name('hasil_kerja.bandrolLokal.view_hasil_karyawan');
                Route::post('{id}/{kode_pengerjaan}/{nik}/input_hasil_karyawan/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_karyawan_bandrol_view_simpan'])->name('hasil_kerja.bandrolLokal.view_hasil_karyawan.simpan');
            });
            Route::prefix('inner_lokal')->group(function () {
                // Route::get('/', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_inner_lokal'])->name('hasil_kerja.innerLokal');
                Route::get('{id}/{kode_pengerjaan}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_inner_lokal'])->name('hasil_kerja.innerLokal');
                Route::get('{id}/{kode_pengerjaan}/{tanggal}/input_hasil', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_inner_lokal_view_hasil'])->name('hasil_kerja.innerLokal.view_hasil');
                Route::post('{id}/{kode_pengerjaan}/{tanggal}/input_hasil/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_inner_view_simpan'])->name('hasil_kerja.innerLokal.view_hasil.simpan');
                
                Route::get('{id}/{kode_pengerjaan}/{nik}/input_hasil_karyawan/{month}/{year}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_karyawan_inner_view'])->name('hasil_kerja.innerLokal.view_hasil_karyawan');
                Route::post('{id}/{kode_pengerjaan}/{nik}/input_hasil_karyawan/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_karyawan_inner_view_simpan'])->name('hasil_kerja.innerLokal.view_hasil_karyawan.simpan');
            });
            Route::prefix('outer_lokal')->group(function () {
                // Route::get('/', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_outer_lokal'])->name('hasil_kerja.outerLokal');
                Route::get('{id}/{kode_pengerjaan}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_outer_lokal'])->name('hasil_kerja.outerLokal');
                Route::get('{id}/{kode_pengerjaan}/{tanggal}/input_hasil', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_outer_lokal_view_hasil'])->name('hasil_kerja.outerLokal.view_hasil');
                Route::post('{id}/{kode_pengerjaan}/{tanggal}/input_hasil/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_outer_view_simpan'])->name('hasil_kerja.outerLokal.view_hasil.simpan');
                
                Route::get('{id}/{kode_pengerjaan}/{nik}/input_hasil_karyawan/{month}/{year}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_karyawan_outer_view'])->name('hasil_kerja.outerLokal.view_hasil_karyawan');
                Route::post('{id}/{kode_pengerjaan}/{nik}/input_hasil_karyawan/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_karyawan_outer_view_simpan'])->name('hasil_kerja.outerLokal.view_hasil_karyawan.simpan');
            });

            Route::prefix('stempel_lokal')->group(function () {
                Route::get('{id}/{kode_pengerjaan}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_stempel_lokal'])->name('hasil_kerja.stempelLokal');
                Route::get('{id}/{kode_pengerjaan}/{tanggal}/input_hasil', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_stempel_lokal_view_hasil'])->name('hasil_kerja.stempelLokal.view_hasil');
                Route::post('{id}/{kode_pengerjaan}/{tanggal}/input_hasil/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_stempel_lokal_view_simpan'])->name('hasil_kerja.stempelLokal.view_hasil.simpan');
                
                Route::get('{id}/{kode_pengerjaan}/{nik}/input_hasil_karyawan/{month}/{year}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_karyawan_stempel_lokal_view'])->name('hasil_kerja.stempelLokal.view_hasil_karyawan');
                Route::post('{id}/{kode_pengerjaan}/{nik}/input_hasil_karyawan/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_karyawan_stempel_lokal_view_simpan'])->name('hasil_kerja.stempelLokal.view_hasil_karyawan.simpan');
            });

            Route::prefix('packing_ekspor')->group(function () {
                // Route::get('/', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_packing_ekspor'])->name('hasil_kerja.packingEkspor');
                Route::get('{id}/{kode_pengerjaan}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_packing_ekspor'])->name('hasil_kerja.packingEkspor');
                Route::get('{id}/{kode_pengerjaan}/{tanggal}/input_hasil', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_packing_ekspor_view_hasil'])->name('hasil_kerja.packingEkspor.view_hasil');
                Route::post('{id}/{kode_pengerjaan}/{tanggal}/input_hasil/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_packing_ekspor_view_simpan'])->name('hasil_kerja.packingEkspor.view_hasil.simpan');
                
                Route::get('{id}/{kode_pengerjaan}/{nik}/input_hasil_karyawan/{month}/{year}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_karyawan_packing_ekspor_view'])->name('hasil_kerja.packingEkspor.view_hasil_karyawan');
                Route::post('{id}/{kode_pengerjaan}/{nik}/input_hasil_karyawan/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_karyawan_packing_ekspor_view_simpan'])->name('hasil_kerja.packingEkspor.view_hasil_karyawan.simpan');
            });
            Route::prefix('kemas_ekspor')->group(function () {
                Route::get('{id}/{kode_pengerjaan}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_kemas_ekspor'])->name('hasil_kerja.kemasEkspor');
                Route::get('{id}/{kode_pengerjaan}/{tanggal}/input_hasil', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_kemas_ekspor_view_hasil'])->name('hasil_kerja.kemasEkspor.view_hasil');
                Route::post('{id}/{kode_pengerjaan}/{tanggal}/input_hasil/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_kemas_ekspor_view_simpan'])->name('hasil_kerja.kemasEkspor.view_hasil.simpan');
                
                Route::get('{id}/{kode_pengerjaan}/{nik}/input_hasil_karyawan/{month}/{year}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_karyawan_kemas_ekspor_view'])->name('hasil_kerja.kemasEkspor.view_hasil_karyawan');
                Route::post('{id}/{kode_pengerjaan}/{nik}/input_hasil_karyawan/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_karyawan_kemas_ekspor_view_simpan'])->name('hasil_kerja.kemasEkspor.view_hasil_karyawan.simpan');
            });
            Route::prefix('gagang_ekspor')->group(function () {
                // Route::get('/', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_gagang_ekspor'])->name('hasil_kerja.gagangEkspor');
                Route::get('{id}/{kode_pengerjaan}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_gagang_ekspor'])->name('hasil_kerja.gagangEkspor');
                Route::get('{id}/{kode_pengerjaan}/{tanggal}/input_hasil', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_gagang_ekspor_view_hasil'])->name('hasil_kerja.gagangEkspor.view_hasil');
                Route::post('{id}/{kode_pengerjaan}/{tanggal}/input_hasil/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_gagang_ekspor_view_simpan'])->name('hasil_kerja.gagangEkspor.view_hasil.simpan');
                
                Route::get('{id}/{kode_pengerjaan}/{nik}/input_hasil_karyawan/{month}/{year}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_karyawan_gagang_ekspor_view'])->name('hasil_kerja.gagangEkspor.view_hasil_karyawan');
                Route::post('{id}/{kode_pengerjaan}/{nik}/input_hasil_karyawan/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_karyawan_gagang_ekspor_view_simpan'])->name('hasil_kerja.gagangEkspor.view_hasil_karyawan.simpan');
            });
            Route::prefix('isi_etiket')->group(function () {
                // Route::get('/', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_isi_etiket'])->name('hasil_kerja.isiEtiket');
                Route::get('{id}/{kode_pengerjaan}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_ambri_isiEtiket'])->name('hasil_kerja.isiEtiket');
                Route::get('{id}/{kode_pengerjaan}/{tanggal}/input_hasil', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_ambri_isiEtiket_view_hasil'])->name('hasil_kerja.isiEtiket.view_hasil');
                Route::post('{id}/{kode_pengerjaan}/{tanggal}/input_hasil/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_ambri_isiEtiket_view_simpan'])->name('hasil_kerja.isiEtiket.view_hasil.simpan');
                
                Route::get('{id}/{kode_pengerjaan}/{nik}/input_hasil_karyawan/{month}/{year}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_karyawan_ambri_isiEtiket_view'])->name('hasil_kerja.isiEtiket.view_hasil_karyawan');
                Route::post('{id}/{kode_pengerjaan}/{nik}/input_hasil_karyawan/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_karyawan_ambri_isiEtiket_view_simpan'])->name('hasil_kerja.isiEtiket.view_hasil_karyawan.simpan');
            });
            Route::prefix('las_tepi')->group(function () {
                Route::get('{id}/{kode_pengerjaan}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_ambri_las_tepi'])->name('hasil_kerja.lasTepi');
                Route::get('{id}/{kode_pengerjaan}/{tanggal}/input_hasil', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_ambri_las_tepi_view_hasil'])->name('hasil_kerja.lasTepi.view_hasil');
                Route::post('{id}/{kode_pengerjaan}/{tanggal}/input_hasil/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_ambri_las_tepi_view_simpan'])->name('hasil_kerja.lasTepi.view_hasil.simpan');
                
                Route::get('{id}/{kode_pengerjaan}/{nik}/input_hasil_karyawan/{month}/{year}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_karyawan_ambri_las_tepi_view'])->name('hasil_kerja.lasTepi.view_hasil_karyawan');
                Route::post('{id}/{kode_pengerjaan}/{nik}/input_hasil_karyawan/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_karyawan_ambri_las_tepi_view_simpan'])->name('hasil_kerja.lasTepi.view_hasil_karyawan.simpan');
            });
            Route::prefix('las_pojok')->group(function () {
                Route::get('/', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_las_pojok'])->name('hasil_kerja.lasPojok');
            });
            Route::prefix('isi_ambri')->group(function () {
                // Route::get('/', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_isi_ambri'])->name('hasil_kerja.isiAmbri');
                Route::get('{id}/{kode_pengerjaan}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_ambri_isi_ambri'])->name('hasil_kerja.isiAmbri');
                Route::get('{id}/{kode_pengerjaan}/{tanggal}/input_hasil', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_ambri_isi_ambri_view_hasil'])->name('hasil_kerja.isiAmbri.view_hasil');
                Route::post('{id}/{kode_pengerjaan}/{tanggal}/input_hasil/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_ambri_isi_ambri_view_simpan'])->name('hasil_kerja.isiAmbri.view_hasil.simpan');
                
                Route::get('{id}/{kode_pengerjaan}/{nik}/input_hasil_karyawan/{month}/{year}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_karyawan_ambri_isi_ambri_view'])->name('hasil_kerja.isiAmbri.view_hasil_karyawan');
                Route::post('{id}/{kode_pengerjaan}/{nik}/input_hasil_karyawan/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_karyawan_ambri_isi_ambri_view_simpan'])->name('hasil_kerja.isiAmbri.view_hasil_karyawan.simpan');
            });
            Route::prefix('harian')->group(function () {
                Route::post('{id}/{kode_pengerjaan}/tambah_karyawan/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_tambah_karyawan_simpan'])->name('hasil_kerja.harian.tambah_karyawan.simpan');
            });
            Route::prefix('marketing')->group(function () {
                Route::get('{id}/{kode_pengerjaan}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_marketing'])->name('hasil_kerja.marketing');
                Route::get('{id}/{kode_pengerjaan}/tambah_karyawan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_marketing_tambah_karyawan'])->name('hasil_kerja.marketing.tambah_karyawan');
                Route::get('{id}/{kode_pengerjaan}/{nik}/{month}/{year}/input_hasil', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_marketing_view'])->name('hasil_kerja.marketing.view');
                Route::post('{id}/{kode_pengerjaan}/{nik}/{month}/{year}/input_hasil/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_marketing_simpan'])->name('hasil_kerja.marketing.simpan');
            });
            Route::prefix('ppic_tembakau')->group(function () {
                Route::get('{id}/{kode_pengerjaan}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_ppic_tembakau'])->name('hasil_kerja.ppicTembakau');
                Route::get('{id}/{kode_pengerjaan}/tambah_karyawan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_ppic_tembakau_tambah_karyawan'])->name('hasil_kerja.ppicTembakau.tambah_karyawan');
                Route::get('{id}/{kode_pengerjaan}/{nik}/{month}/{year}/input_hasil', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_ppic_tembakau_view'])->name('hasil_kerja.ppicTembakau.view');
                Route::post('{id}/{kode_pengerjaan}/{nik}/{month}/{year}/input_hasil/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_ppic_tembakau_simpan'])->name('hasil_kerja.ppicTembakau.simpan');
                // Route::get('/', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_ppic_tembakau'])->name('hasil_kerja.ppicTembakau');
            });
            Route::prefix('primary_process')->group(function () {
                // Route::get('/', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_primary_process'])->name('hasil_kerja.primaryProcess');
                Route::get('{id}/{kode_pengerjaan}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_primary_process'])->name('hasil_kerja.primaryProcess');
                Route::get('{id}/{kode_pengerjaan}/tambah_karyawan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_primary_process_tambah_karyawan'])->name('hasil_kerja.primaryProcess.tambah_karyawan');
                // Route::post('{id}/{kode_pengerjaan}/tambah_karyawan/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_tambah_karyawan_simpan'])->name('hasil_kerja.primaryProcess.tambah_karyawan.simpan');
                Route::get('{id}/{kode_pengerjaan}/{nik}/{month}/{year}/input_hasil', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_primary_process_view'])->name('hasil_kerja.primaryProcess.view');
                Route::post('{id}/{kode_pengerjaan}/{nik}/{month}/{year}/input_hasil/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_primary_process_simpan'])->name('hasil_kerja.primaryProcess.simpan');
            });
            Route::prefix('packing_b')->group(function () {
                // Route::get('/', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_packingB'])->name('hasil_kerja.packingB');
                Route::get('{id}/{kode_pengerjaan}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_packing_b'])->name('hasil_kerja.packingB');
                Route::get('{id}/{kode_pengerjaan}/tambah_karyawan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_packing_b_tambah_karyawan'])->name('hasil_kerja.packingB.tambah_karyawan');
                // Route::post('{id}/{kode_pengerjaan}/tambah_karyawan/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_tambah_karyawan_simpan'])->name('hasil_kerja.packingB.tambah_karyawan.simpan');
                Route::get('{id}/{kode_pengerjaan}/{nik}/{month}/{year}/input_hasil', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_packing_b_view'])->name('hasil_kerja.packingB.view');
                Route::post('{id}/{kode_pengerjaan}/{nik}/{month}/{year}/input_hasil/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_packing_b_simpan'])->name('hasil_kerja.packingB.simpan');
            });
            Route::prefix('ambri')->group(function () {
                // Route::get('/', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_ambri'])->name('hasil_kerja.ambri');
                Route::get('{id}/{kode_pengerjaan}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_ambri'])->name('hasil_kerja.ambri');
                Route::get('{id}/{kode_pengerjaan}/tambah_karyawan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_ambri_tambah_karyawan'])->name('hasil_kerja.ambri.tambah_karyawan');
                // Route::post('{id}/{kode_pengerjaan}/tambah_karyawan/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_tambah_karyawan_simpan'])->name('hasil_kerja.ambri.tambah_karyawan.simpan');
                Route::get('{id}/{kode_pengerjaan}/{nik}/{month}/{year}/input_hasil', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_ambri_view'])->name('hasil_kerja.ambri.view');
                Route::post('{id}/{kode_pengerjaan}/{nik}/{month}/{year}/input_hasil/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_ambri_simpan'])->name('hasil_kerja.ambri.simpan');
            });
            Route::prefix('umum')->group(function () {
                // Route::get('/', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_umum'])->name('hasil_kerja.umum');
                Route::get('{id}/{kode_pengerjaan}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_umum'])->name('hasil_kerja.umum');
                Route::get('{id}/{kode_pengerjaan}/tambah_karyawan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_umum_tambah_karyawan'])->name('hasil_kerja.umum.tambah_karyawan');
                // Route::post('{id}/{kode_pengerjaan}/tambah_karyawan/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_tambah_karyawan_simpan'])->name('hasil_kerja.umum.tambah_karyawan.simpan');
                Route::get('{id}/{kode_pengerjaan}/{nik}/{month}/{year}/input_hasil', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_umum_view'])->name('hasil_kerja.umum.view');
                Route::post('{id}/{kode_pengerjaan}/{nik}/{month}/{year}/input_hasil/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_umum_simpan'])->name('hasil_kerja.umum.simpan');
            });
            Route::prefix('supir')->group(function () {
                // Route::get('/', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_supir'])->name('hasil_kerja.supir');
                Route::get('{id}/{kode_pengerjaan}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_supir'])->name('hasil_kerja.supir');
                Route::get('{id}/{kode_pengerjaan}/tambah_karyawan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_supir_tambah_karyawan'])->name('hasil_kerja.supir.tambah_karyawan');
                // Route::post('{id}/{kode_pengerjaan}/tambah_karyawan/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_tambah_karyawan_simpan'])->name('hasil_kerja.supir.tambah_karyawan.simpan');
                Route::get('{id}/{kode_pengerjaan}/{nik}/{month}/{year}/input_hasil', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_supir_view'])->name('hasil_kerja.supir.view');
                Route::post('{id}/{kode_pengerjaan}/{nik}/{month}/{year}/input_hasil/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_supir_simpan'])->name('hasil_kerja.supir.simpan');
            });
            Route::prefix('satpam')->group(function () {
                // Route::get('/', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_satpam'])->name('hasil_kerja.satpam');
                Route::get('{id}/{kode_pengerjaan}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_satpam'])->name('hasil_kerja.satpam');
                Route::get('{id}/{kode_pengerjaan}/tambah_karyawan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_satpam_tambah_karyawan'])->name('hasil_kerja.satpam.tambah_karyawan');
                // Route::post('{id}/{kode_pengerjaan}/tambah_karyawan/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_tambah_karyawan_simpan'])->name('hasil_kerja.satpam.tambah_karyawan.simpan');
                Route::get('{id}/{kode_pengerjaan}/{nik}/{month}/{year}/input_hasil', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_satpam_view'])->name('hasil_kerja.satpam.view');
                Route::post('{id}/{kode_pengerjaan}/{nik}/{month}/{year}/input_hasil/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_satpam_simpan'])->name('hasil_kerja.satpam.simpan');
            });
            Route::prefix('supir_rit')->group(function () {
                Route::get('{kode_pengerjaan}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_supir_rit'])->name('hasil_kerja.supir_rit');
                Route::get('{kode_pengerjaan}/tambah_karyawan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_supir_rit_tambah_karyawan'])->name('hasil_kerja.supir_rit.karyawan');
                Route::post('{kode_pengerjaan}/tambah_karyawan/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_supir_rit_tambah_karyawan_simpan'])->name('hasil_kerja.supir_rit.karyawan.simpan');
                Route::get('{id}/{kode_pengerjaan}/tambah_karyawan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_supir_rit_tambah_karyawan'])->name('hasil_kerja.supir_rit.tambah_karyawan');
                // Route::post('{id}/{kode_pengerjaan}/tambah_karyawan/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_tambah_karyawan_simpan'])->name('hasil_kerja.supir_rit.tambah_karyawan.simpan');
                Route::get('{kode_pengerjaan}/{tanggal}/input', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_supir_rit_input'])->name('hasil_kerja.supir_rit.input');
                Route::post('{kode_pengerjaan}/{tanggal}/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_supir_rit_simpan'])->name('hasil_kerja.supir_rit.simpan');

                Route::get('{kode_pengerjaan}/{nik}/input_hasil_karyawan/{month}/{year}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_supir_rit_input_karyawan'])->name('hasil_kerja.supir_rit.view_hasil_karyawan');
                Route::post('{kode_pengerjaan}/{nik}/input_hasil_karyawan/{month}/{year}/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_supir_rit_input_karyawan_simpan'])->name('hasil_kerja.supir_rit.view_hasil_karyawan.simpan');

            });
        });
    });

    Route::prefix('laporan')->group(function () {
        Route::get('/', [App\Http\Controllers\LaporanController::class, 'laporan'])->name('laporan');
        Route::get('download/{id_jenis_operator}/{kode_pengerjaan}', [App\Http\Controllers\LaporanController::class, 'laporan_excel'])->name('laporan.download');
        Route::prefix('borongan')->group(function () {
            Route::get('/', [App\Http\Controllers\LaporanController::class, 'laporan_borongan_index'])->name('laporan.borongan');
            Route::get('export/{id_jenis_pekerjaan}/{id}/{kode_pengerjaan}', [App\Http\Controllers\LaporanController::class, 'laporan_borongan_export'])->name('laporan.borongan.export');
            Route::get('export/{id}', function($id){
                return 'Success '.$id;
            })->name('export');
            Route::get('testing_export', function(){
                return view('backend.laporan.borongan.excel_laporan_borongan');
            });
        });
        Route::prefix('harian')->group(function () {
            Route::get('/', [App\Http\Controllers\LaporanController::class, 'laporan_harian_index'])->name('laporan.harian');
            Route::get('export/{id_jenis_pekerjaan}/{id}/{kode_pengerjaan}', [App\Http\Controllers\LaporanController::class, 'laporan_harian_export'])->name('laporan.harian.export');
        });
        Route::prefix('supir_rit')->group(function () {
            Route::get('/', [App\Http\Controllers\LaporanController::class, 'laporan_supir_rit_index'])->name('laporan.supir_rit');
            Route::get('export/{kode_pengerjaan}', [App\Http\Controllers\LaporanController::class, 'laporan_supir_rit_export'])->name('laporan.supir_rit.export');
        });
        
        Route::prefix('thr')->group(function () {
            Route::get('/', [App\Http\Controllers\LaporanController::class, 'laporan_thr'])->name('laporan.thr');
            Route::prefix('{periode}')->group(function () {
                Route::get('/', [App\Http\Controllers\LaporanController::class, 'laporan_thr_periode'])->name('laporan.thr.periode');
                Route::get('slip_gaji', [App\Http\Controllers\LaporanController::class, 'laporan_thr_slip_gaji'])->name('laporan.thr.slip_gaji');
            });
            // Route::get('{kode_pengerjaan}', [App\Http\Controllers\LaporanController::class, 'laporan_thr_pengerjaan'])->name('laporan.thr.pengerjaan');
            // Route::get('{kode_pengerjaan}/{periode}', [App\Http\Controllers\LaporanController::class, 'laporan_thr_pengerjaan_periode'])->name('laporan.thr.pengerjaan.periode');
        });
    });

    Route::prefix('payrol')->group(function () {
        Route::prefix('borongan')->group(function () {
            Route::get('/', [App\Http\Controllers\PayrolController::class, 'borongan'])->name('payrol.borongan');
            Route::get('{kode_pengerjaan}/slip_gaji', [App\Http\Controllers\PayrolController::class, 'borongan_slip_gaji'])->name('payrol.borongan.slip_gaji');
            Route::get('{kode_pengerjaan}/slip_gaji/view', [App\Http\Controllers\PayrolController::class, 'borongan_slip_gaji_view'])->name('payrol.borongan.slip_gaji.view');
            Route::get('{kode_pengerjaan}/bank', [App\Http\Controllers\PayrolController::class, 'borongan_bank'])->name('payrol.borongan.bank');
            Route::get('{kode_pengerjaan}/report', [App\Http\Controllers\PayrolController::class, 'borongan_weekly_report'])->name('payrol.borongan.weekly_report');
            Route::get('{kode_pengerjaan}/detail_slip_gaji', [App\Http\Controllers\PayrolController::class, 'borongan_detail_kirim_slip_gaji'])->name('payrol.borongan.borongan_detail_kirim_slip_gaji');
            // Route::post('{kode_pengerjaan}/detail_kirim_email/kirim_gaji', [App\Http\Controllers\PayrolController::class, 'borongan_kirim_slip_gaji'])->name('payrol.borongan.borongan_kirim_slip_gaji');
            Route::get('{kode_pengerjaan}/detail_kirim_email/kirim_gaji', [App\Http\Controllers\PayrolController::class, 'borongan_kirim_slip_gaji_new'])->name('payrol.borongan.borongan_kirim_slip_gaji');
            Route::get('{kode_pengerjaan}/detail_kirim_email/cek_kirim_gaji', [App\Http\Controllers\PayrolController::class, 'borongan_cek_email_slip_gaji'])->name('payrol.borongan.borongan_cek_email_slip_gaji');
            Route::get('{kode_pengerjaan}/detail_kirim_email/{id}', [App\Http\Controllers\PayrolController::class, 'borongan_cek_slip_gaji'])->name('payrol.borongan.borongan_cek_slip_gaji');
            Route::get('{kode_pengerjaan}/detail_kirim_email/{id}/kirim_ulang', [App\Http\Controllers\PayrolController::class, 'borongan_cek_email_kirim_ulang'])->name('payrol.borongan.borongan_cek_email_kirim_ulang');
            // Route::get('test', function (Codedge\Fpdf\Fpdf\Fpdf $fpdf) {

            //     $fpdf->AddPage();
            //     $fpdf->SetFont('Courier', 'B', 18);
            //     $fpdf->Cell(50, 25, 'Hello World!');
            //     $fpdf->Output();
            //     exit;
            
            // });
        });
        Route::prefix('harian')->group(function () {
            Route::get('/', [App\Http\Controllers\PayrolController::class, 'harian'])->name('payrol.harian');
            Route::get('{kode_pengerjaan}/slip_gaji', [App\Http\Controllers\PayrolController::class, 'harian_slip_gaji'])->name('payrol.harian.slip_gaji');
            Route::get('{kode_pengerjaan}/bank', [App\Http\Controllers\PayrolController::class, 'harian_bank'])->name('payrol.harian.bank');
            Route::get('{kode_pengerjaan}/report', [App\Http\Controllers\PayrolController::class, 'harian_weekly_report'])->name('payrol.harian.weekly_report');
            Route::get('{kode_pengerjaan}/detail_slip_gaji', [App\Http\Controllers\PayrolController::class, 'harian_detail_kirim_slip_gaji'])->name('payrol.harian.harian_detail_kirim_slip_gaji');
            // Route::post('{kode_pengerjaan}/detail_kirim_email/kirim_gaji', [App\Http\Controllers\PayrolController::class, 'harian_kirim_slip_gaji'])->name('payrol.harian.harian_kirim_slip_gaji');
            Route::get('{kode_pengerjaan}/detail_kirim_email/kirim_gaji', [App\Http\Controllers\PayrolController::class, 'harian_kirim_slip_gaji_new'])->name('payrol.harian.harian_kirim_slip_gaji');
            Route::get('{kode_pengerjaan}/detail_kirim_email/cek_kirim_gaji', [App\Http\Controllers\PayrolController::class, 'harian_cek_email_slip_gaji'])->name('payrol.harian.harian_cek_email_slip_gaji');
            Route::get('{kode_pengerjaan}/detail_kirim_email/{id}', [App\Http\Controllers\PayrolController::class, 'harian_cek_slip_gaji'])->name('payrol.harian.harian_cek_slip_gaji');
            Route::get('{kode_pengerjaan}/detail_kirim_email/{id}/kirim_ulang', [App\Http\Controllers\PayrolController::class, 'harian_cek_email_kirim_ulang'])->name('payrol.harian.harian_cek_email_kirim_ulang');

        });
        Route::prefix('supir_rit')->group(function () {
            Route::get('/', [App\Http\Controllers\PayrolController::class, 'supir_rit'])->name('payrol.supir_rit');
            Route::get('{kode_pengerjaan}/slip_gaji', [App\Http\Controllers\PayrolController::class, 'supir_rit_slip_gaji'])->name('payrol.supir_rit.slip_gaji');
            Route::get('{kode_pengerjaan}/bank', [App\Http\Controllers\PayrolController::class, 'supir_rit_bank'])->name('payrol.supir_rit.bank');
            Route::get('{kode_pengerjaan}/report', [App\Http\Controllers\PayrolController::class, 'supir_rit_weekly_report'])->name('payrol.supir_rit.weekly_report');
            Route::get('{kode_pengerjaan}/detail_slip_gaji', [App\Http\Controllers\PayrolController::class, 'supir_rit_detail_kirim_slip_gaji'])->name('payrol.supir_rit.supir_rit_detail_kirim_slip_gaji');
            // Route::post('{kode_pengerjaan}/detail_kirim_email/kirim_gaji', [App\Http\Controllers\PayrolController::class, 'supir_rit_kirim_slip_gaji'])->name('payrol.supir_rit.supir_rit_kirim_slip_gaji');
            Route::get('{kode_pengerjaan}/detail_kirim_email/kirim_gaji', [App\Http\Controllers\PayrolController::class, 'supir_rit_kirim_slip_gaji_new'])->name('payrol.supir_rit.supir_rit_kirim_slip_gaji');
            Route::get('{kode_pengerjaan}/detail_kirim_email/cek_kirim_gaji', [App\Http\Controllers\PayrolController::class, 'supir_rit_cek_email_slip_gaji'])->name('payrol.supir_rit.supir_rit_cek_email_slip_gaji');
            Route::get('{kode_pengerjaan}/detail_kirim_email/{id}', [App\Http\Controllers\PayrolController::class, 'supir_rit_cek_slip_gaji'])->name('payrol.supir_rit.supir_rit_cek_slip_gaji');
            Route::get('{kode_pengerjaan}/detail_kirim_email/{id}/kirim_ulang', [App\Http\Controllers\PayrolController::class, 'supir_rit_cek_email_kirim_ulang'])->name('payrol.supir_rit.supir_rit_cek_email_kirim_ulang');

        });
        Route::prefix('operator_karyawan')->group(function () {
            Route::get('/', [App\Http\Controllers\OperatorKaryawanController::class, 'index'])->name('operator_karyawan');
            Route::get('create', [App\Http\Controllers\OperatorKaryawanController::class, 'create'])->name('operator_karyawan.create');
            Route::post('simpan', [App\Http\Controllers\OperatorKaryawanController::class, 'simpan'])->name('operator_karyawan.simpan');
            Route::post('select_biodata_karyawan', [App\Http\Controllers\OperatorKaryawanController::class, 'select_biodata_karyawan'])->name('operator_karyawan.select_biodata_karyawan');
            Route::post('select_jenis_operator_detail', [App\Http\Controllers\OperatorKaryawanController::class, 'select_jenis_operator_detail'])->name('operator_karyawan.select_jenis_operator_detail');
            Route::post('select_jenis_operator_detail_pekerjaan', [App\Http\Controllers\OperatorKaryawanController::class, 'select_jenis_operator_detail_pekerjaan'])->name('operator_karyawan.select_jenis_operator_detail_pekerjaan');
            Route::get('{id}', [App\Http\Controllers\OperatorKaryawanController::class, 'detail'])->name('operator_karyawan.detail');
            Route::post('update', [App\Http\Controllers\OperatorKaryawanController::class, 'update'])->name('operator_karyawan.update');
        });
        Route::prefix('operator_karyawan_harian')->group(function () {
            Route::get('/', [App\Http\Controllers\OperatorKaryawanController::class, 'karyawan_operator_harian'])->name('operator_karyawan_harian');
            Route::post('simpan', [App\Http\Controllers\OperatorKaryawanController::class, 'karyawan_operator_harian_simpan'])->name('operator_karyawan_harian_simpan');
            Route::get('{id}', [App\Http\Controllers\OperatorKaryawanController::class, 'karyawan_operator_harian_detail'])->name('operator_karyawan_harian_detail');
            Route::post('update', [App\Http\Controllers\OperatorKaryawanController::class, 'karyawan_operator_harian_update'])->name('operator_karyawan_harian_update');
            Route::get('{id}/delete', [App\Http\Controllers\OperatorKaryawanController::class, 'karyawan_operator_harian_hapus'])->name('operator_karyawan_harian_hapus');
        });
        Route::prefix('operator_karyawan_supir_rit')->group(function () {
            Route::get('/', [App\Http\Controllers\OperatorKaryawanController::class, 'karyawan_operator_supir_rit'])->name('operator_karyawan_supir_rit');
            Route::post('simpan', [App\Http\Controllers\OperatorKaryawanController::class, 'karyawan_operator_supir_rit_simpan'])->name('operator_karyawan_supir_rit_simpan');
            Route::get('{id}', [App\Http\Controllers\OperatorKaryawanController::class, 'karyawan_operator_supir_rit_detail'])->name('operator_karyawan_supir_rit_detail');
            Route::post('update', [App\Http\Controllers\OperatorKaryawanController::class, 'karyawan_operator_supir_rit_update'])->name('operator_karyawan_supir_rit_update');
            Route::get('{id}/delete', [App\Http\Controllers\OperatorKaryawanController::class, 'karyawan_operator_supir_rit_hapus'])->name('operator_karyawan_supir_rit_hapus');
        });
    });
    
    Route::prefix('jenis_operator')->group(function () {
        Route::get('/', [App\Http\Controllers\JenisOperatorController::class, 'index'])->name('jenis_operator');
        Route::post('simpan', [App\Http\Controllers\JenisOperatorController::class, 'simpan'])->name('jenis_operator.simpan');
        Route::get('{id}', [App\Http\Controllers\JenisOperatorController::class, 'detail'])->name('jenis_operator.detail');
        Route::post('{id}/simpan', [App\Http\Controllers\JenisOperatorController::class, 'detail_simpan'])->name('jenis_operator.detail.simpan');
        Route::get('{id}/pengerjaan/{id_pengerjaan}', [App\Http\Controllers\JenisOperatorController::class, 'detail_pengerjaan'])->name('jenis_operator.detail.pengerjaan');
        Route::post('{id}/pengerjaan/{id_pengerjaan}/simpan', [App\Http\Controllers\JenisOperatorController::class, 'detail_pengerjaan_simpan'])->name('jenis_operator.detail.pengerjaan.simpan');
    });
    
    Route::prefix('umk_supir_rit')->group(function () {
        Route::get('/', [App\Http\Controllers\JenisUMKSupirRitController::class, 'index'])->name('umk_supir_rit.index');
        Route::post('simpan', [App\Http\Controllers\JenisUMKSupirRitController::class, 'simpan'])->name('umk_supir_rit.simpan');
        Route::post('update', [App\Http\Controllers\JenisUMKSupirRitController::class, 'update'])->name('umk_supir_rit.update');
        Route::get('{id}', [App\Http\Controllers\JenisUMKSupirRitController::class, 'detail'])->name('umk_supir_rit.detail');
        Route::get('{id}/delete', [App\Http\Controllers\JenisUMKSupirRitController::class, 'delete'])->name('umk_supir_rit.delete');
    });

    Route::prefix('jenis_umk_borongan')->group(function () {
        Route::prefix('lokal')->group(function () {
            Route::get('/', [App\Http\Controllers\JenisUMKBoronganController::class, 'lokal'])->name('jenis_umk_borongan.lokal');
            Route::post('simpan', [App\Http\Controllers\JenisUMKBoronganController::class, 'lokal_simpan'])->name('jenis_umk_borongan.lokal.simpan');
            Route::get('{id}', [App\Http\Controllers\JenisUMKBoronganController::class, 'lokal_detail'])->name('jenis_umk_borongan.lokal.detail');
            Route::post('update', [App\Http\Controllers\JenisUMKBoronganController::class, 'lokal_update'])->name('jenis_umk_borongan.lokal.update');
            Route::get('{id}/delete', [App\Http\Controllers\JenisUMKBoronganController::class, 'lokal_delete'])->name('jenis_umk_borongan.lokal.delete');
        });
        Route::prefix('lokal_stempel')->group(function () {
            Route::get('/', [App\Http\Controllers\JenisUMKBoronganController::class, 'lokal_umk_stempel'])->name('jenis_umk_borongan.lokal_umk_stempel');
            Route::get('{id}', [App\Http\Controllers\JenisUMKBoronganController::class, 'lokal_umk_stempel_detail'])->name('jenis_umk_borongan.lokal_umk_stempel_detail');
            Route::post('simpan', [App\Http\Controllers\JenisUMKBoronganController::class, 'lokal_umk_stempel_simpan'])->name('jenis_umk_borongan.lokal_umk_stempel_simpan');
            Route::post('{id}/update', [App\Http\Controllers\JenisUMKBoronganController::class, 'lokal_umk_stempel_update'])->name('jenis_umk_borongan.lokal_umk_stempel_update');
            Route::get('{id}/delete', [App\Http\Controllers\JenisUMKBoronganController::class, 'lokal_umk_stempel_delete'])->name('jenis_umk_borongan.lokal_umk_stempel_delete');
        });
        Route::prefix('ekspor')->group(function () {
            Route::get('/', [App\Http\Controllers\JenisUMKBoronganController::class, 'ekspor'])->name('jenis_umk_borongan.ekspor');
            Route::post('simpan', [App\Http\Controllers\JenisUMKBoronganController::class, 'ekspor_simpan'])->name('jenis_umk_borongan.ekspor.simpan');
            Route::get('{id}', [App\Http\Controllers\JenisUMKBoronganController::class, 'ekspor_detail'])->name('jenis_umk_borongan.ekspor.detail');
            Route::post('update', [App\Http\Controllers\JenisUMKBoronganController::class, 'ekspor_update'])->name('jenis_umk_borongan.ekspor.update');
            Route::get('{id}/delete', [App\Http\Controllers\JenisUMKBoronganController::class, 'ekspor_delete'])->name('jenis_umk_borongan.ekspor.delete');
        });
        Route::prefix('ambri')->group(function () {
            Route::get('/', [App\Http\Controllers\JenisUMKBoronganController::class, 'ambri'])->name('jenis_umk_borongan.ambri');
            Route::post('simpan', [App\Http\Controllers\JenisUMKBoronganController::class, 'ambri_simpan'])->name('jenis_umk_borongan.ambri.simpan');
            Route::get('{id}', [App\Http\Controllers\JenisUMKBoronganController::class, 'ambri_detail'])->name('jenis_umk_borongan.ambri.detail');
            Route::post('update', [App\Http\Controllers\JenisUMKBoronganController::class, 'ambri_update'])->name('jenis_umk_borongan.ambri.update');
            Route::get('{id}/delete', [App\Http\Controllers\JenisUMKBoronganController::class, 'ambri_delete'])->name('jenis_umk_borongan.ambri.delete');
        });
    });

    Route::prefix('umk_periode')->group(function () {
        Route::get('/', [App\Http\Controllers\UMKPeriodeController::class, 'umk_periode'])->name('umk_periode.lokal_umk_periode');
        Route::post('lokal/simpan/{tahun_aktif}', [App\Http\Controllers\UMKPeriodeController::class, 'umk_periode_simpan'])->name('umk_periode.lokal_umk_periode_simpan');
        Route::post('ekspor/simpan/{tahun_aktif}', [App\Http\Controllers\UMKPeriodeController::class, 'umk_borongan_ekspor_simpan'])->name('umk_periode.umk_borongan_ekspor_simpan');
        Route::post('ambri/simpan/{tahun_aktif}', [App\Http\Controllers\UMKPeriodeController::class, 'umk_borongan_ambri_simpan'])->name('umk_periode.umk_borongan_ambri_simpan');
        Route::post('supir_rit/simpan/{tahun_aktif}', [App\Http\Controllers\UMKPeriodeController::class, 'umk_supir_rit_simpan'])->name('umk_periode.umk_supir_rit_simpan');
    });
    
    Route::prefix('bpjs')->group(function () {
        Route::prefix('jht')->group(function () {
            Route::get('/', [App\Http\Controllers\BpjsController::class, 'jht_index'])->name('bpjs.jht');
            Route::post('simpan', [App\Http\Controllers\BpjsController::class, 'jht_simpan'])->name('bpjs.jht.simpan');
            Route::get('{id}', [App\Http\Controllers\BpjsController::class, 'jht_detail'])->name('bpjs.jht.detail');
            Route::post('update', [App\Http\Controllers\BpjsController::class, 'jht_update'])->name('bpjs.jht.update');
            Route::get('{id}/delete', [App\Http\Controllers\BpjsController::class, 'jht_delete'])->name('bpjs.jht.delete');
        });
        Route::prefix('kesehatan')->group(function () {
            Route::get('/', [App\Http\Controllers\BpjsController::class, 'bpjs_kesehatan_index'])->name('bpjs.kesehatan');
            Route::post('simpan', [App\Http\Controllers\BpjsController::class, 'bpjs_kesehatan_simpan'])->name('bpjs.kesehatan.simpan');
            Route::get('{id}', [App\Http\Controllers\BpjsController::class, 'bpjs_kesehatan_detail'])->name('bpjs.kesehatan.detail');
            Route::post('update', [App\Http\Controllers\BpjsController::class, 'bpjs_kesehatan_update'])->name('bpjs.kesehatan.update');
            Route::get('{id}/delete', [App\Http\Controllers\BpjsController::class, 'bpjs_kesehatan_delete'])->name('bpjs.kesehatan.delete');
        });
    });
    
    Route::prefix('tunjangan_kerja')->group(function () {
        Route::get('/', [App\Http\Controllers\TunjanganKerjaController::class, 'index'])->name('tunjangan_kerja');
        Route::post('simpan', [App\Http\Controllers\TunjanganKerjaController::class, 'simpan'])->name('tunjangan_kerja.simpan');
    });

    Route::prefix('pengguna')->group(function () {
        Route::get('user', [App\Http\Controllers\UserController::class, 'user'])->name('pengguna.user');
        Route::post('user/simpan', [App\Http\Controllers\UserController::class, 'simpan'])->name('pengguna.user.simpan');
        Route::get('user/{id}/reset', [App\Http\Controllers\UserController::class, 'reset_pswd'])->name('pengguna.user.reset_pswd');
    });

    Route::prefix('roles')->group(function () {
        Route::get('/', [App\Http\Controllers\RolesController::class, 'index'])->name('roles');
    });

    Route::prefix('management-user')->group(function () {
        Route::get('/', [App\Http\Controllers\UserController::class, 'user_management_index'])->name('user_management');
        Route::get('{id}/edit', [App\Http\Controllers\UserController::class, 'user_management_detail'])->name('user_management.detail');
        Route::post('simpan', [App\Http\Controllers\UserController::class, 'user_management_simpan'])->name('user_management.simpan');
        Route::post('update', [App\Http\Controllers\UserController::class, 'user_management_update'])->name('user_management.update');
    });

    Route::prefix('periode')->group(function () {
        Route::get('/', [App\Http\Controllers\PengerjaanController::class, 'close_periode'])->name('periode.close_periode');
        Route::get('submit', [App\Http\Controllers\PengerjaanController::class, 'close_periode_update'])->name('periode.close_periode.submit');

        Route::get('b', [App\Http\Controllers\PeriodeController::class, 'index'])->name('b.periode');
        Route::get('b/{id}/on', [App\Http\Controllers\PeriodeController::class, 'status_on'])->name('b.periode.status_on');
        Route::get('b/{id}/off', [App\Http\Controllers\PeriodeController::class, 'status_off'])->name('b.periode.status_off');

    });

    Route::prefix('hasil_kerja')->group(function () {
        Route::get('/', [App\Http\Controllers\HasilKerjaController::class, 'index'])->name('hasil_kerja');
    });
    
    Route::prefix('cut_off')->group(function () {
        Route::get('/', [App\Http\Controllers\CutOffController::class, 'index'])->name('cut_off');
        Route::post('simpan', [App\Http\Controllers\CutOffController::class, 'simpan'])->name('cut_off.simpan');
        Route::post('update', [App\Http\Controllers\CutOffController::class, 'update'])->name('cut_off.update');
        Route::get('{id}', [App\Http\Controllers\CutOffController::class, 'detail'])->name('cut_off.detail');
    });

    Route::get('testing', [App\Http\Controllers\TestingController::class, 'testing'])->name('testing');
    Route::get('new_testing/month?month={id}', function($id){
        return $id;
    });
    Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);
});


// Route::get('testMailBorongan', function(){
//     $cek_kirim_slip_gajis = \App\Models\KirimGaji::where('kode_pengerjaan','LIKE','%PB_%')
//                                                 ->where('status','menunggu')
//                                                 ->limit(2)
//                                                 ->orderBy('id','asc')
//                                                 ->get();

//     foreach ($cek_kirim_slip_gajis as $key => $value) {
//         // $data['new_data_pengerjaan'] = \App\Models\NewDataPengerjaan::where('kode_pengerjaan', $kode_pengerjaan)->where('status','n')->first();

//         $data['explode_tanggal_pengerjaans'] = explode('#', $value->new_data_pengerjaan->tanggal);
//         $data['exp_tanggals'] = array_filter($data['explode_tanggal_pengerjaans']);
//         $data['a'] = count($data['exp_tanggals']);

//         $data['exp_tgl_awal'] = explode('-', $data['exp_tanggals'][1]);
//         $data['exp_tgl_akhir'] = explode('-', $data['exp_tanggals'][$data['a']]);

//         $pengerjaan_weekly = \App\Models\PengerjaanWeekly::where('kode_pengerjaan', $value->kode_pengerjaan)
//                                                         ->where('id', $value->pengerjaan_id)
//                                                         ->first();

//         $data['pengerjaans'] = \App\Models\Pengerjaan::where('operator_karyawan_id', $pengerjaan_weekly->operator_karyawan_id)
//                                                     ->where('kode_pengerjaan', $value->kode_pengerjaan)
//                                                     ->get();

//         $data['tanggal'] = \Carbon\Carbon::parse($data['exp_tgl_awal'][0] . '-' . $data['exp_tgl_awal'][1] . '-' . $data['exp_tgl_awal'][2])->isoFormat('D MMMM').' sampai '.\Carbon\Carbon::parse($data['exp_tgl_akhir'][0] . '-' . $data['exp_tgl_akhir'][1] . '-' . $data['exp_tgl_akhir'][2])->isoFormat('D MMMM YYYY');

//         $data['pengerjaan_weekly'] = $pengerjaan_weekly;

//         $data['total_upah_hasil_kerja'] = [];
//         $data['total_lembur_kerja'] = [];

//         foreach ($data['pengerjaans'] as $key => $pengerjaan) {
//             #Borongan Packing
//             if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 1) {
//                 for ($i = 1; $i <= 5; $i++) {
//                     ${'explode_hasil_kerja_' . $i} = explode('|', $pengerjaan['hasil_kerja_' . $i]);
//                     ${'umk_borongan_lokal_' . $i} = \App\Models\UMKBoronganLokal::where('id', ${'explode_hasil_kerja_' . $i}[0])->first();
//                     if (empty(${'umk_borongan_lokal_' . $i})) {
//                         ${'jenis_produk_' . $i} = '-';
//                         ${'hasil_kerja_' . $i} = null;
//                         ${'data_explode_hasil_kerja_' . $i} = '-';
//                         ${'lembur_' . $i} = 1;
//                         ${'total_hasil_' . $i} = 0;
//                     } else {
//                         ${'jenis_produk_' . $i} = ${'umk_borongan_lokal_' . $i}['jenis_produk'];
//                         ${'hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1] * ${'umk_borongan_lokal_' . $i}['umk_packing'];
//                         ${'data_explode_hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1];
//                         ${'explode_lembur_' . $i} = explode('|', $pengerjaan['lembur']);
//                         ${'explode_status_lembur_' . $i} = explode('-', ${'explode_lembur_' . $i}[$i]);
//                         if (${'explode_status_lembur_' . $i}[1] == 'y') {
//                             ${'lembur_' . $i} = 1.5;
//                         } else {
//                             ${'lembur_' . $i} = 1;
//                         }
//                     }
//                 }
//             }

//             #Borongan Bandrol
//             if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 2) {
//                 for ($i = 1; $i <= 5; $i++) {
//                     ${'explode_hasil_kerja_' . $i} = explode('|', $pengerjaan['hasil_kerja_' . $i]);
//                     ${'umk_borongan_lokal_' . $i} = \App\Models\UMKBoronganLokal::where('id', ${'explode_hasil_kerja_' . $i}[0])->first();
//                     if (empty(${'umk_borongan_lokal_' . $i})) {
//                         ${'jenis_produk_' . $i} = '-';
//                         ${'hasil_kerja_' . $i} = null;
//                         ${'data_explode_hasil_kerja_' . $i} = '-';
//                         ${'lembur_' . $i} = 1;
//                         ${'total_hasil_' . $i} = 0;
//                     } else {
//                         ${'jenis_produk_' . $i} = ${'umk_borongan_lokal_' . $i}['jenis_produk'];
//                         ${'hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1] * ${'umk_borongan_lokal_' . $i}['umk_bandrol'];
//                         ${'data_explode_hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1];
//                         ${'explode_lembur_' . $i} = explode('|', $pengerjaan['lembur']);
//                         ${'explode_status_lembur_' . $i} = explode('-', ${'explode_lembur_' . $i}[$i]);
//                         if (${'explode_status_lembur_' . $i}[1] == 'y') {
//                             ${'lembur_' . $i} = 1.5;
//                         } else {
//                             ${'lembur_' . $i} = 1;
//                         }
//                     }
//                 }
//             }

//             #Borongan Inner
//             if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 3) {
//                 for ($i = 1; $i <= 5; $i++) {
//                     ${'explode_hasil_kerja_' . $i} = explode('|', $pengerjaan['hasil_kerja_' . $i]);
//                     ${'umk_borongan_lokal_' . $i} = \App\Models\UMKBoronganLokal::where('id', ${'explode_hasil_kerja_' . $i}[0])->first();
//                     if (empty(${'umk_borongan_lokal_' . $i})) {
//                         ${'jenis_produk_' . $i} = '-';
//                         ${'hasil_kerja_' . $i} = null;
//                         ${'data_explode_hasil_kerja_' . $i} = '-';
//                         ${'lembur_' . $i} = 1;
//                         ${'total_hasil_' . $i} = 0;
//                     } else {
//                         ${'jenis_produk_' . $i} = ${'umk_borongan_lokal_' . $i}['jenis_produk'];
//                         ${'hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1] * ${'umk_borongan_lokal_' . $i}['umk_inner'];
//                         ${'data_explode_hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1];
//                         ${'explode_lembur_' . $i} = explode('|', $pengerjaan['lembur']);
//                         ${'explode_status_lembur_' . $i} = explode('-', ${'explode_lembur_' . $i}[$i]);
//                         if (${'explode_status_lembur_' . $i}[1] == 'y') {
//                             ${'lembur_' . $i} = 1.5;
//                         } else {
//                             ${'lembur_' . $i} = 1;
//                         }
//                     }
//                 }
//             }

//             #Borongan Outer
//             if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 4) {
//                 for ($i = 1; $i <= 5; $i++) {
//                     ${'explode_hasil_kerja_' . $i} = explode('|', $pengerjaan['hasil_kerja_' . $i]);
//                     ${'umk_borongan_lokal_' . $i} = \App\Models\UMKBoronganLokal::where('id', ${'explode_hasil_kerja_' . $i}[0])->first();
//                     if (empty(${'umk_borongan_lokal_' . $i})) {
//                         ${'jenis_produk_' . $i} = '-';
//                         ${'hasil_kerja_' . $i} = null;
//                         ${'data_explode_hasil_kerja_' . $i} = '-';
//                         ${'lembur_' . $i} = 1;
//                         ${'total_hasil_' . $i} = 0;
//                     } else {
//                         ${'jenis_produk_' . $i} = ${'umk_borongan_lokal_' . $i}['jenis_produk'];
//                         ${'hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1] * ${'umk_borongan_lokal_' . $i}['umk_outer'];
//                         ${'data_explode_hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1];
//                         ${'explode_lembur_' . $i} = explode('|', $pengerjaan['lembur']);
//                         ${'explode_status_lembur_' . $i} = explode('-', ${'explode_lembur_' . $i}[$i]);
//                         if (${'explode_status_lembur_' . $i}[1] == 'y') {
//                             ${'lembur_' . $i} = 1.5;
//                         } else {
//                             ${'lembur_' . $i} = 1;
//                         }
//                     }
//                 }
//             }

//             #Borongan Stempel Lokal
//             if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 25) {
//                 for ($i = 1; $i <= 5; $i++) {
//                     ${'explode_hasil_kerja_' . $i} = explode('|', $pengerjaan['hasil_kerja_' . $i]);
//                     ${'umk_borongan_lokal_' . $i} = \App\Models\UMKBoronganStempel::where('id', ${'explode_hasil_kerja_' . $i}[0])->first();
//                     if (empty(${'umk_borongan_lokal_' . $i})) {
//                         ${'jenis_produk_' . $i} = '-';
//                         ${'hasil_kerja_' . $i} = null;
//                         ${'data_explode_hasil_kerja_' . $i} = '-';
//                         ${'lembur_' . $i} = 1;
//                         ${'total_hasil_' . $i} = 0;
//                     } else {
//                         ${'jenis_produk_' . $i} = ${'umk_borongan_lokal_' . $i}['jenis_produk'];
//                         ${'hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1] * ${'umk_borongan_lokal_' . $i}['nominal_umk'];
//                         ${'data_explode_hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1];
//                         ${'explode_lembur_' . $i} = explode('|', $pengerjaan['lembur']);
//                         ${'explode_status_lembur_' . $i} = explode('-', ${'explode_lembur_' . $i}[$i]);
//                         if (${'explode_status_lembur_' . $i}[1] == 'y') {
//                             ${'lembur_' . $i} = 1.5;
//                         } else {
//                             ${'lembur_' . $i} = 1;
//                         }
//                     }
//                 }
//             }

//             #Borongan Ekspor Packing
//             if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 5) {
//                 for ($i = 1; $i <= 5; $i++) {
//                     ${'explode_hasil_kerja_' . $i} = explode('|', $pengerjaan['hasil_kerja_' . $i]);
//                     ${'umk_borongan_lokal_' . $i} = \App\Models\UMKBoronganEkspor::where('id', ${'explode_hasil_kerja_' . $i}[0])->first();
//                     if (empty(${'umk_borongan_lokal_' . $i})) {
//                         ${'jenis_produk_' . $i} = '-';
//                         ${'hasil_kerja_' . $i} = null;
//                         ${'data_explode_hasil_kerja_' . $i} = '-';
//                         ${'lembur_' . $i} = 1;
//                         ${'total_hasil_' . $i} = 0;
//                     } else {
//                         ${'jenis_produk_' . $i} = ${'umk_borongan_lokal_' . $i}['jenis_produk'];
//                         ${'hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1] * ${'umk_borongan_lokal_' . $i}['umk_packing'];
//                         ${'data_explode_hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1];
//                         ${'explode_lembur_' . $i} = explode('|', $pengerjaan['lembur']);
//                         ${'explode_status_lembur_' . $i} = explode('-', ${'explode_lembur_' . $i}[$i]);
//                         if (${'explode_status_lembur_' . $i}[1] == 'y') {
//                             ${'lembur_' . $i} = 1.5;
//                         } else {
//                             ${'lembur_' . $i} = 1;
//                         }
//                     }
//                 }
//             }

//             #Borongan Ekspor Kemas
//             if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 6) {
//                 for ($i = 1; $i <= 5; $i++) {
//                     ${'explode_hasil_kerja_' . $i} = explode('|', $pengerjaan['hasil_kerja_' . $i]);
//                     ${'umk_borongan_lokal_' . $i} = \App\Models\UMKBoronganEkspor::where('id', ${'explode_hasil_kerja_' . $i}[0])->first();
//                     if (empty(${'umk_borongan_lokal_' . $i})) {
//                         ${'jenis_produk_' . $i} = '-';
//                         ${'hasil_kerja_' . $i} = null;
//                         ${'data_explode_hasil_kerja_' . $i} = '-';
//                         ${'lembur_' . $i} = 1;
//                         ${'total_hasil_' . $i} = 0;
//                     } else {
//                         ${'jenis_produk_' . $i} = ${'umk_borongan_lokal_' . $i}['jenis_produk'];
//                         ${'hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1] * ${'umk_borongan_lokal_' . $i}['umk_kemas'];
//                         ${'data_explode_hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1];
//                         ${'explode_lembur_' . $i} = explode('|', $pengerjaan['lembur']);
//                         ${'explode_status_lembur_' . $i} = explode('-', ${'explode_lembur_' . $i}[$i]);
//                         if (${'explode_status_lembur_' . $i}[1] == 'y') {
//                             ${'lembur_' . $i} = 1.5;
//                         } else {
//                             ${'lembur_' . $i} = 1;
//                         }
//                     }
//                 }
//             }

//             #Borongan Ekspor Gagang
//             if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 7) {
//                 for ($i = 1; $i <= 5; $i++) {
//                     ${'explode_hasil_kerja_' . $i} = explode('|', $pengerjaan['hasil_kerja_' . $i]);
//                     ${'umk_borongan_lokal_' . $i} = \App\Models\UMKBoronganEkspor::where('id', ${'explode_hasil_kerja_' . $i}[0])->first();
//                     if (empty(${'umk_borongan_lokal_' . $i})) {
//                         ${'jenis_produk_' . $i} = '-';
//                         ${'hasil_kerja_' . $i} = null;
//                         ${'data_explode_hasil_kerja_' . $i} = '-';
//                         ${'lembur_' . $i} = 1;
//                         ${'total_hasil_' . $i} = 0;
//                     } else {
//                         ${'jenis_produk_' . $i} = ${'umk_borongan_lokal_' . $i}['jenis_produk'];
//                         ${'hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1] * ${'umk_borongan_lokal_' . $i}['umk_pilih_gagang'];
//                         ${'data_explode_hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1];
//                         ${'explode_lembur_' . $i} = explode('|', $pengerjaan['lembur']);
//                         ${'explode_status_lembur_' . $i} = explode('-', ${'explode_lembur_' . $i}[$i]);
//                         if (${'explode_status_lembur_' . $i}[1] == 'y') {
//                             ${'lembur_' . $i} = 1.5;
//                         } else {
//                             ${'lembur_' . $i} = 1;
//                         }
//                     }
//                 }
//             }

//             #Borongan Ambri Isi Etiket
//             if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 8) {
//                 for ($i = 1; $i <= 5; $i++) {
//                     ${'explode_hasil_kerja_' . $i} = explode('|', $pengerjaan['hasil_kerja_' . $i]);
//                     ${'umk_borongan_lokal_' . $i} = \App\Models\UMKBoronganAmbri::where('id', ${'explode_hasil_kerja_' . $i}[0])->first();
//                     if (empty(${'umk_borongan_lokal_' . $i})) {
//                         ${'jenis_produk_' . $i} = '-';
//                         ${'hasil_kerja_' . $i} = null;
//                         ${'data_explode_hasil_kerja_' . $i} = '-';
//                         ${'lembur_' . $i} = 1;
//                         ${'total_hasil_' . $i} = 0;
//                     } else {
//                         ${'jenis_produk_' . $i} = ${'umk_borongan_lokal_' . $i}['jenis_produk'];
//                         ${'hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1] * ${'umk_borongan_lokal_' . $i}['umk_etiket'];
//                         ${'data_explode_hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1];
//                         ${'explode_lembur_' . $i} = explode('|', $pengerjaan['lembur']);
//                         ${'explode_status_lembur_' . $i} = explode('-', ${'explode_lembur_' . $i}[$i]);
//                         if (${'explode_status_lembur_' . $i}[1] == 'y') {
//                             ${'lembur_' . $i} = 1.5;
//                         } else {
//                             ${'lembur_' . $i} = 1;
//                         }
//                     }
//                 }
//             }

//             #Borongan Ambri Las Tepi
//             if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 9) {
//                 for ($i = 1; $i <= 5; $i++) {
//                     ${'explode_hasil_kerja_' . $i} = explode('|', $pengerjaan['hasil_kerja_' . $i]);
//                     ${'umk_borongan_lokal_' . $i} = \App\Models\UMKBoronganAmbri::where('id', ${'explode_hasil_kerja_' . $i}[0])->first();
//                     if (empty(${'umk_borongan_lokal_' . $i})) {
//                         ${'jenis_produk_' . $i} = '-';
//                         ${'hasil_kerja_' . $i} = null;
//                         ${'data_explode_hasil_kerja_' . $i} = '-';
//                         ${'lembur_' . $i} = 1;
//                         ${'total_hasil_' . $i} = 0;
//                     } else {
//                         ${'jenis_produk_' . $i} = ${'umk_borongan_lokal_' . $i}['jenis_produk'];
//                         ${'hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1] * ${'umk_borongan_lokal_' . $i}['umk_las_tepi'];
//                         ${'data_explode_hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1];
//                         ${'explode_lembur_' . $i} = explode('|', $pengerjaan['lembur']);
//                         ${'explode_status_lembur_' . $i} = explode('-', ${'explode_lembur_' . $i}[$i]);
//                         if (${'explode_status_lembur_' . $i}[1] == 'y') {
//                             ${'lembur_' . $i} = 1.5;
//                         } else {
//                             ${'lembur_' . $i} = 1;
//                         }
//                     }
//                 }
//             }

//             #Borongan Ambri Isi Ambri
//             if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 11) {
//                 for ($i = 1; $i <= 5; $i++) {
//                     ${'explode_hasil_kerja_' . $i} = explode('|', $pengerjaan['hasil_kerja_' . $i]);
//                     ${'umk_borongan_lokal_' . $i} = \App\Models\UMKBoronganAmbri::where('id', ${'explode_hasil_kerja_' . $i}[0])->first();
//                     if (empty(${'umk_borongan_lokal_' . $i})) {
//                         ${'jenis_produk_' . $i} = '-';
//                         ${'hasil_kerja_' . $i} = null;
//                         ${'data_explode_hasil_kerja_' . $i} = '-';
//                         ${'lembur_' . $i} = 1;
//                         ${'total_hasil_' . $i} = 0;
//                     } else {
//                         ${'jenis_produk_' . $i} = ${'umk_borongan_lokal_' . $i}['jenis_produk'];
//                         ${'hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1] * ${'umk_borongan_lokal_' . $i}['umk_ambri'];
//                         ${'data_explode_hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1];
//                         ${'explode_lembur_' . $i} = explode('|', $pengerjaan['lembur']);
//                         ${'explode_status_lembur_' . $i} = explode('-', ${'explode_lembur_' . $i}[$i]);
//                         if (${'explode_status_lembur_' . $i}[1] == 'y') {
//                             ${'lembur_' . $i} = 1.5;
//                         } else {
//                             ${'lembur_' . $i} = 1;
//                         }
//                     }
//                 }
//             }
            
//             $total_hasil_kerja = round($hasil_kerja_1 * $lembur_1 + $hasil_kerja_2 * $lembur_2 + $hasil_kerja_3 * $lembur_3 + $hasil_kerja_4 * $lembur_4 + $hasil_kerja_5 * $lembur_5) - $pengerjaan['uang_lembur'];
//             $total_lembur = $pengerjaan['uang_lembur'];

//             array_push($data['total_upah_hasil_kerja'], $total_hasil_kerja);
//             array_push($data['total_lembur_kerja'], $total_lembur);

//             if ($value->new_data_pengerjaan->akhir_bulan == 'y') {
//                 if (empty($value->tunjangan_kerja)) {
//                     $data['tunjangan_kerja'] = 0;
//                 } else {
//                     $data['tunjangan_kerja'] = $value->tunjangan_kerja;
//                 }
//             } else {
//                 $data['tunjangan_kerja'] = 0;
//             }

//             if (empty($value->tunjangan_kehadiran)) {
//                 $data['tunjangan_kehadiran'] = 0;
//             } else {
//                 $data['tunjangan_kehadiran'] = $value->tunjangan_kehadiran;
//             }

//             if (empty($value->uang_makan)) {
//                 $data['uang_makan'] = 0;
//             } else {
//                 $data['uang_makan'] = $value->uang_makan;
//             }

//             if (empty($value->plus_1)) {
//                 $data['plus_1'] = 0;
//                 $data['ket_plus_1'] = null;
//             } else {
//                 $explode_plus_1 = explode('|', $value->plus_1);
//                 $data['plus_1'] = floatval($explode_plus_1[0]);
//                 $data['ket_plus_1'] = $explode_plus_1[1];
//             }

//             if (empty($value->plus_2)) {
//                 $data['plus_2'] = 0;
//                 $data['ket_plus_2'] = null;
//             } else {
//                 $explode_plus_2 = explode('|', $value->plus_2);
//                 $data['plus_2'] = floatval($explode_plus_2[0]);
//                 $data['ket_plus_2'] = $explode_plus_2[1];
//             }

//             if (empty($value->plus_3)) {
//                 $data['plus_3'] = 0;
//                 $data['ket_plus_3'] = null;
//             } else {
//                 $explode_plus_3 = explode('|', $value->plus_3);
//                 $data['plus_3'] = floatval($explode_plus_3[0]);
//                 $data['ket_plus_3'] = $explode_plus_3[1];
//             }

//             if (empty($value->jht)) {
//                 $data['jht'] = 0;
//             } else {
//                 $data['jht'] = $value->jht;
//             }

//             if (empty($value->bpjs_kesehatan)) {
//                 $data['bpjs_kesehatan'] = 0;
//             } else {
//                 $data['bpjs_kesehatan'] = $value->bpjs_kesehatan;
//             }

//             if (empty($value->minus_1)) {
//                 $data['minus_1'] = '0';
//                 $data['ket_minus_1'] = null;
//             } else {
//                 $explode_minus_1 = explode('|', $value->minus_1);
//                 $data['minus_1'] = floatval($explode_minus_1[0]);
//                 $data['ket_minus_1'] = $explode_minus_1[1];
//             }

//             if (empty($value->minus_2)) {
//                 $data['minus_2'] = 0;
//                 $data['ket_minus_2'] = null;
//             } else {
//                 $explode_minus_2 = explode('|', $value->minus_2);
//                 $data['minus_2'] = floatval($explode_minus_2[0]);
//                 $data['ket_minus_2'] = $explode_minus_2[1];
//             }

//             $data['total_gaji_diterima'] = (array_sum($data['total_upah_hasil_kerja']) + array_sum($data['total_lembur_kerja']) + $data['tunjangan_kerja'] + $data['tunjangan_kehadiran'] + $data['uang_makan'] + $data['plus_1'] + $data['plus_2'] + $data['plus_3']) - ($data['jht'] + $data['bpjs_kesehatan'] + $data['minus_1'] + $data['minus_2']);
//         }

//         $pdf = \Pdf::loadView('email.slipGajiOperatorBorongan',$data);
//         $pdf->setPaper(array(0,0,560,380));
//         $pdf->setEncryption('01101997','01101997');

//         \Mail::send('email.testingMail',$data, function($message) use($data,$pdf,$pengerjaan_weekly){
//             $message->to('rioanugrah999@gmail.com')
//                     ->subject('Laporan Slip Gaji '.$pengerjaan_weekly->operator_karyawan->biodata_karyawan->nama.' '.date('d-m-Y'))
//                     ->attachData($pdf->output(), 'Laporan Slip Gaji '.$pengerjaan_weekly->operator_karyawan->biodata_karyawan->nama.' '.date('d-m-Y').'.pdf');
//         });

//         if (\Mail::failures()) {
//             $value->update([
//                 'status' => 'gagal'
//             ]);
//         }else{
//             $value->update([
//                 'status' => 'terkirim'
//             ]);
//         }

//     }

// });

// Route::get('testMailHarian', function(){
//     $cek_kirim_slip_gajis = \App\Models\KirimGaji::where('kode_pengerjaan','LIKE','%PH_%')
//                                                 ->where('status','menunggu')
//                                                 ->limit(2)
//                                                 ->orderBy('id','asc')
//                                                 ->get();

//                                                 // dd($cek_kirim_slip_gajis);
//     foreach ($cek_kirim_slip_gajis as $key => $value) {
//         $data['explode_tanggal_pengerjaans'] = explode('#', $value->new_data_pengerjaan->tanggal);
//         $data['exp_tanggals'] = array_filter($data['explode_tanggal_pengerjaans']);
//         $data['a'] = count($data['exp_tanggals']);

//         $data['exp_tgl_awal'] = explode('-', $data['exp_tanggals'][1]);
//         $data['exp_tgl_akhir'] = explode('-', $data['exp_tanggals'][$data['a']]);

//         $data['pengerjaan_harian'] = \App\Models\PengerjaanHarian::where('kode_pengerjaan',$value->kode_pengerjaan)
//                                                                 ->where('id', $value->pengerjaan_id)
//                                                                 ->first();

//         if (empty($data['pengerjaan_harian']->lembur)) {
//             $data['hasil_lembur'] = 0;
//             $data['lembur_1'] = 0;
//             $data['lembur_2'] = 0;
//         }else{
//             $exlode_lembur = explode("|",$data['pengerjaan_harian']->lembur);
//             if (empty($exlode_lembur)) {
//                 $data['hasil_lembur'] = 0;
//                 $data['lembur_1'] = 0;
//                 $data['lembur_2'] = 0;
//             }else{
//                 $data['hasil_lembur'] = $exlode_lembur[0];
//                 $data['lembur_1'] = $exlode_lembur[1];
//                 $data['lembur_2'] = $exlode_lembur[2];
//             }
//         }

//         $data['total_jam_lembur'] = floatval($data['lembur_1'])+floatval($data['lembur_2']);

//         if (empty($data['pengerjaan_harian']->upah_dasar_weekly)) {
//             $data['upah_dasar_weekly'] = 0;
//         }else{
//             $data['upah_dasar_weekly'] = $data['pengerjaan_harian']->upah_dasar_weekly;
//         }

//         if($value->new_data_pengerjaan->akhir_bulan == 'y'){
//             if (empty($data['pengerjaan_harian']->tunjangan_kehadiran)) {
//                 $data['tunjangan_kehadiran'] = 0;
//             }else{
//                 $data['tunjangan_kehadiran'] = $data['pengerjaan_harian']->tunjangan_kehadiran;
//             }
//         }else{
//             $data['tunjangan_kehadiran'] = 0;
//         }

//         if ($value->new_data_pengerjaan->akhir_bulan == 'y') {
//             if (empty($data['pengerjaan_harian']->tunjangan_kerja)) {
//                 $data['tunjangan_kerja'] = 0;
//             }else{
//                 $data['tunjangan_kerja'] = $data['pengerjaan_harian']->tunjangan_kerja;
//             }
//         }else{
//             $data['tunjangan_kerja'] = 0;
//         }

//         if (empty($data['pengerjaan_harian']->uang_makan)) {
//             $data['uang_makan'] = 0;
//         }else{
//             $data['uang_makan'] = $data['pengerjaan_harian']->uang_makan;
//         }

//         if (empty($data['pengerjaan_harian']->plus_1)) {
//             $data['plus_1'] = 0;
//             $data['ket_plus_1'] = "";
//         }else{
//             $explode_plus_1 = explode("|",$data['pengerjaan_harian']->plus_1);
//             $data['plus_1'] = intval($explode_plus_1[0]);
//             $data['ket_plus_1'] = $explode_plus_1[1];
//         }

//         if (empty($data['pengerjaan_harian']->plus_2)) {
//             $data['plus_2'] = 0;
//             $data['ket_plus_2'] = "";
//         }else{
//             $explode_plus_2 = explode("|",$data['pengerjaan_harian']->plus_2);
//             $data['plus_2'] = intval($explode_plus_2[0]);
//             $data['ket_plus_2'] = $explode_plus_2[1];
//         }

//         if (empty($data['pengerjaan_harian']->plus_3)) {
//             $data['plus_3'] = 0;
//             $data['ket_plus_3'] = "";
//         }else{
//             $explode_plus_3 = explode("|",$data['pengerjaan_harian']->plus_3);
//             $data['plus_3'] = intval($explode_plus_3[0]);
//             $data['ket_plus_3'] = $explode_plus_3[1];
//         }

//         if (empty($data['pengerjaan_harian']->minus_1)) {
//             $data['minus_1'] = 0;
//             $data['ket_minus_1'] = "";
//         }else{
//             $explode_minus_1 = explode("|",$data['pengerjaan_harian']->minus_1);
//             if (empty($explode_minus_1[0])) {
//                 $data['minus_1'] = 0;
//             }else{
//                 $data['minus_1'] = intval($explode_minus_1[0]);
//             }
//             $data['ket_minus_1'] = $explode_minus_1[1];
//         }

//         if (empty($data['pengerjaan_harian']->minus_2)) {
//             $data['minus_2'] = 0;
//             $data['ket_minus_2'] = "";
//         }else{
//             $explode_minus_2 = explode("|",$data['pengerjaan_harian']->minus_2);
//             if (empty($explode_minus_2[0])) {
//                 $data['minus_2'] = 0;
//             }else{
//                 $data['minus_2'] = intval($explode_minus_2[0]);
//             }
//             $data['ket_minus_2'] = $explode_minus_2[1];
//         }
        
//         if (empty($data['pengerjaan_harian']->jht)) {
//             $data['jht'] = 0;
//         }else{
//             $data['jht'] = intval($data['pengerjaan_harian']->jht);
//         }

//         if (empty($data['pengerjaan_harian']->bpjs_kesehatan)) {
//             $data['bpjs_kesehatan'] = 0;
//         }else{
//             $data['bpjs_kesehatan'] = intval($data['pengerjaan_harian']->bpjs_kesehatan);
//         }

//         $data['total_gaji_diterima'] = ($data['pengerjaan_harian']->upah_dasar_weekly+$data['hasil_lembur']+$data['tunjangan_kehadiran']+$data['tunjangan_kerja']+
//                                         $data['plus_1']+$data['plus_2']+$data['plus_3']+$data['pengerjaan_harian']->uang_makan)-
//                                         ($data['jht']+$data['bpjs_kesehatan']+$data['minus_1']+$data['minus_2']);

//         $pdf = \Pdf::loadView('email.slipGajiOperatorHarian',$data);
//         $pdf->setPaper(array(0,0,560,380));  
//         $pdf->setEncryption('01101997','01101997');

//         \Mail::send('email.testingMailHarian',$data, function($message) use($data,$pdf){
//             $message->to('rioanugrah999@gmail.com')
//                     ->subject('Laporan Slip Gaji '.$data['pengerjaan_harian']->operator_karyawan->biodata_karyawan->nama.' '.date('d-m-Y'))
//                     ->attachData($pdf->output(), 'Laporan Slip Gaji '.$data['pengerjaan_harian']->operator_karyawan->biodata_karyawan->nama.' '.date('d-m-Y').'.pdf');
//         });

//         if (\Mail::failures()) {
//             $value->update([
//                 'status' => 'gagal'
//             ]);
//         }else{
//             $value->update([
//                 'status' => 'terkirim'
//             ]);
//         }
//     }
// });

// Route::get('testMailSupirRit', function(){
//     $cek_kirim_slip_gajis = \App\Models\KirimGaji::where('kode_pengerjaan','LIKE','%PS_%')
//                                                 ->where('status','menunggu')
//                                                 ->limit(2)
//                                                 ->orderBy('id','asc')
//                                                 ->get();

//                                                 // dd($cek_kirim_slip_gajis);

//     foreach ($cek_kirim_slip_gajis as $key => $value) {
//         $data['explode_tanggal_pengerjaans'] = explode('#', $value->new_data_pengerjaan->tanggal);
//         $data['exp_tanggals'] = array_filter($data['explode_tanggal_pengerjaans']);
//         $data['a'] = count($data['exp_tanggals']);

//         $data['exp_tgl_awal'] = explode('-', $data['exp_tanggals'][1]);
//         $data['exp_tgl_akhir'] = explode('-', $data['exp_tanggals'][$data['a']]);

//         $pengerjaan_rit_weekly = \App\Models\PengerjaanRITWeekly::where('kode_pengerjaan', $value->kode_pengerjaan)
//                                                                 ->where('id',$value->pengerjaan_id)
//                                                                 ->first();

//         $data['pengerjaan_rit_weekly'] = $pengerjaan_rit_weekly;
//         $data['kode_pengerjaan'] = $value->kode_pengerjaan;
        
//         $upah_dasar = array();

//         for ($i=0;$i<$data['a'];$i++) { 
//             $pengerjaan_rits = \App\Models\PengerjaanRITHarian::where('kode_pengerjaan',$value->new_data_pengerjaan->kode_pengerjaan)
//                                                             ->where('karyawan_supir_rit_id',$pengerjaan_rit_weekly->karyawan_supir_rit_id)
//                                                             ->get();

//             if (empty($pengerjaan_rits[$i]->hasil_kerja_1)) {
//                 $tanggal_pengerjaan = 0;
//                 $hasil_kerja_1 = 0;
//                 $hasil_umk_rit = 0;
//                 $tarif_umk = 0;
//                 $dpb = 0;
//                 $jenis_umk = '-';
//             }else{
//                 $data['tanggal_pengerjaan'] = \Carbon\Carbon::create($pengerjaan_rits[$i]->tanggal_pengerjaan)->isoFormat('D MMM');
//                 $explode_hasil_kerja_1 = explode("|",$pengerjaan_rits[$i]->hasil_kerja_1);
//                 $umk_rit = \App\Models\RitUMK::where('id',$explode_hasil_kerja_1[0])->first();
//                 if (empty($umk_rit)) {
//                     $hasil_kerja_1 = 0;
//                     $hasil_umk_rit = 0;
//                     $tarif_umk = 0;
//                     $dpb = 0;
//                     $jenis_umk = '-';
//                 }else{
//                     $hasil_kerja_1 = $umk_rit->tarif*$explode_hasil_kerja_1[1];
//                     $hasil_umk_rit = $umk_rit->kategori_upah;
//                     $tarif_umk = $umk_rit->tarif;
//                     $dpb = $pengerjaan_rits[$i]->dpb/7*$pengerjaan_rits[$i]->upah_dasar;
//                     if (empty($umk_rit->rit_tujuan)) {
//                         $jenis_umk = '-';
//                     }else{
//                         $jenis_umk = $umk_rit->rit_tujuan->tujuan.' - '.$umk_rit->rit_kendaraan->jenis_kendaraan;
//                     }
//                     $total_upah_dasar = $hasil_kerja_1+$dpb;
//                     array_push($upah_dasar,$total_upah_dasar);
//                 }
//             }

//         }

//         $data['hasil_upah_dasar'] = array_sum($upah_dasar);

//         if (empty($pengerjaan_rit_weekly->lembur)) {
//             $data['lembur_1'] = 0;
//             $data['lembur_2'] = 0;
//             $data['hasil_lembur'] = 0;
//         }else{
//             $explode_lembur = explode("|",$pengerjaan_rit_weekly->lembur);
//             $data['lembur_1'] = $explode_lembur[1];
//             $data['lembur_2'] = $explode_lembur[2];
//             $data['hasil_lembur'] = $explode_lembur[0];
//         }

//         $data['total_jam_lembur'] = floatval($data['lembur_1'])+floatval($data['lembur_2']);

//         if ($value->new_data_pengerjaan->akhir_bulan == 'y') {
//             if (empty($pengerjaan_rit_weekly->tunjangan_kehadiran)) {
//                 $data['tunjangan_kehadiran'] = 0;
//             }else{
//                 $data['tunjangan_kehadiran'] = $pengerjaan_rit_weekly->tunjangan_kehadiran;
//             }
//         }else{
//             $data['tunjangan_kehadiran'] = 0;
//         }

//         if ($value->new_data_pengerjaan->akhir_bulan == 'y') {
//             if (empty($pengerjaan_rit_weekly->tunjangan_kerja)) {
//                 $data['tunjangan_kerja'] = 0;
//             }else{
//                 $data['tunjangan_kerja'] = $pengerjaan_rit_weekly->tunjangan_kerja;
//             }
//         }else{
//             $data['tunjangan_kerja'] = 0;
//         }

//         if (empty($pengerjaan_rit_weekly->uang_makan)) {
//             $data['uang_makan'] = 0;
//         }else{
//             $data['uang_makan'] = $pengerjaan_rit_weekly->uang_makan;
//         }

//         if (empty($pengerjaan_rit_weekly->plus_1)) {
//             $data['plus_1'] = 0;
//             $data['keterangan_plus_1'] = '';
//         }else{
//             $explode_plus_1 = explode("|",$pengerjaan_rit_weekly->plus_1);
//             $data['plus_1'] = floatval($explode_plus_1[0]);
//             $data['keterangan_plus_1'] = $explode_plus_1[1];
//         }

//         if (empty($pengerjaan_rit_weekly->plus_2)) {
//             $data['plus_2'] = 0;
//             $data['keterangan_plus_2'] = '';
//         }else{
//             $explode_plus_2 = explode("|",$pengerjaan_rit_weekly->plus_2);
//             $data['plus_2'] = floatval($explode_plus_2[0]);
//             $data['keterangan_plus_2'] = $explode_plus_2[1];
//         }

//         if (empty($pengerjaan_rit_weekly->plus_3)) {
//             $data['plus_3'] = 0;
//             $data['keterangan_plus_3'] = '';
//         }else{
//             $explode_plus_3 = explode("|",$pengerjaan_rit_weekly->plus_3);
//             $data['plus_3'] = floatval($explode_plus_3[0]);
//             $data['keterangan_plus_3'] = $explode_plus_3[1];
//         }

//         $data['total_gaji'] = $data['hasil_upah_dasar']+$data['plus_1']+$data['plus_2']+$data['plus_3']+$data['uang_makan']+$data['hasil_lembur']+$data['tunjangan_kerja']+$data['tunjangan_kehadiran'];

//         if (empty($pengerjaan_rit_weekly->minus_1)) {
//             $data['minus_1'] = 0;
//             $data['keterangan_minus_1'] = '';
//         }else{
//             $explode_minus_1 = explode("|",$pengerjaan_rit_weekly->minus_1);
//             $data['minus_1'] = $explode_minus_1[0];
//             $data['keterangan_minus_1'] = $explode_minus_1[1];
//         }

//         if (empty($pengerjaan_rit_weekly->minus_2)) {
//             $data['minus_2'] = 0;
//             $data['keterangan_minus_2'] = '';
//         }else{
//             $explode_minus_2 = explode("|",$pengerjaan_rit_weekly->minus_2);
//             $data['minus_2'] = $explode_minus_2[0];
//             $data['keterangan_minus_2'] = $explode_minus_2[1];
//         }

//         if (empty($pengerjaan_rit_weekly->jht)) {
//             $data['jht'] = 0;
//         }else{
//             $data['jht'] = $pengerjaan_rit_weekly->jht;
//         }

//         if (empty($pengerjaan_rit_weekly->bpjs_kesehatan)) {
//             $data['bpjs_kesehatan'] = 0;
//         }else{
//             $data['bpjs_kesehatan'] = $pengerjaan_rit_weekly->bpjs_kesehatan;
//         }

//         if (empty($pengerjaan_rit_weekly->pensiun)) {
//             $data['pensiun'] = 0;
//         }else{
//             $data['pensiun'] = $pengerjaan_rit_weekly->pensiun;
//         }

//         $data['total_upah_diterima'] = $data['total_gaji']-$data['minus_1']-$data['minus_2']-$data['jht']-$data['bpjs_kesehatan']-$data['pensiun'];

//         $pdf = \Pdf::loadView('email.slipGajiOperatorSupirRit',$data);
//         $pdf->setPaper(array(0,0,400,500));   
//         $pdf->setEncryption('01101997','01101997');

//         \Mail::send('email.testingMailSupirRit',$data, function($message) use($data,$pdf,$pengerjaan_rit_weekly){
//             $message->to('rioanugrah999@gmail.com')
//                     ->subject('Laporan Slip Gaji '.$pengerjaan_rit_weekly->operator_supir_rit->biodata_karyawan->nama.' '.date('d-m-Y'))
//                     ->attachData($pdf->output(), 'Laporan Slip Gaji '.$pengerjaan_rit_weekly->operator_supir_rit->biodata_karyawan->nama.' '.date('d-m-Y').'.pdf');
//         });

//         if (\Mail::failures()) {
//             $value->update([
//                 'status' => 'gagal'
//             ]);
//         }else{
//             $value->update([
//                 'status' => 'terkirim'
//             ]);
//         }
//     }
// });
// Route::get('testing',function(){
//     $startPeriode = \Carbon\Carbon::create(2024,05,26)->format('Y-m-d');
//     $endPeriode = \Carbon\Carbon::create(2024,06,25)->format('Y-m-d');
//     $periode = \Carbon\CarbonPeriod::create($startPeriode,$endPeriode);
//     foreach ($periode as $key => $prd) {
//         $data[] = $prd->format('Y-m-d');
//     }
//     return $data;
// });

Route::get('testLog', function(){
    echo \Log::info('Test');
});