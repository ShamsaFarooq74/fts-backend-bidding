<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Role;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\api\ResponseController;
use App\Models\Role_has_permission;


class RolesPermissonController extends ResponseController
{

    public function userRoles(Request $request)
    {
        $user = Auth::user();
        if ($user->hasRole('admin')) {
            $roles = Role::get();
        }else{
            $roles = Role::where('id','<>','1')->get();
        }
        return $this->sendResponse(1, 'Data fetched successfully!', $roles);
    }

    public function addRolesPermission(Request $request)
    {
        $user = Auth::user();

        $module_query = Module::with('modulesPermission')->select('id','name');
        $modules = $module_query->get();

        $role_id = 0;
        $allowed_modules  = [];
        if($user->hasRole(1)) {
            $user_id = User::whereHas('roles', function ($query) {$query->where('role_id', 1); })->pluck('id')->first();
            $role_id = DB::table('model_has_roles')->where(['model_id' => $user_id])->pluck('role_id')->first();
            $permissions = DB::table('role_has_permissions')->where(['role_id' => $role_id])->pluck('permission_id')->toArray();
            $allowed_modules = array_unique(Permission::whereIn('id',$permissions)->pluck('module_id')->toArray());
        }elseif($user){
            $role_id = DB::table('model_has_roles')->where(['model_id' => $user['role_id']])->pluck('role_id')->first();
        }

        $data['modules'] = $modules;
        $data['allowed_modules'] = $allowed_modules;
        $data['role_id'] = $role_id;
        return $this->sendResponse(1, 'Data fetched!', $data);
    }

    public function saveRolesPermission(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_name' => 'required|unique:roles,name',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(0, $validator->errors()->first(), '');
        }

        $user = Auth::user();
        $role = new role();
        $role->name = $request->role_name;
        $role->guard_name = 'web';
        $role->user_id = $user['id'];
        $role->save();
        
        if($request->has('all')){
            $all = json_decode(json_encode($request->all),true);
            foreach ($all as $data) {
                    $module = Module::find($data);
                    $module_name = strtolower(str_replace(' ', '-', $module->name));
                    $name = $module_name . '-all';
                    $permission = Permission::where('name',$name)->first();
                    if(!$permission){
                        $permission = new Permission;
                    }
                    $permission->module_id = $data;
                    $permission->name = $name;

                    $permission->guard_name = 'web';
                    $permission->save();
                    DB::table('role_has_permissions')->insert([
                            'permission_id' => $permission->id,
                            'role_id' => $role->id,
                        ]
                    );
                
            }
        }

        if($request->has('read')) {
            $read = json_decode(json_encode($request->read),true);
            foreach($read as $data1){
                    $module = Module::find($data1);
                    $module_name = strtolower(str_replace(' ', '-', $module->name));
                    $name = $module_name . '-read';
                    $permission = Permission::where('name',$name)->first();
                    if(!$permission){
                        $permission = new Permission;
                    }
                    $permission->module_id = $data1;
                    $permission->name = $name;
                    $permission->guard_name = 'web';
                    $permission->save();

                    DB::table('role_has_permissions')->insert([
                            'permission_id' => $permission->id,
                            'role_id' => $role->id,
                        ]);
            }
        }

        if($request->has('write')) {
            $write = json_decode(json_encode($request->write),true);
            foreach($write as $data2){
                    $module = Module::find($data2);
                    $module_name = strtolower(str_replace(' ', '-', $module->name));
                    $name = $module_name . '-write';
                    $permission = Permission::where('name',$name)->first();
                    if(!$permission){
                        $permission = new Permission;
                    }
                    $permission->module_id = $data2;
                    $permission->name = $name;
                    $permission->guard_name = 'web';
                    $permission->save();

                    DB::table('role_has_permissions')->insert([
                            'permission_id' => $permission->id,
                            'role_id' => $role->id,
                        ]
                    );
            }
        }

        if($request->has('delete')) {
            $delete = json_decode(json_encode($request->delete),true);
            foreach($delete as $data3){
                    $module = Module::find($data3);
                    $module_name = strtolower(str_replace(' ', '-', $module->name));
                    $name = $module_name . '-delete';
                    $permission = Permission::where('name',$name)->first();
                    if(!$permission){
                        $permission = new Permission;
                    }
                    $permission->module_id = $data3;
                    $permission->name = $name;
                    $permission->guard_name = 'web';
                    $permission->save();
                    DB::table('role_has_permissions')->insert([
                            'permission_id' => $permission->id,
                            'role_id' => $role->id,
                        ]
                    );
            }
        }

