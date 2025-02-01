<style>
    * {
        font-family: Arial, Helvetica, sans-serif
    }

    table{
        border: 1px solid;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }
</style>
<?php
$new_data_pengerjaan = \App\Models\NewDataPengerjaan::where('kode_pengerjaan', $kode_pengerjaan)->first();
$explode_tanggal_pengerjaans = explode('#', $new_data_pengerjaan['tanggal']);
$exp_tanggals = array_filter($explode_tanggal_pengerjaans);
$a = count($exp_tanggals);
$exp_tgl_awal = explode('-', $exp_tanggals[1]);
$exp_tgl_akhir = explode('-', $exp_tanggals[$a]);

$pengerjaan_weekly = \App\Models\PengerjaanWeekly::where('kode_pengerjaan', $kode_pengerjaan)->where('id', $id)->first();
$pengerjaans = \App\Models\Pengerjaan::where('operator_karyawan_id', $pengerjaan_weekly->operator_karyawan_id)
    ->where('kode_pengerjaan', $kode_pengerjaan)
    ->get();
$total_upah_hasil_kerja = [];
$total_lembur_kerja = [];

foreach ($pengerjaans as $key => $pengerjaan) {
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

    array_push($total_upah_hasil_kerja, $total_hasil_kerja);
    array_push($total_lembur_kerja, $total_lembur);

    if ($new_data_pengerjaan['akhir_bulan'] == 'y') {
        if (empty($pengerjaan_weekly->tunjangan_kerja)) {
            $tunjangan_kerja = 0;
        } else {
            $tunjangan_kerja = $pengerjaan_weekly->tunjangan_kerja;
        }
    } else {
        $tunjangan_kerja = 0;
    }

    if (empty($pengerjaan_weekly->tunjangan_kehadiran)) {
        $tunjangan_kehadiran = 0;
    } else {
        $tunjangan_kehadiran = $pengerjaan_weekly->tunjangan_kehadiran;
    }

    if (empty($pengerjaan_weekly->uang_makan)) {
        $uang_makan = 0;
    } else {
        $uang_makan = $pengerjaan_weekly->uang_makan;
    }

    if (empty($pengerjaan_weekly->plus_1)) {
        $plus_1 = 0;
        $ket_plus_1 = null;
    } else {
        $explode_plus_1 = explode('|', $pengerjaan_weekly->plus_1);
        $plus_1 = floatval($explode_plus_1[0]);
        $ket_plus_1 = $explode_plus_1[1];
    }

    if (empty($pengerjaan_weekly->plus_2)) {
        $plus_2 = 0;
        $ket_plus_2 = null;
    } else {
        $explode_plus_2 = explode('|', $pengerjaan_weekly->plus_2);
        $plus_2 = floatval($explode_plus_2[0]);
        $ket_plus_2 = $explode_plus_2[1];
    }

    if (empty($pengerjaan_weekly->plus_3)) {
        $plus_3 = 0;
        $ket_plus_3 = null;
    } else {
        $explode_plus_3 = explode('|', $pengerjaan_weekly->plus_3);
        $plus_3 = floatval($explode_plus_3[0]);
        $ket_plus_3 = $explode_plus_3[1];
    }

    if (empty($pengerjaan_weekly->jht)) {
        $jht = 0;
    } else {
        $jht = $pengerjaan_weekly->jht;
    }

    if (empty($pengerjaan_weekly->bpjs_kesehatan)) {
        $bpjs_kesehatan = 0;
    } else {
        $bpjs_kesehatan = $pengerjaan_weekly->bpjs_kesehatan;
    }

    if (empty($pengerjaan_weekly->minus_1)) {
        $minus_1 = '0';
        $ket_minus_1 = null;
    } else {
        $explode_minus_1 = explode('|', $pengerjaan_weekly->minus_1);
        $minus_1 = floatval($explode_minus_1[0]);
        $ket_minus_1 = $explode_minus_1[1];
    }

    if (empty($pengerjaan_weekly->minus_2)) {
        $minus_2 = 0;
        $ket_minus_2 = null;
    } else {
        $explode_minus_2 = explode('|', $pengerjaan_weekly->minus_2);
        $minus_2 = floatval($explode_minus_2[0]);
        $ket_minus_2 = $explode_minus_2[1];
    }

    $total_gaji_diterima = (array_sum($total_upah_hasil_kerja) + array_sum($total_lembur_kerja) + $tunjangan_kerja + $tunjangan_kehadiran + $uang_makan + $plus_1 + $plus_2 + $plus_3) - ($jht + $bpjs_kesehatan + $minus_1 + $minus_2);
}
?>

