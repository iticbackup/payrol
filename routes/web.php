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
                Route::get('{id}/{kode_pengerjaan}/{tanggal}/input_hasil', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_packing_view_hasil'])->name('hasil_kerja.packingLokal.view_hasil');
                Route::post('{id}/{kode_pengerjaan}/{tanggal}/input_hasil/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_packing_view_simpan'])->name('hasil_kerja.packingLokal.view_hasil.simpan');
                
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
            Route::prefix('marketing')->group(function () {
                Route::get('{id}/{kode_pengerjaan}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_marketing'])->name('hasil_kerja.marketing');
                Route::get('{id}/{kode_pengerjaan}/{nik}/{month}/{year}/input_hasil', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_marketing_view'])->name('hasil_kerja.marketing.view');
                Route::post('{id}/{kode_pengerjaan}/{nik}/{month}/{year}/input_hasil/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_marketing_simpan'])->name('hasil_kerja.marketing.simpan');
            });
            Route::prefix('ppic_tembakau')->group(function () {
                Route::get('{id}/{kode_pengerjaan}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_ppic_tembakau'])->name('hasil_kerja.ppicTembakau');
                Route::get('{id}/{kode_pengerjaan}/{nik}/{month}/{year}/input_hasil', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_ppic_tembakau_view'])->name('hasil_kerja.ppicTembakau.view');
                Route::post('{id}/{kode_pengerjaan}/{nik}/{month}/{year}/input_hasil/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_ppic_tembakau_simpan'])->name('hasil_kerja.ppicTembakau.simpan');
                // Route::get('/', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_ppic_tembakau'])->name('hasil_kerja.ppicTembakau');
            });
            Route::prefix('primary_process')->group(function () {
                // Route::get('/', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_primary_process'])->name('hasil_kerja.primaryProcess');
                Route::get('{id}/{kode_pengerjaan}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_primary_process'])->name('hasil_kerja.primaryProcess');
                Route::get('{id}/{kode_pengerjaan}/{nik}/{month}/{year}/input_hasil', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_primary_process_view'])->name('hasil_kerja.primaryProcess.view');
                Route::post('{id}/{kode_pengerjaan}/{nik}/{month}/{year}/input_hasil/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_primary_process_simpan'])->name('hasil_kerja.primaryProcess.simpan');
            });
            Route::prefix('packing_b')->group(function () {
                // Route::get('/', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_packingB'])->name('hasil_kerja.packingB');
                Route::get('{id}/{kode_pengerjaan}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_packing_b'])->name('hasil_kerja.packingB');
                Route::get('{id}/{kode_pengerjaan}/{nik}/{month}/{year}/input_hasil', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_packing_b_view'])->name('hasil_kerja.packingB.view');
                Route::post('{id}/{kode_pengerjaan}/{nik}/{month}/{year}/input_hasil/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_packing_b_simpan'])->name('hasil_kerja.packingB.simpan');
            });
            Route::prefix('ambri')->group(function () {
                // Route::get('/', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_ambri'])->name('hasil_kerja.ambri');
                Route::get('{id}/{kode_pengerjaan}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_ambri'])->name('hasil_kerja.ambri');
                Route::get('{id}/{kode_pengerjaan}/{nik}/{month}/{year}/input_hasil', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_ambri_view'])->name('hasil_kerja.ambri.view');
                Route::post('{id}/{kode_pengerjaan}/{nik}/{month}/{year}/input_hasil/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_ambri_simpan'])->name('hasil_kerja.ambri.simpan');
            });
            Route::prefix('umum')->group(function () {
                // Route::get('/', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_umum'])->name('hasil_kerja.umum');
                Route::get('{id}/{kode_pengerjaan}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_umum'])->name('hasil_kerja.umum');
                Route::get('{id}/{kode_pengerjaan}/{nik}/{month}/{year}/input_hasil', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_umum_view'])->name('hasil_kerja.umum.view');
                Route::post('{id}/{kode_pengerjaan}/{nik}/{month}/{year}/input_hasil/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_umum_simpan'])->name('hasil_kerja.umum.simpan');
            });
            Route::prefix('supir')->group(function () {
                // Route::get('/', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_supir'])->name('hasil_kerja.supir');
                Route::get('{id}/{kode_pengerjaan}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_supir'])->name('hasil_kerja.supir');
                Route::get('{id}/{kode_pengerjaan}/{nik}/{month}/{year}/input_hasil', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_supir_view'])->name('hasil_kerja.supir.view');
                Route::post('{id}/{kode_pengerjaan}/{nik}/{month}/{year}/input_hasil/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_supir_simpan'])->name('hasil_kerja.supir.simpan');
            });
            Route::prefix('satpam')->group(function () {
                // Route::get('/', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_satpam'])->name('hasil_kerja.satpam');
                Route::get('{id}/{kode_pengerjaan}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_satpam'])->name('hasil_kerja.satpam');
                Route::get('{id}/{kode_pengerjaan}/{nik}/{month}/{year}/input_hasil', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_satpam_view'])->name('hasil_kerja.satpam.view');
                Route::post('{id}/{kode_pengerjaan}/{nik}/{month}/{year}/input_hasil/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_harian_satpam_simpan'])->name('hasil_kerja.satpam.simpan');
            });
            Route::prefix('supir_rit')->group(function () {
                Route::get('{kode_pengerjaan}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_supir_rit'])->name('hasil_kerja.supir_rit');
                Route::get('{kode_pengerjaan}/{tanggal}/input', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_supir_rit_input'])->name('hasil_kerja.supir_rit.input');
                Route::post('{kode_pengerjaan}/{tanggal}/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_supir_rit_simpan'])->name('hasil_kerja.supir_rit.simpan');

                Route::get('{kode_pengerjaan}/{nik}/input_hasil_karyawan/{month}/{year}', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_supir_rit_input_karyawan'])->name('hasil_kerja.supir_rit.view_hasil_karyawan');
                Route::post('{kode_pengerjaan}/{nik}/input_hasil_karyawan/{month}/{year}/simpan', [App\Http\Controllers\PengerjaanController::class, 'hasil_kerja_supir_rit_input_karyawan_simpan'])->name('hasil_kerja.supir_rit.view_hasil_karyawan.simpan');

            });
        });
    });

    Route::prefix('laporan')->group(function () {
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
    });

    Route::prefix('payrol')->group(function () {
        Route::prefix('borongan')->group(function () {
            Route::get('/', [App\Http\Controllers\PayrolController::class, 'borongan'])->name('payrol.borongan');
            Route::get('{kode_pengerjaan}/slip_gaji', [App\Http\Controllers\PayrolController::class, 'borongan_slip_gaji'])->name('payrol.borongan.slip_gaji');
            Route::get('{kode_pengerjaan}/slip_gaji/view', [App\Http\Controllers\PayrolController::class, 'borongan_slip_gaji_view'])->name('payrol.borongan.slip_gaji.view');
            Route::get('{kode_pengerjaan}/bank', [App\Http\Controllers\PayrolController::class, 'borongan_bank'])->name('payrol.borongan.bank');
            Route::get('{kode_pengerjaan}/report', [App\Http\Controllers\PayrolController::class, 'borongan_weekly_report'])->name('payrol.borongan.weekly_report');
            Route::get('{kode_pengerjaan}/detail_kirim_email', [App\Http\Controllers\PayrolController::class, 'borongan_detail_kirim_slip_gaji'])->name('payrol.borongan.borongan_detail_kirim_slip_gaji');
            Route::post('{kode_pengerjaan}/detail_kirim_email/kirim_gaji', [App\Http\Controllers\PayrolController::class, 'borongan_kirim_slip_gaji'])->name('payrol.borongan.borongan_kirim_slip_gaji');
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
            Route::get('{kode_pengerjaan}/detail_kirim_email', [App\Http\Controllers\PayrolController::class, 'harian_detail_kirim_slip_gaji'])->name('payrol.harian.harian_detail_kirim_slip_gaji');
            Route::post('{kode_pengerjaan}/detail_kirim_email/kirim_gaji', [App\Http\Controllers\PayrolController::class, 'harian_kirim_slip_gaji'])->name('payrol.harian.harian_kirim_slip_gaji');
            Route::get('{kode_pengerjaan}/detail_kirim_email/cek_kirim_gaji', [App\Http\Controllers\PayrolController::class, 'harian_cek_email_slip_gaji'])->name('payrol.harian.harian_cek_email_slip_gaji');
            Route::get('{kode_pengerjaan}/detail_kirim_email/{id}', [App\Http\Controllers\PayrolController::class, 'harian_cek_slip_gaji'])->name('payrol.harian.harian_cek_slip_gaji');
            Route::get('{kode_pengerjaan}/detail_kirim_email/{id}/kirim_ulang', [App\Http\Controllers\PayrolController::class, 'harian_cek_email_kirim_ulang'])->name('payrol.harian.harian_cek_email_kirim_ulang');

        });
        Route::prefix('supir_rit')->group(function () {
            Route::get('/', [App\Http\Controllers\PayrolController::class, 'supir_rit'])->name('payrol.supir_rit');
            Route::get('{kode_pengerjaan}/slip_gaji', [App\Http\Controllers\PayrolController::class, 'supir_rit_slip_gaji'])->name('payrol.supir_rit.slip_gaji');
            Route::get('{kode_pengerjaan}/bank', [App\Http\Controllers\PayrolController::class, 'supir_rit_bank'])->name('payrol.supir_rit.bank');
            Route::get('{kode_pengerjaan}/report', [App\Http\Controllers\PayrolController::class, 'supir_rit_weekly_report'])->name('payrol.supir_rit.weekly_report');
            Route::get('{kode_pengerjaan}/detail_kirim_email', [App\Http\Controllers\PayrolController::class, 'supir_rit_detail_kirim_slip_gaji'])->name('payrol.supir_rit.supir_rit_detail_kirim_slip_gaji');
            Route::post('{kode_pengerjaan}/detail_kirim_email/kirim_gaji', [App\Http\Controllers\PayrolController::class, 'supir_rit_kirim_slip_gaji'])->name('payrol.supir_rit.supir_rit_kirim_slip_gaji');
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

    Route::prefix('jenis_umk_borongan')->group(function () {
        Route::prefix('lokal')->group(function () {
            Route::get('/', [App\Http\Controllers\JenisUMKBoronganController::class, 'lokal'])->name('jenis_umk_borongan.lokal');
            Route::post('simpan', [App\Http\Controllers\JenisUMKBoronganController::class, 'lokal_simpan'])->name('jenis_umk_borongan.lokal.simpan');
            Route::get('{id}', [App\Http\Controllers\JenisUMKBoronganController::class, 'lokal_detail'])->name('jenis_umk_borongan.lokal.detail');
            Route::post('update', [App\Http\Controllers\JenisUMKBoronganController::class, 'lokal_update'])->name('jenis_umk_borongan.lokal.update');
            Route::get('{id}/delete', [App\Http\Controllers\JenisUMKBoronganController::class, 'lokal_delete'])->name('jenis_umk_borongan.lokal.delete');
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

    Route::get('testing', [App\Http\Controllers\TestingController::class, 'testing'])->name('testing');
    Route::get('new_testing/month?month={id}', function($id){
        return $id;
    });
});

// Route::get('testing',function(){
//     $startPeriode = \Carbon\Carbon::create(2024,05,26)->format('Y-m-d');
//     $endPeriode = \Carbon\Carbon::create(2024,06,25)->format('Y-m-d');
//     $periode = \Carbon\CarbonPeriod::create($startPeriode,$endPeriode);
//     foreach ($periode as $key => $prd) {
//         $data[] = $prd->format('Y-m-d');
//     }
//     return $data;
// });
