<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Admin extends Model
{
    use HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    // admins has created_at but no updated_at column/trigger — see
    // backend_details.md §4.
    const UPDATED_AT = null;

    protected $table = 'admins';

    protected $fillable = [
        'username',
        'password_hash',
        'full_name',
        'role',
        'org_code',
    ];

    protected $hidden = [
        'password_hash',
    ];

    /** True when this admin can access both orgs (org_code is null). */
    public function hasAccessTo(string $orgCode): bool
    {
        return $this->org_code === null || $this->org_code === $orgCode;
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'org_code', 'code');
    }
}
