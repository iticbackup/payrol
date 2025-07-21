<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

use App\Models\NewDataPengerjaan;
use App\Models\KaryawanOperator;
use App\Models\UMKBoronganLokal;
use App\Models\TestingBorongan;

use \Carbon\Carbon;
use DB;

class BoronganImport implements 
ToModel, 
WithStartRow
{
    /**
    * @param Collection $collection
    */
    // public function collection(Collection $collection)
    // {
    //     //
    // }

    use Importable;

    function __construct(
        $tanggal,
        $kodePengerjaan,
        $kodePayrol,
    )
    {
        $this->tanggal = $tanggal;
        $this->kode_pengerjaan = $kodePengerjaan;
        $this->kode_payrol = $kodePayrol;
    }

    private $rows = 0;

    public function waktu($waktu){
        // if(($waktu>0) and ($waktu<60)){
        //     $lama=number_format($waktu,2);
        //     return $lama;
        // }
        // if(($waktu>60) and ($waktu<3600)){
        //     $detik=fmod($waktu,60);
        //     $menit=$waktu-$detik;
        //     $menit=$menit/60;
        //     $lama=$menit.".".number_format($detik,2);
        //     return $lama;
        // }
        // elseif($waktu >3600){
        //     $detik=fmod($waktu,60);
        //     $tempmenit=($waktu-$detik)/60;
        //     $menit=fmod($tempmenit,60);
        //     $jam=($tempmenit-$menit)/60;    
        //     $lama=$jam.".".$menit.".".number_format($detik,2);
        //     return $lama;
        // }

        // return $waktu;
        if(($waktu>0) and ($waktu<=60)){
            $detik=fmod($waktu,60);
            $lama=number_format($waktu,2);
            return $detik;
        }

        if(($waktu>60) and ($waktu<=3600)){
            $detik=fmod($waktu,60);
            $menit=$waktu-$detik;
            $menit=$menit/60;
            $lama=$menit;
            // $lama=$menit.".".number_format($detik,2);
            return $lama;
        }

        elseif($waktu >3600){
            $detik=fmod($waktu,60);
            $tempmenit=($waktu-$detik)/60;
            $menit=fmod($tempmenit,60);
            $jam=($tempmenit-$menit)/60;    
            $lama=$jam.".".$menit.".".number_format($detik,2);
            return $lama;
        }
    }

    public function model(array $row)
    {
        // dd($this->waktu(60));
        $kodePengerjaan = $this->kode_pengerjaan;
        $newDataPengerjaan = NewDataPengerjaan::where('kode_pengerjaan',$kodePengerjaan)->first();
        $totalHari = count(array_filter(explode('#',$newDataPengerjaan->tanggal)));

        for ($i=1; $i <= $totalHari ; $i++) { 
            ++$this->rows;
            $cekNikKaryawan = KaryawanOperator::select('id')->where('nik',$row[1])->first();

            if (empty($cekNikKaryawan)) {
                $operator_karyawan_id = 0;
            }else{
                $operator_karyawan_id = $cekNikKaryawan->id;
            }

            $norut = TestingBorongan::max('id');
            if ($norut > 0) {
                $id = $norut+1;
            }else{
                $id = 1;
            }

            // dd($this->mapping()['h1']);

            $cekUmkBoronganPackingH1 = UMKBoronganLokal::select('id')->where('jenis_produk',$row[3])
                                                                ->where('status','Y')
                                                                ->first();
            if (empty($cekUmkBoronganPackingH1)) {
                $h1 = 0;
            }else{
                $h1 = $cekUmkBoronganPackingH1->id;
            }             

            $cekUmkBoronganPackingH2 = UMKBoronganLokal::select('id')->where('jenis_produk',$row[6])
                                                                ->where('status','Y')
                                                                ->first();
            if (empty($cekUmkBoronganPackingH2)) {
                $h2 = 0;
            }else{
                $h2 = $cekUmkBoronganPackingH2->id;
            }        

            $cekUmkBoronganPackingH3 = UMKBoronganLokal::select('id')->where('jenis_produk',$row[9])
                                                                ->where('status','Y')
                                                                ->first();
            if (empty($cekUmkBoronganPackingH3)) {
                $h3 = 0;
            }else{
                $h3 = $cekUmkBoronganPackingH3->id;
            }   

            $cekUmkBoronganPackingH4 = UMKBoronganLokal::select('id')->where('jenis_produk',$row[12])
                                                                ->where('status','Y')
                                                                ->first();
            if (empty($cekUmkBoronganPackingH4)) {
                $h4 = 0;
            }else{
                $h4 = $cekUmkBoronganPackingH4->id;
            } 

            $cekUmkBoronganPackingH5 = UMKBoronganLokal::select('id')->where('jenis_produk',$row[15])
                                                                ->where('status','Y')
                                                                ->first();
            if (empty($cekUmkBoronganPackingH5)) {
                $h5 = 0;
            }else{
                $h5 = $cekUmkBoronganPackingH5->id;
            }                             
                                                                // dd($cekUmkBoronganPacking);

            $simpanBorongan = TestingBorongan::where('kode_pengerjaan',$kodePengerjaan)
                                                    ->where('operator_karyawan_id',$operator_karyawan_id)
                                                    ->where('tanggal_pengerjaan',Carbon::create($this->tanggal)->format('Y-m-d'))
                                                    ->first();

            if (empty($simpanBorongan)) {
                
                return new TestingBorongan([
                    'id' => $id,
                    'kode_pengerjaan' => $kodePengerjaan,
                    'kode_payrol' => $kodePengerjaan,
                    'operator_karyawan_id' => $operator_karyawan_id,
                    'tanggal_pengerjaan' => Carbon::create($this->tanggal)->format('Y-m-d'),
                    'hasil_kerja_1' => $h1.'|'.$row[4],
                    'hasil_kerja_2' => $h2.'|'.$row[7],
                    'hasil_kerja_3' => $h3.'|'.$row[10],
                    'hasil_kerja_4' => $h4.'|'.$row[13],
                    'hasil_kerja_5' => $h5.'|'.$row[16],
                    'total_jam_kerja_1' => $row[5],
                    'total_jam_kerja_2' => $row[8],
                    'total_jam_kerja_3' => $row[11],
                    'total_jam_kerja_4' => $row[14],
                    'total_jam_kerja_5' => $row[17],
                ]);
            }else{
                $simpanBorongan->update([
                    'kode_pengerjaan' => $kodePengerjaan,
                    'kode_payrol' => $kodePengerjaan,
                    'tanggal_pengerjaan' => Carbon::create($this->tanggal)->format('Y-m-d'),
                    'hasil_kerja_1' => $h1.'|'.$row[4],
                    'hasil_kerja_2' => $h2.'|'.$row[7],
                    'hasil_kerja_3' => $h3.'|'.$row[10],
                    'hasil_kerja_4' => $h4.'|'.$row[13],
                    'hasil_kerja_5' => $h5.'|'.$row[16],
                    'total_jam_kerja_1' => $row[5],
                    'total_jam_kerja_2' => $row[8],
                    'total_jam_kerja_3' => $row[11],
                    'total_jam_kerja_4' => $row[14],
                    'total_jam_kerja_5' => $row[17],
                ]);
            }
        }
        
    }

    // public function columnFormats(): array
    // {
    //     return [
    //         'F' => NumberFormat::FORMAT_DATE_DDMMYYYY,
    //         'C' => NumberFormat::FORMAT_CURRENCY_EUR_INTEGER,
    //     ];
    // }

    public function getRowCount(): int
    {
        return $this->rows;
    }

    public function startRow(): int
    {
        return 17;
    }
}
