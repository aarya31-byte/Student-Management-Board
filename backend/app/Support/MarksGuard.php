<?php

namespace App\Support;

use Illuminate\Validation\ValidationException;

class MarksGuard
{
    /**
     * Partial updates may touch only the obtained or only the total value,
     * so the cross-field check can't rely on both being present in the same
     * request (unlike on create) — callers pass the values as they will be
     * *after* the update is applied.
     */
    public static function ensure(float $obtained, float $total, string $obtainedField): void
    {
        if ($obtained > $total) {
            throw ValidationException::withMessages([
                $obtainedField => "{$obtainedField} cannot exceed the total.",
            ]);
        }
    }
}
