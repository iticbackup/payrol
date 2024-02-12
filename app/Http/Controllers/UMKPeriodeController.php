<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\UMKBoronganLokal;
use App\Models\UMKBoronganEkspor;
use App\Models\UMKBoronganAmbri;
use App\Models\RitUMK;

use \Carbon\Carbon;
use Validator;

class UMKPeriodeController extends Controller
{
    public function umk_periode()
    {
        // $data['tahun_berjalan'] = 2025;
        $data['tahun_berjalan'] = Carbon::now()->format('Y');
        // $data['umk_borongan_lokals'] = UMKBoronganLokal::whereIn('tahun_aktif',[2023,2024])->get();
        return view('backend.umk_periode.index',$data);
    }

    public function umk_periode_simpan(Request $request, $tahun_aktif)
    {
        // $rules = [
        //     'umk_packing' => 'required',
        //     'umk_bandrol' => 'required',
        //     'umk_inner' => 'required',
        //     'umk_outer' => 'required',
        // ];

        // $messages = [
        //     'umk_packing.required'  => 'UMK Packing wajib diisi.',
        //     'umk_bandrol.required'  => 'UMK Bandrol wajib diisi.',
        //     'umk_inner.required'  => 'UMK Inner wajib diisi.',
        //     'umk_outer.required'  => 'UMK Outer wajib diisi.',
        // ];

        // $validator = Validator::make($request->all(), $rules, $messages);
        // if ($validator->passes()) {
        //     $norut = UMKBoronganLokal::max('id');
        //     foreach ($request->jenis_produk as $key => $value) {
        //         // dd($request->tahun_aktif[$key]);
        //         // dd(Carbon::create($request->tahun_aktif[$key])->format('Y')-1);
                
        //         $umk_borongan_lokal = UMKBoronganLokal::where('jenis_produk',$request->jenis_produk[$key])->where('tahun_aktif',Carbon::create($request->tahun_aktif[$key])->format('Y')-1)->first();
        //         if (!empty($umk_borongan_lokal)) {
        //             $input['id'] = $norut+$key+1;
        //             $input['jenis_produk'] = $request->jenis_produk[$key];
        //             $input['umk_packing'] = $request->umk_packing[$key];
        //             $input['umk_bandrol'] = $request->umk_bandrol[$key];
        //             $input['umk_inner'] = $request->umk_inner[$key];
        //             $input['umk_outer'] = $request->umk_outer[$key];
        //             $input['tahun_aktif'] = Carbon::create($request->tahun_aktif[$key])->format('Y');
        //             $input['status'] = "Y";
        //             $umk_borongan_lokal->update([
        //                 'status' => "T"
        //             ]);
        //             UMKBoronganLokal::create($input);
        //         }
        //     }
        // }
        // return $validator->errors()->all();
        $norut = UMKBoronganLokal::max('id');
        foreach ($request->jenis_produk as $key => $value) {
            $umk_borongan_lokal = UMKBoronganLokal::where('jenis_produk',$request->jenis_produk[$key])->where('tahun_aktif',Carbon::create($request->tahun_aktif[$key])->format('Y')-1)->first();
            if (!empty($umk_borongan_lokal)) {
                $input['id'] = $norut+$key+1;
                $input['jenis_produk'] = $request->jenis_produk[$key];
                $input['umk_packing'] = $request->umk_packing[$key];
                $input['umk_bandrol'] = $request->umk_bandrol[$key];
                $input['umk_inner'] = $request->umk_inner[$key];
                $input['umk_outer'] = $request->umk_outer[$key];
                $input['tahun_aktif'] = $tahun_aktif;
                $input['status'] = "Y";
                $umk_borongan_lokal->update([
                    'status' => "T"
                ]);
                UMKBoronganLokal::create($input);
            }
        }

        return redirect()->back()->with([
            'success' => 'Data Berhasil Disimpan'
        ]);
    }

    public function umk_borongan_ekspor_simpan(Request $request, $tahun_aktif)
    {
        $norut = UMKBoronganEkspor::max('id');
        foreach ($request->jenis_produk as $key => $value) {
            $umk_borongan_ekspor = UMKBoronganEkspor::where('jenis_produk',$request->jenis_produk[$key])->where('tahun_aktif',$tahun_aktif-1)->first();
            if (!empty($umk_borongan_ekspor)) {
                $input['id'] = $norut+$key+1;
                $input['jenis_produk'] = $request->jenis_produk[$key];
                $input['umk_packing'] = $request->umk_packing[$key];
                $input['umk_kemas'] = $request->umk_kemas[$key];
                $input['umk_pilih_gagang'] = $request->umk_pilih_gagang[$key];
                $input['tahun_aktif'] = $tahun_aktif;
                $input['status'] = "Y";
                $umk_borongan_ekspor->update([
                    'status' => "T"
                ]);
                UMKBoronganEkspor::create($input);
            }
        }

        return redirect()->back()->with([
            'success' => 'Data Berhasil Disimpan'
        ]);
    }

    public function umk_borongan_ambri_simpan(Request $request, $tahun_aktif)
    {
        $norut = UMKBoronganAmbri::max('id');
        foreach ($request->jenis_produk as $key => $value) {
            $umk_borongan_ambri = UMKBoronganAmbri::where('jenis_produk',$request->jenis_produk[$key])->where('tahun_aktif',$tahun_aktif-1)->first();
            if (!empty($umk_borongan_ambri)) {
                $input['id'] = $norut+$key+1;
                $input['jenis_produk'] = $request->jenis_produk[$key];
                $input['umk_etiket'] = $request->umk_etiket[$key];
                $input['umk_las_tepi'] = $request->umk_las_tepi[$key];
                $input['umk_las_pojok'] = $request->umk_las_pojok[$key];
                $input['umk_ambri'] = $request->umk_ambri[$key];
                $input['tahun_aktif'] = $tahun_aktif;
                $input['status'] = "Y";
                $umk_borongan_ambri->update([
                    'status' => "T"
                ]);
                UMKBoronganAmbri::create($input);
            }
        }

        return redirect()->back()->with([
            'success' => 'Data Berhasil Disimpan'
        ]);
    }

    public function umk_supir_rit_simpan(Request $request, $tahun_aktif)
    {
        $norut = RitUMK::max('id');
        foreach ($request->kategori_upah as $key => $value) {
            $umk_supir_rit = RitUMK::where('kategori_upah',$request->kategori_upah[$key])
                                    ->where('rit_posisi_id',$request->rit_posisi_id[$key])
                                    ->where('tahun_aktif',Carbon::create($tahun_aktif)->format('Y')-1)
                                    ->first();
            // dd($umk_supir_rit);
            if (!empty($umk_supir_rit)) {
                $input['id'] = $norut+$key+1;
                $input['kategori_upah'] = $request->kategori_upah[$key];
                $input['rit_posisi_id'] = $request->rit_posisi_id[$key];
                $input['rit_kendaraan_id'] = $request->rit_kendaraan_id[$key];
                $input['rit_tujuan_id'] = $request->rit_tujuan_id[$key];
                $input['tarif'] = $request->tarif[$key];
                $input['tahun_aktif'] = Carbon::create($request->tahun_aktif[$key])->format('Y');
                $input['status'] = "y";
                $umk_supir_rit->update([
                    'status' => 't'
                ]);
                RitUMK::create($input);
            }
        }

        return redirect()->back()->with([
            'success' => 'Data Berhasil Disimpan'
        ]);
    }

}
