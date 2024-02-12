<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Roles;
use App\Models\User;
use App\Models\UserManagement;
use \Carbon\Carbon;
use Hash;
use Cache;
use Validator;
use DataTables;

class UserController extends Controller
{
    public function user(Request $request)
    {
        if(auth()->user()->roles == 3){
            return redirect()->back();
        }else{
            if ($request->ajax()) {
                $data = User::all();
                return DataTables::of($data)
                        ->addIndexColumn()
                        ->addColumn('roles', function($row){
                            // dd($row->role->roles);
                            return $row->role->nama_role;
                        })
                        ->addColumn('is_online', function($row){
                            if(Cache::has('is_online' . $row->id)){
                                return '<span class="text-success">Online</span>';
                            }else{
                                return '<span class="text-secondary">Offline</span>';
                            }
                        })
                        ->addColumn('is_active', function($row){
                            if ($row->is_active == 1) {
                                return 'Aktif';
                            }
                            elseif ($row->is_active == 0) {
                                return 'Non Aktif';
                            }
                            // return $row->role->roles;
                        })
                        ->addColumn('created_at', function($row){
                            return Carbon::parse($row->created_at)->isoFormat('LLLL');
                        })
                        ->addColumn('updated_at', function($row){
                            return Carbon::parse($row->updated_at)->isoFormat('LLLL');
                        })
                        ->addColumn('action', function($row){
                            $Roles = Roles::find(auth()->user()->roles);
                            $UserManagement = UserManagement::where('user_id',auth()->user()->id)->first();
                            $btn = '<div class="btn-group" role="group" aria-label="Basic example">';
                            if($Roles->id == 1){
                                // $btn = $btn.'<button type="button" onclick="management(`'.$row->id.'`)" class="btn btn-primary btn-icon">';
                                // $btn = $btn.'<i class="fas fa-lock"></i> User Management';
                                // $btn = $btn.'</button>';
                                $btn = $btn.'<button type="button" onclick="reset(`'.$row->id.'`)" class="btn btn-primary btn-icon">';
                                $btn = $btn.'<i class="fas fa-undo-alt"></i> Reset Password';
                                $btn = $btn.'</button>';
                            }
                            // if($UserManagement->u == "Y"){
                            //     $btn = $btn.'<button type="button" onclick="edit(`'.$row->id.'`)" class="btn btn-warning btn-icon">';
                            //     $btn = $btn.'<i class="fa fa-edit"></i>';
                            //     $btn = $btn.'</button>';
                            // }
                            // if($UserManagement->d == "Y"){
                            //     $btn = $btn.'<button type="button" onclick="hapus(`'.$row->id.'`)" class="btn btn-danger btn-icon">';
                            //     $btn = $btn.'<i class="fa fa-trash"></i>';
                            //     $btn = $btn.'</button>';
                            // }
                            $btn = $btn.'</div>';
                            return $btn;
                        })
                        ->rawColumns(['action','is_online'])
                        ->make(true);
            }
            $data['roles'] = Roles::all();
            $data['UserManagement'] = UserManagement::where('user_id',auth()->user()->id)->first();
            return view('backend.users.user',$data);
        }
    }

    public function simpan(Request $request)
    {
        $rules = [
            'username' => 'required',
            'name' => 'required',
            // 'email' => 'required',
            'roles' => 'required',
            // 'password' => 'required',
        ];
 
        $messages = [
            'username.required'  => 'Username wajib diisi.',
            'name.required'  => 'Nama wajib diisi.',
            // 'email.required'  => 'Email wajib diisi.',
            'roles.required'   => 'Akses User wajib diisi.',
            // 'password.required'   => 'Password User wajib diisi.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->passes()) {
            $input = $request->all();
            
            $input['id_generate'] = Str::uuid()->toString();
            $input['password'] = Hash::make('user1234');
            $user = User::create($input);

            if($user){
                $message_title="Berhasil !";
                $message_content="User ".$input['name']." Berhasil Dibuat";
                $message_type="success";
                $message_succes = true;
            }

            $array_message = array(
                'success' => $message_succes,
                'message_title' => $message_title,
                'message_content' => $message_content,
                'message_type' => $message_type,
            );
            return response()->json($array_message);
        }

        return response()->json(
            [
                'success' => false,
                'error' => $validator->errors()->all()
            ]
        );
    }

