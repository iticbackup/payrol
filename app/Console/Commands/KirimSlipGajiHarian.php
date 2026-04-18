<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\KirimGaji;
use App\Models\PengerjaanHarian;
use \Carbon\Carbon;

use Pdf;
use Mail;

class KirimSlipGajiHarian extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:kirimslipgajiharian';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim Slip Gaji Harian Setelah Melakukan Close Period';

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
        $cek_kirim_slip_gajis = KirimGaji::where('kode_pengerjaan','LIKE','%PH_%')
                                        ->where('status','menunggu')
                                        ->orWhere('status','gagal')
                                        ->limit(3)
                                        ->orderBy('id','asc')
                                        ->get();

        foreach ($cek_kirim_slip_gajis as $key => $value) {
            if (!empty($value->karyawan_operator_harian->biodata_karyawan->email)) {
                $data['explode_tanggal_pengerjaans'] = explode('#', $value->new_data_pengerjaan->tanggal);
                $data['exp_tanggals'] = array_filter($data['explode_tanggal_pengerjaans']);
                $data['a'] = count($data['exp_tanggals']);
    
                $data['exp_tgl_awal'] = explode('-', $data['exp_tanggals'][1]);
                $data['exp_tgl_akhir'] = explode('-', $data['exp_tanggals'][$data['a']]);
    
                $data['pengerjaan_harian'] = PengerjaanHarian::where('kode_pengerjaan',$value->kode_pengerjaan)
                                                            ->where('id', $value->pengerjaan_id)
                                                            ->first();
    
                if (empty($data['pengerjaan_harian']->lembur)) {
                    $data['hasil_lembur'] = 0;
                    $data['lembur_1'] = 0;
                    $data['lembur_2'] = 0;
                }else{
                    $exlode_lembur = explode("|",$data['pengerjaan_harian']->lembur);
                    if (empty($exlode_lembur)) {
                        $data['hasil_lembur'] = 0;
                        $data['lembur_1'] = 0;
                        $data['lembur_2'] = 0;
                    }else{
                        $data['hasil_lembur'] = $exlode_lembur[0];
                        $data['lembur_1'] = $exlode_lembur[1];
                        $data['lembur_2'] = $exlode_lembur[2];
                    }
                }
    
                $data['total_jam_lembur'] = floatval($data['lembur_1'])+floatval($data['lembur_2']);
    
                if (empty($data['pengerjaan_harian']->upah_dasar_weekly)) {
                    $data['upah_dasar_weekly'] = 0;
                }else{
                    $data['upah_dasar_weekly'] = $data['pengerjaan_harian']->upah_dasar_weekly;
                }
    
                if($value->new_data_pengerjaan->akhir_bulan == 'y'){
                    if (empty($data['pengerjaan_harian']->tunjangan_kehadiran)) {
                        $data['tunjangan_kehadiran'] = 0;
                    }else{
                        $data['tunjangan_kehadiran'] = $data['pengerjaan_harian']->tunjangan_kehadiran;
                    }
                }else{
                    $data['tunjangan_kehadiran'] = 0;
                }
    
                if ($value->new_data_pengerjaan->akhir_bulan == 'y') {
                    if (empty($data['pengerjaan_harian']->tunjangan_kerja)) {
                        $data['tunjangan_kerja'] = 0;
                    }else{
                        $data['tunjangan_kerja'] = $data['pengerjaan_harian']->tunjangan_kerja;
                    }
                }else{
                    $data['tunjangan_kerja'] = 0;
                }
    
                if (empty($data['pengerjaan_harian']->uang_makan)) {
                    $data['uang_makan'] = 0;
                }else{
                    $data['uang_makan'] = $data['pengerjaan_harian']->uang_makan;
                }
    
                if (empty($data['pengerjaan_harian']->plus_1)) {
                    $data['plus_1'] = 0;
                    $data['ket_plus_1'] = "";
                }else{
                    $explode_plus_1 = explode("|",$data['pengerjaan_harian']->plus_1);
                    $data['plus_1'] = intval($explode_plus_1[0]);
                    $data['ket_plus_1'] = $explode_plus_1[1];
                }
    
                if (empty($data['pengerjaan_harian']->plus_2)) {
                    $data['plus_2'] = 0;
                    $data['ket_plus_2'] = "";
                }else{
                    $explode_plus_2 = explode("|",$data['pengerjaan_harian']->plus_2);
                    $data['plus_2'] = intval($explode_plus_2[0]);
                    $data['ket_plus_2'] = $explode_plus_2[1];
                }
    
                if (empty($data['pengerjaan_harian']->plus_3)) {
                    $data['plus_3'] = 0;
                    $data['ket_plus_3'] = "";
                }else{
                    $explode_plus_3 = explode("|",$data['pengerjaan_harian']->plus_3);
                    $data['plus_3'] = intval($explode_plus_3[0]);
                    $data['ket_plus_3'] = $explode_plus_3[1];
                }
    
                if (empty($data['pengerjaan_harian']->minus_1)) {
                    $data['minus_1'] = 0;
                    $data['ket_minus_1'] = "";
                }else{
                    $explode_minus_1 = explode("|",$data['pengerjaan_harian']->minus_1);
                    if (empty($explode_minus_1[0])) {
                        $data['minus_1'] = 0;
                    }else{
                        $data['minus_1'] = intval($explode_minus_1[0]);
                    }
                    $data['ket_minus_1'] = $explode_minus_1[1];
                }
    
                if (empty($data['pengerjaan_harian']->minus_2)) {
                    $data['minus_2'] = 0;
                    $data['ket_minus_2'] = "";
                }else{
                    $explode_minus_2 = explode("|",$data['pengerjaan_harian']->minus_2);
                    if (empty($explode_minus_2[0])) {
                        $data['minus_2'] = 0;
                    }else{
                        $data['minus_2'] = intval($explode_minus_2[0]);
                    }
                    $data['ket_minus_2'] = $explode_minus_2[1];
                }
                
                if (empty($data['pengerjaan_harian']->jht)) {
                    $data['jht'] = 0;
                }else{
                    $data['jht'] = intval($data['pengerjaan_harian']->jht);
                }
    
                if (empty($data['pengerjaan_harian']->bpjs_kesehatan)) {
                    $data['bpjs_kesehatan'] = 0;
                }else{
                    $data['bpjs_kesehatan'] = intval($data['pengerjaan_harian']->bpjs_kesehatan);
                }
    
                $data['total_gaji_diterima'] = ($data['pengerjaan_harian']->upah_dasar_weekly+$data['hasil_lembur']+$data['tunjangan_kehadiran']+$data['tunjangan_kerja']+
                                                $data['plus_1']+$data['plus_2']+$data['plus_3']+$data['pengerjaan_harian']->uang_makan)-
                                                ($data['jht']+$data['bpjs_kesehatan']+$data['minus_1']+$data['minus_2']);
    
                $pdf = Pdf::loadView('email.slipGajiOperatorHarian',$data);
                $pdf->setPaper(array(0,0,560,380));  
                $pdf->setEncryption(Carbon::create($data['pengerjaan_harian']->operator_karyawan->biodata_karyawan->tgl_lahir)->format('dmY'),Carbon::create($data['pengerjaan_harian']->operator_karyawan->biodata_karyawan->tgl_lahir)->format('dmY'));
    
                Mail::send('email.testingMailHarian',$data, function($message) use($data,$pdf){
                    $message->to(strtolower($data['pengerjaan_harian']->operator_karyawan->biodata_karyawan->email))
                            ->subject('Laporan Slip Gaji '.$data['pengerjaan_harian']->operator_karyawan->biodata_karyawan->nama.' '.date('d-m-Y'))
                            ->attachData($pdf->output(), 'Laporan Slip Gaji '.$data['pengerjaan_harian']->operator_karyawan->biodata_karyawan->nama.' '.date('d-m-Y').'.pdf');
                });
    
                if (\Mail::failures()) {
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

            }else{
                \Log::error('Harian '.$value->nama_karyawan.' Email Belum Tersedia.');
            }
        }
    }
}
