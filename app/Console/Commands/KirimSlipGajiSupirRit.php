<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\KirimGaji;
use App\Models\PengerjaanRITWeekly;
use App\Models\PengerjaanRITHarian;

use \Carbon\Carbon;

use Pdf;
use Mail;

class KirimSlipGajiSupirRit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:kirimslipgajisupirit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim Slip Gaji Supir RIT Setelah Melakukan Close Period';

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
        $cek_kirim_slip_gajis = KirimGaji::where('kode_pengerjaan','LIKE','%PS_%')
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

            $pengerjaan_rit_weekly = PengerjaanRITWeekly::where('kode_pengerjaan', $value->kode_pengerjaan)
                                                        ->where('id',$value->pengerjaan_id)
                                                        ->first();

            $data['pengerjaan_rit_weekly'] = $pengerjaan_rit_weekly;
            $data['kode_pengerjaan'] = $value->kode_pengerjaan;
            
            $upah_dasar = array();

            for ($i=0;$i<$data['a'];$i++) { 
                $pengerjaan_rits = \App\Models\PengerjaanRITHarian::where('kode_pengerjaan',$value->new_data_pengerjaan->kode_pengerjaan)
                                                                ->where('karyawan_supir_rit_id',$pengerjaan_rit_weekly->karyawan_supir_rit_id)
                                                                ->get();

                if (empty($pengerjaan_rits[$i]->hasil_kerja_1)) {
                    $tanggal_pengerjaan = 0;
                    $hasil_kerja_1 = 0;
                    $hasil_umk_rit = 0;
                    $tarif_umk = 0;
                    $dpb = 0;
                    $jenis_umk = '-';
                }else{
                    $data['tanggal_pengerjaan'] = \Carbon\Carbon::create($pengerjaan_rits[$i]->tanggal_pengerjaan)->isoFormat('D MMM');
                    $explode_hasil_kerja_1 = explode("|",$pengerjaan_rits[$i]->hasil_kerja_1);
                    $umk_rit = \App\Models\RitUMK::where('id',$explode_hasil_kerja_1[0])->first();
                    if (empty($umk_rit)) {
                        $hasil_kerja_1 = 0;
                        $hasil_umk_rit = 0;
                        $tarif_umk = 0;
                        $dpb = 0;
                        $jenis_umk = '-';
                    }else{
                        $hasil_kerja_1 = $umk_rit->tarif*$explode_hasil_kerja_1[1];
                        $hasil_umk_rit = $umk_rit->kategori_upah;
                        $tarif_umk = $umk_rit->tarif;
                        $dpb = $pengerjaan_rits[$i]->dpb/7*$pengerjaan_rits[$i]->upah_dasar;
                        if (empty($umk_rit->rit_tujuan)) {
                            $jenis_umk = '-';
                        }else{
                            $jenis_umk = $umk_rit->rit_tujuan->tujuan.' - '.$umk_rit->rit_kendaraan->jenis_kendaraan;
                        }
                        $total_upah_dasar = $hasil_kerja_1+$dpb;
                        array_push($upah_dasar,$total_upah_dasar);
                    }
                }

            }

            $data['hasil_upah_dasar'] = array_sum($upah_dasar);

            if (empty($pengerjaan_rit_weekly->lembur)) {
                $data['lembur_1'] = 0;
                $data['lembur_2'] = 0;
                $data['hasil_lembur'] = 0;
            }else{
                $explode_lembur = explode("|",$pengerjaan_rit_weekly->lembur);
                $data['lembur_1'] = $explode_lembur[1];
                $data['lembur_2'] = $explode_lembur[2];
                $data['hasil_lembur'] = $explode_lembur[0];
            }

            $data['total_jam_lembur'] = floatval($data['lembur_1'])+floatval($data['lembur_2']);

            if ($value->new_data_pengerjaan->akhir_bulan == 'y') {
                if (empty($pengerjaan_rit_weekly->tunjangan_kehadiran)) {
                    $data['tunjangan_kehadiran'] = 0;
                }else{
                    $data['tunjangan_kehadiran'] = $pengerjaan_rit_weekly->tunjangan_kehadiran;
                }
            }else{
                $data['tunjangan_kehadiran'] = 0;
            }

            if ($value->new_data_pengerjaan->akhir_bulan == 'y') {
                if (empty($pengerjaan_rit_weekly->tunjangan_kerja)) {
                    $data['tunjangan_kerja'] = 0;
                }else{
                    $data['tunjangan_kerja'] = $pengerjaan_rit_weekly->tunjangan_kerja;
                }
            }else{
                $data['tunjangan_kerja'] = 0;
            }

            if (empty($pengerjaan_rit_weekly->uang_makan)) {
                $data['uang_makan'] = 0;
            }else{
                $data['uang_makan'] = $pengerjaan_rit_weekly->uang_makan;
            }

            if (empty($pengerjaan_rit_weekly->plus_1)) {
                $data['plus_1'] = 0;
                $data['keterangan_plus_1'] = '';
            }else{
                $explode_plus_1 = explode("|",$pengerjaan_rit_weekly->plus_1);
                $data['plus_1'] = floatval($explode_plus_1[0]);
                $data['keterangan_plus_1'] = $explode_plus_1[1];
            }

            if (empty($pengerjaan_rit_weekly->plus_2)) {
                $data['plus_2'] = 0;
                $data['keterangan_plus_2'] = '';
            }else{
                $explode_plus_2 = explode("|",$pengerjaan_rit_weekly->plus_2);
                $data['plus_2'] = floatval($explode_plus_2[0]);
                $data['keterangan_plus_2'] = $explode_plus_2[1];
            }

            if (empty($pengerjaan_rit_weekly->plus_3)) {
                $data['plus_3'] = 0;
                $data['keterangan_plus_3'] = '';
            }else{
                $explode_plus_3 = explode("|",$pengerjaan_rit_weekly->plus_3);
                $data['plus_3'] = floatval($explode_plus_3[0]);
                $data['keterangan_plus_3'] = $explode_plus_3[1];
            }

            $data['total_gaji'] = $data['hasil_upah_dasar']+$data['plus_1']+$data['plus_2']+$data['plus_3']+$data['uang_makan']+$data['hasil_lembur']+$data['tunjangan_kerja']+$data['tunjangan_kehadiran'];

            if (empty($pengerjaan_rit_weekly->minus_1)) {
                $data['minus_1'] = 0;
                $data['keterangan_minus_1'] = '';
            }else{
                $explode_minus_1 = explode("|",$pengerjaan_rit_weekly->minus_1);
                $data['minus_1'] = $explode_minus_1[0];
                $data['keterangan_minus_1'] = $explode_minus_1[1];
            }

            if (empty($pengerjaan_rit_weekly->minus_2)) {
                $data['minus_2'] = 0;
                $data['keterangan_minus_2'] = '';
            }else{
                $explode_minus_2 = explode("|",$pengerjaan_rit_weekly->minus_2);
                $data['minus_2'] = $explode_minus_2[0];
                $data['keterangan_minus_2'] = $explode_minus_2[1];
            }

            if (empty($pengerjaan_rit_weekly->jht)) {
                $data['jht'] = 0;
            }else{
                $data['jht'] = $pengerjaan_rit_weekly->jht;
            }

            if (empty($pengerjaan_rit_weekly->bpjs_kesehatan)) {
                $data['bpjs_kesehatan'] = 0;
            }else{
                $data['bpjs_kesehatan'] = $pengerjaan_rit_weekly->bpjs_kesehatan;
            }

            if (empty($pengerjaan_rit_weekly->pensiun)) {
                $data['pensiun'] = 0;
            }else{
                $data['pensiun'] = $pengerjaan_rit_weekly->pensiun;
            }

            $data['total_upah_diterima'] = $data['total_gaji']-$data['minus_1']-$data['minus_2']-$data['jht']-$data['bpjs_kesehatan']-$data['pensiun'];

            $pdf = Pdf::loadView('email.slipGajiOperatorSupirRit',$data);
            $pdf->setPaper(array(0,0,400,500));   
            $pdf->setEncryption(Carbon::create($pengerjaan_rit_weekly->operator_supir_rit->biodata_karyawan->tgl_lahir)->format('dmY'),Carbon::create($pengerjaan_rit_weekly->operator_supir_rit->biodata_karyawan->tgl_lahir)->format('dmY'));

            Mail::send('email.testingMailSupirRit',$data, function($message) use($data,$pdf,$pengerjaan_rit_weekly){
                $message->to(strtolower($pengerjaan_rit_weekly->operator_supir_rit->biodata_karyawan->email))
                        ->subject('Laporan Slip Gaji '.$pengerjaan_rit_weekly->operator_supir_rit->biodata_karyawan->nama.' '.date('d-m-Y'))
                        ->attachData($pdf->output(), 'Laporan Slip Gaji '.$pengerjaan_rit_weekly->operator_supir_rit->biodata_karyawan->nama.' '.date('d-m-Y').'.pdf');
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
