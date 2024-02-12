<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Roles;
use \Carbon\Carbon;
use Validator;
use DataTables;

class RolesController extends Controller
{
    public function index(Request $request)
    {
        if(auth()->user()->roles == 3){
            return redirect()->back();
        }else{
            if ($request->ajax()) {
                $data = Roles::all();
                return DataTables::of($data)
                        ->addIndexColumn()
                        ->addColumn('created_at', function($row){
                            return Carbon::parse($row->created_at)->isoFormat('LLLL');
                        })
                        ->addColumn('updated_at', function($row){
                            return Carbon::parse($row->updated_at)->isoFormat('LLLL');
                        })
                        ->addColumn('action', function($row){
                            $btn = '<button type="button" onclick="edit(`'.$row->id.'`)" class="btn btn-warning btn-icon">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button type="button" onclick="hapus(`'.$row->id.'`)" class="btn btn-danger btn-icon">
                                        <i class="fa fa-trash"></i>
                                    </button>';
                            return $btn;
                        })
                        ->rawColumns(['action'])
                        ->make(true);
            }
            return view('backend.roles.index');
        }
    }
    public function simpan()
    {
        # code...
    }
}
