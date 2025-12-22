<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use Exception;

//vendors
use Yajra\DataTables\DataTables;

//models
use App\Models\User;

//request
use App\Http\Requests\Settings\UsersAdminRequest;

class UsersAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->wantsJson())
        {
            $data = User::select('*')
            ->where('role', 'admin');
            
            return DataTables::of($data)
            ->addIndexColumn()      
            ->toJson();
        }
        else
        {
            return view('admin.settings.users.users-index', []);
        }
    }        	
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.settings.users.user-create');
    }	
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UsersAdminRequest $request): \Illuminate\Http\RedirectResponse
    {
        
        $validated = $request->validated();
            
        $route_name = 'settings-users-superadmin.index';
        
        $validated['password'] = Hash::make($request->password);


        $user = User::create($validated);
        $user->assignRole(['admin']);

        return redirect(route($route_name))->with('swal', true);
    }	
    public function edit(Request $request, $id)
    {
        $user = User::find($id);

        if (is_null($user))
        {
            \Alert::error("User dengan ID ($id) tidak terdaftar.")->autoClose(3000)->timerProgressBar();
            return back()->with('swal', false);			            
        }
        else
        {
            return view('admin.settings.users.user-edit', [
                'data' => $user,
            ]);
        }
    }
    public function update(UsersAdminRequest $request, $id)
    {
        $user = User::find($id);

        if (is_null($user))
        {
            \Alert::error("User dengan ID ($id) tidak terdaftar.")->autoClose(3000)->timerProgressBar();
            return back()->with('swal', false);
        }
        else
        {
            $validated = $request->validated();

            if(!is_null($validated['password']))
            {
                $user->password = Hash::make($validated['password']);
            } 
            
            $user->update($validated);
            
            \Alert::success('Berhasil', 'Data user dengan role ' . $user->default_role. '  berhasil diubah.')->autoClose(3000)->timerProgressBar();	
            
            $route_name = 'settings-users-superadmin.index';

            return redirect(route($route_name))->with('swal', true);
        }
    }	
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $user = User::find($id);

        try
        {
            if (is_null($user))
            {
                throw new Exception("User dengan ID ($id) tidak terdaftar.");
            }

            if ($user->isdeleted == 0)
            {
                throw new Exception("User dengan ID ($id) tidak bisa dihapus karena memiliki flag isdeleted = 0.");
            }

            if ($user->default_role == 'mahasiswa' && $user->mahasiswa->count() > 0)
            {
                throw new Exception("User mahasiswa ini tidak bisa dhapus karena memiliki satu atau lebih register mahasiswa");      
            }

            if ($user->default_role == 'mahasiswabaru' && $user->mahasiswabaru->count() > 0)
            {
                throw new Exception("User mahasiswa baru ini tidak bisa dhapus karena memiliki satu atau lebih formulir pendaftaran");
            }

            if ($user->default_role == 'dosen')
            {
                throw new Exception("User dengan default role dosen tidak bisa dhapus melalui halaman ini");
            }

            $default_role = $user->default_role;
            $user->delete();

            activity()
                ->event('store-user')
                ->withProperties(['ip' => $request->ip()])
                ->tap(function (Activity $activity) {
                    $activity->log_name = 'system-user';
                })
                ->performedOn($user)
                ->log("User {$user->name} berhasil dihapus");

            \Alert::success("Data user dengan role $default_role berhasil dihapus.")->autoClose(3000)->timerProgressBar();
            // return redirect(route('system-users-manage.index'))->with('swal', true);
            return back()->with('swal', true);
        }
        catch(\Exception $e)
        {
            \Alert::error($e->getMessage())->persistent();
            // return redirect(route('system-users-manage.index'))->with('swal', false);
            return back()->with('swal', true);
        }
    }
    /**
     * Store user permissions resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeuserpermissions(Request $request)
    {      
        $table_user = User::getTableName();
        
        $this->validate($request, [
            'user_id' => "required|exists:$table_user,id",
        ]);
        $post = $request->all();		
        $user_id = $post['user_id'];

        $user = User::find($user_id);			

        $permissions = isset($post['chkpermission']) ? $post['chkpermission'] : [];
        $current_permission_role = $user->permissions->pluck('name','id')->toArray();
        
        $permissions = $current_permission_role + $permissions;		
        
        $records = [];
        foreach($permissions as $perm_id=>$v)
        {
            $records[] = $perm_id;
        }
        
        $user->givePermissionTo($records);		
        
        \Alert::success('Berhasil', 'Permission user ' . $user->username . ' berhasil diubah atau ditambah.')->autoClose(3000)->timerProgressBar();
        return redirect(route('system-users-manage.show', ['id' => $user_id, 'tab' => 'permission']))->with('swal', true);
    }
    /**
     * Destroy user permissions resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function revokeuserpermission(Request $request)
    {      
        $post = $request->all();
        $name = $post['permission_name'];
        $user_id = $post['user_id'];		
        $pid = $post['pid'];		

        $user = User::find($user_id);

        $user->revokePermissionTo($name);
        
        \Alert::success('Berhasil', 'Permission '. $request->input('permission_name'). ' dari user ' . $user->name . ' berhasil dihapus.')->autoClose(3000)->timerProgressBar();
        return redirect(route("$pid.show", ['id' => $request->input('user_id'), 'tab' => 'permission']))->with('swal', true);		
    }	
    /**
     * Create user permissions resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeuserrole(Request $request)
    {
        $table_user = User::getTableName();
        $table_role = config('permission.table_names')['roles'];
        
        $this->validate($request, [
            'user_id' => "required|exists:$table_user,id",
            'role_name' => "required|exists:$table_role,name",
        ], [
            'user_id.required' => 'Silakan pilih pengguna yang akan ditambahkan role-nya',
            'user_id.exists' => 'Pengguna yang akan ditambahkan role-nya tidak terdaftar',
            'role_name.required' => 'Silakan pilih salah satu role pengguna yang akan ditambahkan',
            'role_name.exists' => 'Role pengguna yang akan ditambahkan tidak terdaftar',
        ]);
        
        $post = $request->all();		
        $user_id = $post['user_id'];
        $role_name = $post['role_name'];
        $pid = $post['pid'];		
        
        $user = User::find($user_id);			

        $daftar_role = $user->getRoleNames()->toArray();
        $daftar_role[] = $role_name;
        
        $user->syncRoles($daftar_role);

        \Alert::success('Berhasil', 'Role '. $request->input('role_name'). ' dari user ' . $user->name . ' berhasil ditambah.')->autoClose(3000)->timerProgressBar();
        return redirect(route("$pid.show", ['id' => $user_id, 'tab' => 'role']))->with('swal', true);		
    }
    /**
     * Destroy user permissions resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function revokeuserrole(Request $request)
    {      
        $table_user = User::getTableName();
        $table_role = config('permission.table_names')['roles'];

        $this->validate($request, [
            'user_id' => "required|exists:$table_user,id",
            'role_name_delete' => "required|exists:$table_role,name",
        ],[
            'user_id.required' => 'Silakan pilih pengguna yang akan dihapus role-nya',
            'user_id.exists' => 'Pengguna yang akan dihapus role-nya tidak terdaftar',
            'role_name_delete.required' => 'Silakan pilih salah satu role pengguna yang akan dihapus',
            'role_name_delete.exists' => 'Role pengguna yang akan dihapus tidak terdaftar',
        ]);

        $post = $request->all();
        $name = $post['role_name_delete'];
        $user_id = $post['user_id'];		
        $pid = $post['pid'];		

        $user = User::find($user_id);
        $daftar_role = $user->getRoleNames()->toArray();

        if (in_array($name, $daftar_role)) 
        {
            unset($daftar_role[array_search($name, $daftar_role)]);

            $role = Role::findByName($name);
            $role_permission = $role->permissions->pluck('name')->toArray();

            $user->revokePermissionTo($role_permission);      
        }    
        $user->syncRoles($daftar_role);
        
        \Alert::success('Berhasil', 'Role '. $request->input('role_name'). ' dari user ' . $user->name . ' berhasil dihapus.')->autoClose(3000)->timerProgressBar();
        return redirect(route("$pid.show", ['id' => $user_id, 'tab' => 'role']))->with('swal', true);		
    }	

    public function resetrole(Request $request, $id)
    {
        $user = User::find($id);
        
        if (is_null($user))
        {
            flash("User dengan ID ($id) tidak terdaftar.")->error();
            return back();			            
        }
        
        $user->syncRoles([$user->default_role]);

        \Alert::success("Role user  ($user->name) berhasil direset menjadi role default.")->autoClose(3000)->timerProgressBar();
        // return redirect(route('system-users-manage.index'))->with('swal', true);
        return back()->with('swal', true);
    }

    public function resetpermission(Request $request)
    {
        \Alert::success('Berhasil', 'Seluruh permission user masing-masing role akan direset sesuai dengan role yang dimilikinya, berhasil ditambah ke queue.')->autoClose(3000)->timerProgressBar();
        // return redirect(route('system-users-manage.index'))->with('swal', true);
        return back()->with('swal', true);
    }
}
