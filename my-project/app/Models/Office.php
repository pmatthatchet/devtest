<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Office model for `offices` table
 * 
 * Has the following fields:
 * - VARCHAR name, non-unique
 * - INTEGER price, unsigned, work in flat units (dollars?)
 * - INTEGER office_count, unsigned, number of office rooms in the complex
 * - INTEGER table_count, unsigned, number of tables in the complex
 * - INTEGER area_size, unsigned, the office area in flat square metres
 */
class Office extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'price',
        'office_count',
        'table_count',
        'area_size',
    ];

    // =============================

    /**
     * Get row count
     *
     * @return integer
     */
    public static function count(): int 
    {
        return self::query()->count();
    }

    /**
     * Wipe the entire table
     *
     * @return integer The number of rows deleted
     */
    public static function formatAll(): int 
    {
        $cnt = self::count();
        self::query()->delete();

        // Get the difference
        $cnt = $cnt - self::count();

        return $cnt;
    }
}
