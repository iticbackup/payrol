<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\KirimGaji;
use App\Models\PengerjaanWeekly;
use App\Models\Pengerjaan;
use \Carbon\Carbon;

use Pdf;
use Mail;

class KirimSlipGajiBorongan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:kirimslipgajiborongan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim Slip Gaji Borongan Setelah Melakukan Close Period';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // return 0;
        $cek_kirim_slip_gajis = KirimGaji::where('kode_pengerjaan','LIKE','%PB_%')
                                        ->where('status','menunggu')
                                        ->limit(2)
                                        ->orderBy('id','asc')
                                        ->get();

        foreach ($cek_kirim_slip_gajis as $key => $value) {
            $data['explode_tanggal_pengerjaans'] = explode('#', $value->new_data_pengerjaan->tanggal);
            $data['exp_tanggals'] = array_filter($data['explode_tanggal_pengerjaans']);
            $data['a'] = count($data['exp_tanggals']);

            $data['exp_tgl_awal'] = explode('-', $data['exp_tanggals'][1]);
            $data['exp_tgl_akhir'] = explode('-', $data['exp_tanggals'][$data['a']]);

            $pengerjaan_weekly = PengerjaanWeekly::where('kode_pengerjaan', $value->kode_pengerjaan)
                                                ->where('id', $value->pengerjaan_id)
                                                ->first();

            $data['pengerjaans'] = Pengerjaan::where('operator_karyawan_id', $pengerjaan_weekly->operator_karyawan_id)
                                            ->where('kode_pengerjaan', $value->kode_pengerjaan)
                                            ->get();

            $data['tanggal'] = Carbon::parse($data['exp_tgl_awal'][0] . '-' . $data['exp_tgl_awal'][1] . '-' . $data['exp_tgl_awal'][2])->isoFormat('D MMMM').' sampai '.\Carbon\Carbon::parse($data['exp_tgl_akhir'][0] . '-' . $data['exp_tgl_akhir'][1] . '-' . $data['exp_tgl_akhir'][2])->isoFormat('D MMMM YYYY');

            $data['pengerjaan_weekly'] = $pengerjaan_weekly;

            $data['total_upah_hasil_kerja'] = [];
            $data['total_lembur_kerja'] = [];

            foreach ($data['pengerjaans'] as $key => $pengerjaan) {
                #Borongan Packing
                if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 1) {
                    for ($i = 1; $i <= 5; $i++) {
                        ${'explode_hasil_kerja_' . $i} = explode('|', $pengerjaan['hasil_kerja_' . $i]);
                        ${'umk_borongan_lokal_' . $i} = \App\Models\UMKBoronganLokal::where('id', ${'explode_hasil_kerja_' . $i}[0])->first();
                        if (empty(${'umk_borongan_lokal_' . $i})) {
                            ${'jenis_produk_' . $i} = '-';
                            ${'hasil_kerja_' . $i} = null;
                            ${'data_explode_hasil_kerja_' . $i} = '-';
                            ${'lembur_' . $i} = 1;
                            ${'total_hasil_' . $i} = 0;
                        } else {
                            ${'jenis_produk_' . $i} = ${'umk_borongan_lokal_' . $i}['jenis_produk'];
                            ${'hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1] * ${'umk_borongan_lokal_' . $i}['umk_packing'];
                            ${'data_explode_hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1];
                            ${'explode_lembur_' . $i} = explode('|', $pengerjaan['lembur']);
                            ${'explode_status_lembur_' . $i} = explode('-', ${'explode_lembur_' . $i}[$i]);
                            if (${'explode_status_lembur_' . $i}[1] == 'y') {
                                ${'lembur_' . $i} = 1.5;
                            } else {
                                ${'lembur_' . $i} = 1;
                            }
                        }
                    }
                }

                #Borongan Bandrol
                if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 2) {
                    for ($i = 1; $i <= 5; $i++) {
                        ${'explode_hasil_kerja_' . $i} = explode('|', $pengerjaan['hasil_kerja_' . $i]);
                        ${'umk_borongan_lokal_' . $i} = \App\Models\UMKBoronganLokal::where('id', ${'explode_hasil_kerja_' . $i}[0])->first();
                        if (empty(${'umk_borongan_lokal_' . $i})) {
                            ${'jenis_produk_' . $i} = '-';
                            ${'hasil_kerja_' . $i} = null;
                            ${'data_explode_hasil_kerja_' . $i} = '-';
                            ${'lembur_' . $i} = 1;
                            ${'total_hasil_' . $i} = 0;
                        } else {
                            ${'jenis_produk_' . $i} = ${'umk_borongan_lokal_' . $i}['jenis_produk'];
                            ${'hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1] * ${'umk_borongan_lokal_' . $i}['umk_bandrol'];
                            ${'data_explode_hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1];
                            ${'explode_lembur_' . $i} = explode('|', $pengerjaan['lembur']);
                            ${'explode_status_lembur_' . $i} = explode('-', ${'explode_lembur_' . $i}[$i]);
                            if (${'explode_status_lembur_' . $i}[1] == 'y') {
                                ${'lembur_' . $i} = 1.5;
                            } else {
                                ${'lembur_' . $i} = 1;
                            }
                        }
                    }
                }

                #Borongan Inner
                if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 3) {
                    for ($i = 1; $i <= 5; $i++) {
                        ${'explode_hasil_kerja_' . $i} = explode('|', $pengerjaan['hasil_kerja_' . $i]);
                        ${'umk_borongan_lokal_' . $i} = \App\Models\UMKBoronganLokal::where('id', ${'explode_hasil_kerja_' . $i}[0])->first();
                        if (empty(${'umk_borongan_lokal_' . $i})) {
                            ${'jenis_produk_' . $i} = '-';
                            ${'hasil_kerja_' . $i} = null;
                            ${'data_explode_hasil_kerja_' . $i} = '-';
                            ${'lembur_' . $i} = 1;
                            ${'total_hasil_' . $i} = 0;
                        } else {
                            ${'jenis_produk_' . $i} = ${'umk_borongan_lokal_' . $i}['jenis_produk'];
                            ${'hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1] * ${'umk_borongan_lokal_' . $i}['umk_inner'];
                            ${'data_explode_hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1];
                            ${'explode_lembur_' . $i} = explode('|', $pengerjaan['lembur']);
                            ${'explode_status_lembur_' . $i} = explode('-', ${'explode_lembur_' . $i}[$i]);
                            if (${'explode_status_lembur_' . $i}[1] == 'y') {
                                ${'lembur_' . $i} = 1.5;
                            } else {
                                ${'lembur_' . $i} = 1;
                            }
                        }
                    }
                }

                #Borongan Outer
                if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 4) {
                    for ($i = 1; $i <= 5; $i++) {
                        ${'explode_hasil_kerja_' . $i} = explode('|', $pengerjaan['hasil_kerja_' . $i]);
                        ${'umk_borongan_lokal_' . $i} = \App\Models\UMKBoronganLokal::where('id', ${'explode_hasil_kerja_' . $i}[0])->first();
                        if (empty(${'umk_borongan_lokal_' . $i})) {
                            ${'jenis_produk_' . $i} = '-';
                            ${'hasil_kerja_' . $i} = null;
                            ${'data_explode_hasil_kerja_' . $i} = '-';
                            ${'lembur_' . $i} = 1;
                            ${'total_hasil_' . $i} = 0;
                        } else {
                            ${'jenis_produk_' . $i} = ${'umk_borongan_lokal_' . $i}['jenis_produk'];
                            ${'hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1] * ${'umk_borongan_lokal_' . $i}['umk_outer'];
                            ${'data_explode_hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1];
                            ${'explode_lembur_' . $i} = explode('|', $pengerjaan['lembur']);
                            ${'explode_status_lembur_' . $i} = explode('-', ${'explode_lembur_' . $i}[$i]);
                            if (${'explode_status_lembur_' . $i}[1] == 'y') {
                                ${'lembur_' . $i} = 1.5;
                            } else {
                                ${'lembur_' . $i} = 1;
                            }
                        }
                    }
                }

                #Borongan Stempel Lokal
                if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 25) {
                    for ($i = 1; $i <= 5; $i++) {
                        ${'explode_hasil_kerja_' . $i} = explode('|', $pengerjaan['hasil_kerja_' . $i]);
                        ${'umk_borongan_lokal_' . $i} = \App\Models\UMKBoronganStempel::where('id', ${'explode_hasil_kerja_' . $i}[0])->first();
                        if (empty(${'umk_borongan_lokal_' . $i})) {
                            ${'jenis_produk_' . $i} = '-';
                            ${'hasil_kerja_' . $i} = null;
                            ${'data_explode_hasil_kerja_' . $i} = '-';
                            ${'lembur_' . $i} = 1;
                            ${'total_hasil_' . $i} = 0;
                        } else {
                            ${'jenis_produk_' . $i} = ${'umk_borongan_lokal_' . $i}['jenis_produk'];
                            ${'hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1] * ${'umk_borongan_lokal_' . $i}['nominal_umk'];
                            ${'data_explode_hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1];
                            ${'explode_lembur_' . $i} = explode('|', $pengerjaan['lembur']);
                            ${'explode_status_lembur_' . $i} = explode('-', ${'explode_lembur_' . $i}[$i]);
                            if (${'explode_status_lembur_' . $i}[1] == 'y') {
                                ${'lembur_' . $i} = 1.5;
                            } else {
                                ${'lembur_' . $i} = 1;
                            }
                        }
                    }
                }

                #Borongan Ekspor Packing
                if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 5) {
                    for ($i = 1; $i <= 5; $i++) {
                        ${'explode_hasil_kerja_' . $i} = explode('|', $pengerjaan['hasil_kerja_' . $i]);
                        ${'umk_borongan_lokal_' . $i} = \App\Models\UMKBoronganEkspor::where('id', ${'explode_hasil_kerja_' . $i}[0])->first();
                        if (empty(${'umk_borongan_lokal_' . $i})) {
                            ${'jenis_produk_' . $i} = '-';
                            ${'hasil_kerja_' . $i} = null;
                            ${'data_explode_hasil_kerja_' . $i} = '-';
                            ${'lembur_' . $i} = 1;
                            ${'total_hasil_' . $i} = 0;
                        } else {
                            ${'jenis_produk_' . $i} = ${'umk_borongan_lokal_' . $i}['jenis_produk'];
                            ${'hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1] * ${'umk_borongan_lokal_' . $i}['umk_packing'];
                            ${'data_explode_hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1];
                            ${'explode_lembur_' . $i} = explode('|', $pengerjaan['lembur']);
                            ${'explode_status_lembur_' . $i} = explode('-', ${'explode_lembur_' . $i}[$i]);
                            if (${'explode_status_lembur_' . $i}[1] == 'y') {
                                ${'lembur_' . $i} = 1.5;
                            } else {
                                ${'lembur_' . $i} = 1;
                            }
                        }
                    }
                }

                #Borongan Ekspor Kemas
                if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 6) {
                    for ($i = 1; $i <= 5; $i++) {
                        ${'explode_hasil_kerja_' . $i} = explode('|', $pengerjaan['hasil_kerja_' . $i]);
                        ${'umk_borongan_lokal_' . $i} = \App\Models\UMKBoronganEkspor::where('id', ${'explode_hasil_kerja_' . $i}[0])->first();
                        if (empty(${'umk_borongan_lokal_' . $i})) {
                            ${'jenis_produk_' . $i} = '-';
                            ${'hasil_kerja_' . $i} = null;
                            ${'data_explode_hasil_kerja_' . $i} = '-';
                            ${'lembur_' . $i} = 1;
                            ${'total_hasil_' . $i} = 0;
                        } else {
                            ${'jenis_produk_' . $i} = ${'umk_borongan_lokal_' . $i}['jenis_produk'];
                            ${'hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1] * ${'umk_borongan_lokal_' . $i}['umk_kemas'];
                            ${'data_explode_hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1];
                            ${'explode_lembur_' . $i} = explode('|', $pengerjaan['lembur']);
                            ${'explode_status_lembur_' . $i} = explode('-', ${'explode_lembur_' . $i}[$i]);
                            if (${'explode_status_lembur_' . $i}[1] == 'y') {
                                ${'lembur_' . $i} = 1.5;
                            } else {
                                ${'lembur_' . $i} = 1;
                            }
                        }
                    }
                }

                #Borongan Ekspor Gagang
                if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 7) {
                    for ($i = 1; $i <= 5; $i++) {
                        ${'explode_hasil_kerja_' . $i} = explode('|', $pengerjaan['hasil_kerja_' . $i]);
                        ${'umk_borongan_lokal_' . $i} = \App\Models\UMKBoronganEkspor::where('id', ${'explode_hasil_kerja_' . $i}[0])->first();
                        if (empty(${'umk_borongan_lokal_' . $i})) {
                            ${'jenis_produk_' . $i} = '-';
                            ${'hasil_kerja_' . $i} = null;
                            ${'data_explode_hasil_kerja_' . $i} = '-';
                            ${'lembur_' . $i} = 1;
                            ${'total_hasil_' . $i} = 0;
                        } else {
                            ${'jenis_produk_' . $i} = ${'umk_borongan_lokal_' . $i}['jenis_produk'];
                            ${'hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1] * ${'umk_borongan_lokal_' . $i}['umk_pilih_gagang'];
                            ${'data_explode_hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1];
                            ${'explode_lembur_' . $i} = explode('|', $pengerjaan['lembur']);
                            ${'explode_status_lembur_' . $i} = explode('-', ${'explode_lembur_' . $i}[$i]);
                            if (${'explode_status_lembur_' . $i}[1] == 'y') {
                                ${'lembur_' . $i} = 1.5;
                            } else {
                                ${'lembur_' . $i} = 1;
                            }
                        }
                    }
                }

                #Borongan Ambri Isi Etiket
                if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 8) {
                    for ($i = 1; $i <= 5; $i++) {
                        ${'explode_hasil_kerja_' . $i} = explode('|', $pengerjaan['hasil_kerja_' . $i]);
                        ${'umk_borongan_lokal_' . $i} = \App\Models\UMKBoronganAmbri::where('id', ${'explode_hasil_kerja_' . $i}[0])->first();
                        if (empty(${'umk_borongan_lokal_' . $i})) {
                            ${'jenis_produk_' . $i} = '-';
                            ${'hasil_kerja_' . $i} = null;
                            ${'data_explode_hasil_kerja_' . $i} = '-';
                            ${'lembur_' . $i} = 1;
                            ${'total_hasil_' . $i} = 0;
                        } else {
                            ${'jenis_produk_' . $i} = ${'umk_borongan_lokal_' . $i}['jenis_produk'];
                            ${'hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1] * ${'umk_borongan_lokal_' . $i}['umk_etiket'];
                            ${'data_explode_hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1];
                            ${'explode_lembur_' . $i} = explode('|', $pengerjaan['lembur']);
                            ${'explode_status_lembur_' . $i} = explode('-', ${'explode_lembur_' . $i}[$i]);
                            if (${'explode_status_lembur_' . $i}[1] == 'y') {
                                ${'lembur_' . $i} = 1.5;
                            } else {
                                ${'lembur_' . $i} = 1;
                            }
                        }
                    }
                }

                #Borongan Ambri Las Tepi
                if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 9) {
                    for ($i = 1; $i <= 5; $i++) {
                        ${'explode_hasil_kerja_' . $i} = explode('|', $pengerjaan['hasil_kerja_' . $i]);
                        ${'umk_borongan_lokal_' . $i} = \App\Models\UMKBoronganAmbri::where('id', ${'explode_hasil_kerja_' . $i}[0])->first();
                        if (empty(${'umk_borongan_lokal_' . $i})) {
                            ${'jenis_produk_' . $i} = '-';
                            ${'hasil_kerja_' . $i} = null;
                            ${'data_explode_hasil_kerja_' . $i} = '-';
                            ${'lembur_' . $i} = 1;
                            ${'total_hasil_' . $i} = 0;
                        } else {
                            ${'jenis_produk_' . $i} = ${'umk_borongan_lokal_' . $i}['jenis_produk'];
                            ${'hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1] * ${'umk_borongan_lokal_' . $i}['umk_las_tepi'];
                            ${'data_explode_hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1];
                            ${'explode_lembur_' . $i} = explode('|', $pengerjaan['lembur']);
                            ${'explode_status_lembur_' . $i} = explode('-', ${'explode_lembur_' . $i}[$i]);
                            if (${'explode_status_lembur_' . $i}[1] == 'y') {
                                ${'lembur_' . $i} = 1.5;
                            } else {
                                ${'lembur_' . $i} = 1;
                            }
                        }
                    }
                }

                #Borongan Ambri Isi Ambri
                if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 11) {
                    for ($i = 1; $i <= 5; $i++) {
                        ${'explode_hasil_kerja_' . $i} = explode('|', $pengerjaan['hasil_kerja_' . $i]);
                        ${'umk_borongan_lokal_' . $i} = \App\Models\UMKBoronganAmbri::where('id', ${'explode_hasil_kerja_' . $i}[0])->first();
                        if (empty(${'umk_borongan_lokal_' . $i})) {
                            ${'jenis_produk_' . $i} = '-';
                            ${'hasil_kerja_' . $i} = null;
                            ${'data_explode_hasil_kerja_' . $i} = '-';
                            ${'lembur_' . $i} = 1;
                            ${'total_hasil_' . $i} = 0;
                        } else {
                            ${'jenis_produk_' . $i} = ${'umk_borongan_lokal_' . $i}['jenis_produk'];
                            ${'hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1] * ${'umk_borongan_lokal_' . $i}['umk_ambri'];
                            ${'data_explode_hasil_kerja_' . $i} = ${'explode_hasil_kerja_' . $i}[1];
                            ${'explode_lembur_' . $i} = explode('|', $pengerjaan['lembur']);
                            ${'explode_status_lembur_' . $i} = explode('-', ${'explode_lembur_' . $i}[$i]);
                            if (${'explode_status_lembur_' . $i}[1] == 'y') {
                                ${'lembur_' . $i} = 1.5;
                            } else {
                                ${'lembur_' . $i} = 1;
                            }
                        }
                    }
                }
                
                $total_hasil_kerja = round($hasil_kerja_1 * $lembur_1 + $hasil_kerja_2 * $lembur_2 + $hasil_kerja_3 * $lembur_3 + $hasil_kerja_4 * $lembur_4 + $hasil_kerja_5 * $lembur_5) - $pengerjaan['uang_lembur'];
                $total_lembur = $pengerjaan['uang_lembur'];

                array_push($data['total_upah_hasil_kerja'], $total_hasil_kerja);
                array_push($data['total_lembur_kerja'], $total_lembur);

                if ($value->new_data_pengerjaan->akhir_bulan == 'y') {
                    if (empty($value->tunjangan_kerja)) {
                        $data['tunjangan_kerja'] = 0;
                    } else {
                        $data['tunjangan_kerja'] = $value->tunjangan_kerja;
                    }
                } else {
                    $data['tunjangan_kerja'] = 0;
                }

                if (empty($value->tunjangan_kehadiran)) {
                    $data['tunjangan_kehadiran'] = 0;
                } else {
                    $data['tunjangan_kehadiran'] = $value->tunjangan_kehadiran;
                }

                if (empty($value->uang_makan)) {
                    $data['uang_makan'] = 0;
                } else {
                    $data['uang_makan'] = $value->uang_makan;
                }

                if (empty($value->plus_1)) {
                    $data['plus_1'] = 0;
                    $data['ket_plus_1'] = null;
                } else {
                    $explode_plus_1 = explode('|', $value->plus_1);
                    $data['plus_1'] = floatval($explode_plus_1[0]);
                    $data['ket_plus_1'] = $explode_plus_1[1];
                }

                if (empty($value->plus_2)) {
                    $data['plus_2'] = 0;
                    $data['ket_plus_2'] = null;
                } else {
                    $explode_plus_2 = explode('|', $value->plus_2);
                    $data['plus_2'] = floatval($explode_plus_2[0]);
                    $data['ket_plus_2'] = $explode_plus_2[1];
                }

                if (empty($value->plus_3)) {
                    $data['plus_3'] = 0;
                    $data['ket_plus_3'] = null;
                } else {
                    $explode_plus_3 = explode('|', $value->plus_3);
                    $data['plus_3'] = floatval($explode_plus_3[0]);
                    $data['ket_plus_3'] = $explode_plus_3[1];
                }

                if (empty($value->jht)) {
                    $data['jht'] = 0;
                } else {
                    $data['jht'] = $value->jht;
                }

                if (empty($value->bpjs_kesehatan)) {
                    $data['bpjs_kesehatan'] = 0;
                } else {
                    $data['bpjs_kesehatan'] = $value->bpjs_kesehatan;
                }

                if (empty($value->minus_1)) {
                    $data['minus_1'] = '0';
                    $data['ket_minus_1'] = null;
                } else {
                    $explode_minus_1 = explode('|', $value->minus_1);
                    $data['minus_1'] = floatval($explode_minus_1[0]);
                    $data['ket_minus_1'] = $explode_minus_1[1];
                }

                if (empty($value->minus_2)) {
                    $data['minus_2'] = 0;
                    $data['ket_minus_2'] = null;
                } else {
                    $explode_minus_2 = explode('|', $value->minus_2);
                    $data['minus_2'] = floatval($explode_minus_2[0]);
                    $data['ket_minus_2'] = $explode_minus_2[1];
                }

                $data['total_gaji_diterima'] = (array_sum($data['total_upah_hasil_kerja']) + array_sum($data['total_lembur_kerja']) + $data['tunjangan_kerja'] + $data['tunjangan_kehadiran'] + $data['uang_makan'] + $data['plus_1'] + $data['plus_2'] + $data['plus_3']) - ($data['jht'] + $data['bpjs_kesehatan'] + $data['minus_1'] + $data['minus_2']);
            }

            $pdf = Pdf::loadView('email.slipGajiOperatorBorongan',$data);
            $pdf->setPaper(array(0,0,560,380));
            $pdf->setEncryption(Carbon::create($pengerjaan_weekly->operator_karyawan->biodata_karyawan->tgl_lahir)->format('dmY'),Carbon::create($pengerjaan_weekly->operator_karyawan->biodata_karyawan->tgl_lahir)->format('dmY'));

            Mail::send('email.testingMailBorongan',$data, function($message) use($data,$pdf,$pengerjaan_weekly){
                $message->to(strtolower($pengerjaan_weekly->operator_karyawan->biodata_karyawan->email))
                        ->subject('Laporan Slip Gaji '.$pengerjaan_weekly->operator_karyawan->biodata_karyawan->nama.' '.date('d-m-Y'))
                        ->attachData($pdf->output(), 'Laporan Slip Gaji '.$pengerjaan_weekly->operator_karyawan->biodata_karyawan->nama.' '.date('d-m-Y').'.pdf');
            });

            if (Mail::failures()) {
                $value->update([
                    'status' => 'gagal'
                ]);

                \Log::error($value->kode_pengerjaan.' '.$value->nama_karyawan.' Gagal Kirim Email');

            }else{
                $value->update([
                    'status' => 'terkirim'
                ]);

                \Log::info($value->kode_pengerjaan.' '.$value->nama_karyawan.' Terkirim');
            }

        }
    }
}
