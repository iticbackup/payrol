<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SlipGajiMail extends Mailable
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
        // return $this->view('backend.payrol.penggajian.borongan.pdf_cek_gaji2');
        return $this->markdown('backend.payrol.penggajian.borongan.pdf_cek_gaji2');
    }
}