    public function reset_pswd($id)
    {
        $user = User::find($id);
        if(empty($user)){
            return response()->json([
                'success' => false,
                'message' => 'User Tidak Ditemukan'
            ]);
        }

        $user->password = Hash::make('reset123');
        $user->update();
        if($user){
            $message_title="Berhasil !";
            $message_content="User ".$user->name." Berhasil Direset";
            $message_type="success";
            $message_succes = true;

            $array_message = array(
                'success' => $message_succes,
                'message_title' => $message_title,
                'message_content' => $message_content,
                'message_type' => $message_type,
            );
            return response()->json($array_message);
        }

        return response()->json(
            [
                'success' => false,
                'error' => 'User '.$user->name.' Tidak Berhasil Direset'
            ]
        );
    }

    public function user_management_index(Request $request)
    {
        if(auth()->user()->roles != 1){
            return redirect()->back();
        }else{
            if ($request->ajax()) {
                $data = UserManagement::all();
                return DataTables::of($data)
                        ->addIndexColumn()
                        ->addColumn('user', function($row){
                            return $row->user->name;
                        })
                        ->addColumn('c', function($row){
                            if($row->c == 'Y'){
                                return '<i class="fas fa-check"></i>';
                            }else{
                                return '<i class="fas fa-times"></i>';
                            }
                        })
                        ->addColumn('r', function($row){
                            if($row->r == 'Y'){
                                return '<i class="fas fa-check"></i>';
                            }else{
                                return '<i class="fas fa-times"></i>';
                            }
                        })
                        ->addColumn('u', function($row){
                            if($row->u == 'Y'){
                                return '<i class="fas fa-check"></i>';
                            }else{
                                return '<i class="fas fa-times"></i>';
                            }
                        })
                        ->addColumn('d', function($row){
                            if($row->d == 'Y'){
                                return '<i class="fas fa-check"></i>';
                            }else{
                                return '<i class="fas fa-times"></i>';
                            }
                        })
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
                        ->rawColumns(['action','c','r','u','d'])
                        ->make(true);
            }
            $data['users'] = User::all();
            return view('backend.users.management.index',$data);
        }
    }

    public function user_management_simpan(Request $request)
    {
        $rules = [
            'user_id' => 'required',
        ];
        $messages = [
            'user_id.required'  => 'User wajib diisi.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->passes()) {
            $input = $request->all();
            // dd($input);
            $input['id'] = Str::uuid()->toString();
            if($request->options_akses == 'administrator'){
                $input['c'] = 'Y';
                $input['r'] = 'Y';
                $input['u'] = 'Y';
                $input['d'] = 'Y';
            }else if($request->options_akses == 'admin'){
                $input['c'] = 'Y';
                $input['r'] = 'Y';
                $input['u'] = 'Y';
                $input['d'] = 'T';
            }else if($request->options_akses == 'user'){
                $input['c'] = 'Y';
                $input['r'] = 'Y';
                $input['u'] = 'T';
                $input['d'] = 'T';
            }else if($request->options_akses == 'custom'){
                $input['c'] = $request->c;
                $input['r'] = $request->r;
                $input['u'] = $request->u;
                $input['d'] = $request->d;
            }
            $user_management = UserManagement::create($input);

            if($user_management){
                $message_title="Berhasil !";
                $message_content="User Management Berhasil Dibuat";
                $message_type="success";
                $message_succes = true;
            }

            $array_message = array(
                'success' => $message_succes,
                'message_title' => $message_title,
                'message_content' => $message_content,
                'message_type' => $message_type,
            );
            return response()->json($array_message);
        }

        return response()->json(
            [
                'success' => false,
                'error' => $validator->errors()->all()
            ]
        );
    }

    public function user_management_detail($id)
    {
        $user_management = UserManagement::find($id);
        if(empty($user_management)){
            return response()->json([
                'success' => false,
                'message' => 'User Management Tidak Ditemukan'
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $user_management
        ]);
    }

    public function user_management_update(Request $request)
    {
        $rules = [
            'edit_user_id' => 'required',
        ];
        $messages = [
            'edit_user_id.required'  => 'User wajib diisi.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->passes()) {
            // $input = $request->all();
            $input['id'] = $request->edit_id;
            $input['user_id'] = $request->edit_user_id;
            $input['c'] = $request->edit_c;
            $input['r'] = $request->edit_r;
            $input['u'] = $request->edit_u;
            $input['d'] = $request->edit_d;

            $user_management = UserManagement::where('id',$request->edit_id)->update($input);

            if($user_management){
                $message_title="Berhasil !";
                $message_content="User Management Berhasil Diupdate";
                $message_type="success";
                $message_succes = true;
            }

            $array_message = array(
                'success' => $message_succes,
                'message_title' => $message_title,
                'message_content' => $message_content,
                'message_type' => $message_type,
            );
            return response()->json($array_message);
        }

        return response()->json(
            [
                'success' => false,
                'error' => $validator->errors()->all()
            ]
        );
    }
}
