<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\NewDataPengerjaan;

use \Carbon\Carbon;
use DataTables;

class PeriodeController extends Controller
{
    public function index(Request $request){
        if ($request->ajax()) {
            $data = NewDataPengerjaan::all();
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('tanggal_penggajian', function($row){
                                $explode_tanggal_pengerjaans = explode("#",$row->tanggal);
                                foreach ($explode_tanggal_pengerjaans as $key => $explode_tanggal_pengerjaan) {
                                    if ($key != 0) {
                                        $hasil_tanggal_pengerjaan[] = Carbon::parse($explode_tanggal_pengerjaan)->isoFormat('D MMMM');
                                    }
                                }
                                return $hasil_tanggal_pengerjaan;
                            })
                            ->addColumn('status', function($row){
                                if ($row->status == 'y') {
                                    return '<span class="badge badge-outline-primary">Progress</span>';
                                }else{
                                    return '<span class="badge badge-outline-success">Selesai</span>';
                                }
                                return '-';
                            })
                            ->addColumn('action', function($row){
                                $btn = '';
                                // $btn .= '<div class="button-items">';
                                // $btn .= '<button class="btn btn-success btn-icon-circle btn-icon-circle-md"><i class="far fa-file-pdf"></i></button>';
                                // $btn .= '</div>';

                                // $btn.=  '<div class="btn-group" role="group">';
                                // $btn.=      '<button class="btn btn-primary"><i class="far fa-edit"></i> Close Periode</button>';
                                // $btn.=  '</div>';
                                $btn.= '<div class="form-check form-switch">';
                                if ($row->status == 'y') {
                                    $btn.= '<input class="form-check-input" type="checkbox" name="check'.$row->id.'" checked id="check'.$row->id.'" onclick="status('.$row->id.')">';
                                }else{
                                    $btn.= '<input class="form-check-input" type="checkbox" name="check'.$row->id.'" id="check'.$row->id.'" onclick="status('.$row->id.')">';
                                }
                                $btn.= '</div>';
                                return $btn;
                            })
                            ->rawColumns(['action','status'])
                            ->make(true);
        }
        return view('backend.periode.index');
    }

    public function status_on($id){
        $new_data_pengerjaan = NewDataPengerjaan::find($id);
        if (empty($new_data_pengerjaan)) {
            return response()->json([
                'success' => false,
                'message_title' => 'Data Tidak Ditemukan'
            ]);
        }else{
            $new_data_pengerjaan->update([
                'status' => 'y'
            ]);
            return response()->json([
                'success' => true,
                'message_title' => 'Periode '.$new_data_pengerjaan->kode_pengerjaan.' is Open'
            ]);
        }
    }
    public function status_off($id){
        $new_data_pengerjaan = NewDataPengerjaan::find($id);
        if (empty($new_data_pengerjaan)) {
            return response()->json([
                'success' => false,
                'message_title' => 'Data Tidak Ditemukan'
            ]);
        }else{
            $new_data_pengerjaan->update([
                'status' => 'n'
            ]);
            return response()->json([
                'success' => true,
                'message_title' => 'Periode '.$new_data_pengerjaan->kode_pengerjaan.' is Closed'
            ]);
        }
    }
}
