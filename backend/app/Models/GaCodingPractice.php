<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GaCodingPractice extends Model
{
    use HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'ga_coding_practice';

    protected $fillable = [
        'student_id',
        'topic_id',
        'total_problems',
        'solved_problems',
        'created_by',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'topic_id');
    }
}
