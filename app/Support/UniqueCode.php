<?php

namespace App\Support;

use Closure;
use Illuminate\Database\QueryException;

/**
 * Helper for creating records whose human-readable code column (receipt_no,
 * enrollment_no, employee_id, …) is derived from a running sequence.
 *
 * Deriving that sequence from a live COUNT() is unsafe: it collides whenever a
 * row has been deleted (count drops below the real maximum) or two requests
 * run concurrently (both read the same count). This helper runs the create
 * closure and retries when the resulting insert hits a UNIQUE violation, so a
 * losing race simply recomputes the next number and tries again.
 *
 * The closure MUST re-read the current maximum every time it runs, so each
 * retry produces a fresh sequence value rather than the stale one that clashed.
 */
class UniqueCode
{
    /**
     * @template TModel
     * @param  Closure():TModel  $make
     * @return TModel
     */
    public static function create(Closure $make, int $attempts = 5)
    {
        for ($attempt = 1; ; $attempt++) {
            try {
                return $make();
            } catch (QueryException $e) {
                if ($attempt >= $attempts || ! self::isUniqueViolation($e)) {
                    throw $e;
                }
            }
        }
    }

    /**
     * Next sequence number for a code column, based on the highest value
     * currently stored (not a COUNT), read from the most recently inserted
     * matching row. Returns 1 when no matching row exists yet.
     */
    public static function nextSequence(string $lastCode = null): int
    {
        if ($lastCode === null || $lastCode === '') {
            return 1;
        }

        $suffix = substr($lastCode, strrpos($lastCode, '-') + 1);

        return ((int) $suffix) + 1;
    }

    protected static function isUniqueViolation(QueryException $e): bool
    {
        // SQLSTATE 23000 = integrity constraint violation (SQLite & MySQL).
        return (string) $e->getCode() === '23000';
    }
}