<table>
    <tr>
        <td style="font-weight: bold">Tanggal Gaji</td>
        <td colspan="4">{{ \Carbon\Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' s/d '.\Carbon\Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY') }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold">Nama</td>
        <td colspan="4" style="font-weight: bold">{{ strtoupper($pengerjaan_weekly->operator_karyawan->biodata_karyawan->nama).' ('.$pengerjaan_weekly->operator_karyawan->biodata_karyawan->nik.')' }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold">Departemen</td>
        <td colspan="4" style="font-weight: bold">{{ strtoupper($pengerjaan_weekly->operator_karyawan->jenis_operator_detail_pengerjaan->jenis_posisi_pekerjaan) }}</td>
    </tr>
    <tr>
        <td>Gaji</td>
        <td>{{ $a.' HARI' }}</td>
        <td></td>
        <td>RP</td>
        <td style="text-align: right">{{ number_format(array_sum($total_upah_hasil_kerja),0,',','.') }}</td>
    </tr>
    <tr>
        <td>Lembur</td>
        <td></td>
        <td></td>
        <td>RP</td>
        <td style="text-align: right">{{ number_format(array_sum($total_lembur_kerja),0,',','.') }}</td>
    </tr>
    <tr>
        <td>Insentif Kehadiran</td>
        <td></td>
        <td></td>
        <td>RP</td>
        <td style="text-align: right">{{ number_format($tunjangan_kehadiran,0,',','.') }}</td>
    </tr>
    <tr>
        <td>Tunjangan Kerja</td>
        <td></td>
        <td></td>
        <td>RP</td>
        <td style="text-align: right">{{ number_format($tunjangan_kerja,0,',','.') }}</td>
    </tr>
    <tr>
        <td>Plus</td>
        <td>({{ $ket_plus_1 }})</td>
        <td></td>
        <td>RP</td>
        <td style="text-align: right">{{ number_format($plus_1,0,',','.') }}</td>
    </tr>
    <tr>
        <td></td>
        <td>({{ $ket_plus_2 }})</td>
        <td></td>
        <td>RP</td>
        <td style="text-align: right">{{ number_format($plus_2,0,',','.') }}</td>
    </tr>
    <tr>
        <td></td>
        <td>({{ $ket_plus_3 }})</td>
        <td></td>
        <td>RP</td>
        <td style="text-align: right">{{ number_format($plus_3,0,',','.') }}</td>
    </tr>
    <tr>
        <td>Uang Makan</td>
        <td></td>
        <td></td>
        <td>RP</td>
        <td style="text-align: right">{{ number_format($uang_makan,0,',','.') }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold">Potongan</td>
        <td style="font-weight: bold">BPJS > JHT + JP</td>
        <td></td>
        <td style="font-weight: bold">RP</td>
        <td style="text-align: right; font-weight: bold">{{ number_format($jht,0,',','.') }}</td>
    </tr>
    <tr>
        <td></td>
        <td style="font-weight: bold">BPJS Kesehatan</td>
        <td></td>
        <td style="font-weight: bold">RP</td>
        <td style="text-align: right; font-weight: bold">{{ number_format($bpjs_kesehatan,0,',','.') }}</td>
    </tr>
    <tr>
        <td></td>
        <td style="font-weight: bold">({{ $ket_minus_1 }})</td>
        <td></td>
        <td style="font-weight: bold">RP</td>
        <td style="text-align: right; font-weight: bold">{{ number_format($minus_1,0,',','.') }}</td>
    </tr>
    <tr>
        <td></td>
        <td style="font-weight: bold">({{ $ket_minus_2 }})</td>
        <td></td>
        <td style="font-weight: bold">RP</td>
        <td style="text-align: right; font-weight: bold">{{ number_format($minus_2,0,',','.') }}</td>
    </tr>
    <tr>
        <td colspan="2" style="font-weight: bold">Total Diterima</td>
        <td></td>
        <td style="font-weight: bold">RP</td>
        <td style="text-align: right; font-weight: bold">{{ number_format($total_gaji_diterima,0,',','.') }}</td>
    </tr>
</table>
<div style="margin-top: 5%">
    <strong style="font-size: 10pt;">Note:</strong>
    <div style="font-size: 10pt">Slip Gaji ini bersifat rahasia digunakan untuk pribadi dan tidak boleh diketahui oleh pihak lain.</div>
</div>