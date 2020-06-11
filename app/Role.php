<?php

namespace App;

use Spatie\Permission\Models\Role as BaseRole;

class Role extends BaseRole {

    protected $table = 'roles';
    
    public const UNDELETEABLE_ROLES = ['Teacher'];

    public const ARCHIVED_STUDENT = "Archived Student";
    public const ARCHIVED_TEACHER = "Archived Teacher";
    
    protected $fillable = [
        'name', 'guard_name', 'created_at', 'updated_at',
        'login_redirect_path', 'is_student', 'default_lang',
        'send_login_details', 'can_login'
    ];

    public $timestamps = true;

    public static function get_student_roles()
    {
        return self::where('is_student', 1)->get();
    }
}
