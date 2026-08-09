<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    protected $table = 'organizations';

    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = ['code', 'name'];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'org_code', 'code');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'org_code', 'code');
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'org_code', 'code');
    }
}
