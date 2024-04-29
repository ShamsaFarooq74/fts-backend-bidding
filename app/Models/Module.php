<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Permission;

class Module extends Model
{
    use HasFactory, HasRoles;

    protected $table ='modules';
    protected $fillable =
    [
        'name',
    ];
    protected $hidden = ['created_at','updated_at'];
    
    public function modulesPermission()
    {
        return $this->hasMany(Permission::class,'module_id','id');
    }
}
