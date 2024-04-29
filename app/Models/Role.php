<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    
    protected $table ='roles';
    protected $fillable =
    [
        'role_name',
        'guard_name',
        'user_id',
        'is_active',
        'is_deleted',
       
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
        "guard_name",
        "status",
        "user_id",
        "is_active",
        "is_deleted"
    ];
    public function users(){
        return $this->hasmany(User::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions');
    }
}
