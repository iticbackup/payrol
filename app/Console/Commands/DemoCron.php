<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Mail;

class DemoCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        // \Mail::send('OK', function($message){
        //     $message->to('rioanugrah8899@gmail.com')
        //             ->subject('Test Email Payrol '.date('d-m-Y H:i'));
        // });

        // Mail::to('rioanugrah999@gmail.com')->send(new \App\Mail\MyTestMail());
        // if (Mail::failures()) {
        //     echo 'Email Tidak Terkirim '. date('Y-m-d H:i:s');
        // }else{
        //     echo 'Email Terkirim '. date('Y-m-d H:i:s');
        // }

        \App\Models\User::create([
            'id_generate' => Str::uuid()->toString(),
            'username' => 'ok',
            'name' => 'Ok',
            'password' => \Hash::make('user1234'),
            'roles' => 3,
            'is_active' => 1,
        ]);

        return 'OK';

        // echo "Cron job Berhasil di jalankan " . date('Y-m-d H:i:s');
        // echo \Log::info("Cron job Berhasil di jalankan " . date('Y-m-d H:i:s'));

        // Mail::to('rioanugrah999@gmail.com')->send(new \App\Mail\MyTestMail());
        // return 0;
        // \Log::info("Cron job Berhasil di jalankan " . date('Y-m-d H:i:s'));
    }
}