        $permissionsData = DB::table('role_has_permissions')->where('role_id', $role->id)->get();
        $permissions = $permissionsData ?? [];
        return $this->sendResponse(1, 'Role Saved Successfully!',$permissions);
    }

    public function editRolesPermission(Request $request, int $id)
    {
        $user = Auth::user();
        $role = Role::find($id);
        $module_query = Module::with('modulesPermission')->select('id','name');
        $modules = $module_query->get();
        $allowed_modules  = [];
        if($user->hasRole(1)) {//admin role id
            $user_id = User::whereHas('roles', function ($query) {$query->where('role_id', 1); })->pluck('id')->first();
            $role_id = DB::table('model_has_roles')->where(['model_id' => $user_id])->pluck('role_id')->first();
            $permissions = DB::table('role_has_permissions')->where(['role_id' => $role_id])->pluck('permission_id')->toArray();
            $allowed_modules = array_unique(Permission::whereIn('id',$permissions)->pluck('module_id')->toArray());
        }else{
            $role_id = DB::table('model_has_roles')->where(['model_id' => $user['id']])->pluck('role_id')->first();
            $permissions = DB::table('role_has_permissions')->where(['role_id' => $role_id])->pluck('permission_id')->toArray();
            $allowed_modules = array_unique(Permission::whereIn('id',$permissions)->pluck('module_id')->toArray());
        }

        $rolePermissions = Permission::join('role_has_permissions', 'role_has_permissions.permission_id', 'permissions.id')
            ->select('permissions.module_id','permissions.name','role_has_permissions.permission_id','role_has_permissions.role_id')
            ->where('role_has_permissions.role_id',$id)
            ->get()->toArray();

        $assignedPermission = DB::table('role_has_permissions')->where('role_id',$id)->pluck('permission_id')->toArray();

        $data['role'] = $role;
        $data['assignedPermission'] = $assignedPermission;
        $data['role_id'] = $role_id;
        $data['allowed_modules'] = $allowed_modules;
        $data['modules'] = $modules;
        $data['rolePermissions'] = $rolePermissions;

        return $this->sendResponse(1, 'Data fetched successfully!',$data);
    }

    public function updateRolesPermission(Request $request)
    {
        $user = Auth::user();
        $user_role =  $user->roles->first();
        $role_id = $user_role['id'];
        $id = $request->id;
        $role_name = Role::find($id);
        if($role_name->name != $request->role_name){
            $validator = Validator::make($request->all(), [
                'role_name' => 'required|unique:roles,role_name'
            ]);

            if ($validator->fails()) {
                return $this->sendResponse(0, $validator->errors()->first(), '');
            }
        }

        $role = Role::find($id);
        $role->name = $request->role_name;
        $role->user_id = $user['id'];
        $role->save();

        DB::table('role_has_permissions')->where('role_id' ,$role->id)->delete();
        if($request->has('all')){
            $all =  json_decode(json_encode($request->all),true);
            foreach($all as $data){
                    $module = Module::find($data);
                    $module_name = strtolower(str_replace(' ', '-', $module->name));
                    $name = $module_name . '-all';
                    $permission = Permission::where('name',$name)->first();
                    if(!$permission){
                        $permission = new Permission;
                    }
                    $permission->module_id = $data;
                    $permission->name = $name;
                    $permission->guard_name = 'web';
                    $permission->save();
                    DB::table('role_has_permissions')->insert([
                            'permission_id' => $permission->id,
                            'role_id' => $role->id,
                        ]
                    );
            }
        }
        if($request->has('read')) {
            $read = json_decode(json_encode($request->read),true);
            foreach($read as $data1){
                    $module = Module::find($data1);
                    $module_name = strtolower(str_replace(' ', '-', $module->name));
                    $name = $module_name . '-read';
                    $permission = Permission::where('name',$name)->first();
                    if(!$permission){
                        $permission = new Permission;
                    }
                    $permission->module_id = $data1;
                    $permission->name = $name;
                    $permission->guard_name = 'web';
                    $permission->save();
                    DB::table('role_has_permissions')->insert([
                            'permission_id' => $permission->id,
                            'role_id' => $role->id,
                        ]
                    );
            }
        }
        if($request->has('write')) {
            $write = json_decode(json_encode($request->write),true);
            foreach($write as $data2){
                    $module = Module::find($data2);
                    $module_name = strtolower(str_replace(' ', '-', $module->name));
                    $name = $module_name . '-write';
                    $permission = Permission::where('name',$name)->first();
                    if(!$permission){
                        $permission = new Permission;
                    }
                    $permission->module_id = $data2;
                    $permission->name = $name;
                    $permission->guard_name = 'web';
                    $permission->save();
                    DB::table('role_has_permissions')->insert([
                            'permission_id' => $permission->id,
                            'role_id' => $role->id,
                        ]
                    );
            }
        }
        if($request->has('delete')) {
            $delete =  json_decode(json_encode($request->delete),true);
            foreach($delete as $data3){
                    $module = Module::find($data3);
                    $module_name = strtolower(str_replace(' ', '-', $module->name));
                    $name = $module_name . '-delete';
                    $permission = Permission::where('name',$name)->first();
                    if(!$permission){
                        $permission = new Permission;
                    }
                    $permission->module_id = $data3;
                    $permission->name = $name;
                    $permission->guard_name = 'web';
                    $permission->save();
                    DB::table('role_has_permissions')->insert([
                            'permission_id' => $permission->id,
                            'role_id' => $role->id,
                        ]
                    );
            }
        }

        $permissionsData = DB::table('role_has_permissions')->where('role_id', $id)->get();
        $permissions = $permissionsData ?? [];
        Artisan::call('cache:clear');

        // $msg = 'Updated Role "'.$role->role_name.'" as "'.$request->role_name.'"';
        // createLog('user_action',$msg);
        return $this->sendResponse(1, 'Role updated Successfully!',$permissions);
    }
}
