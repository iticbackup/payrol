<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use App\Models\NewDataPengerjaan;
use App\Models\PengerjaanWeekly;
use App\Models\Pengerjaan;

class SlipGajiOperatorBoronganMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($kode_pengerjaan,$id)
    {
        $this->kode_pengerjaan = $kode_pengerjaan;
        $this->id = $id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // return $this->view('view.name');
        // return 'OK '.$this->kode_pengerjaan;
        $data['kode_pengerjaan'] = $this->kode_pengerjaan;
        
        $data['new_data_pengerjaan'] = NewDataPengerjaan::where('kode_pengerjaan', $this->kode_pengerjaan)->first();
        $data['explode_tanggal_pengerjaans'] = explode('#', $data['new_data_pengerjaan']['tanggal']);
        $data['exp_tanggals'] = array_filter($data['explode_tanggal_pengerjaans']);
        $data['a'] = count($data['exp_tanggals']);

        $data['exp_tgl_awal'] = explode('-', $data['exp_tanggals'][1]);
        $data = explode('-', $data['exp_tanggals'][$data['a']]);

        $data['pengerjaan_weekly'] = PengerjaanWeekly::where('kode_pengerjaan', $this->kode_pengerjaan)
                                                ->where('id', $this->id)
                                                ->first();

        $data['pengerjaans'] = Pengerjaan::where('operator_karyawan_id', $data['pengerjaan_weekly']->operator_karyawan_id)
                                        ->where('kode_pengerjaan', $this->kode_pengerjaan)
                                        ->get();
        // return $this->view('email.testingMail',$data);
        return $this->markdown('email.slipGajiOperatorBorongan',$data);
    }
}
