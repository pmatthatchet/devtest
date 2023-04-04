<?php

namespace App\Services;

use App\Models\Office as OfficeModel;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

/**
 * Class responsible for building a search query for the Office model
 */
class OfficeQueryBuilder
{
    /**
     * Create a query builder to be returned
     *
     * @param \Closure|null $modifier_callback
     * @return QueryBuilder
     */
    public static function createQB(?\Closure $modifier_callback): QueryBuilder
    {
        $qb = OfficeModel::query();

        // Apply any additional callbacks
        if (!is_null($modifier_callback)) {
            $modifier_callback($qb);
        }

        return $qb;
    }

    // === Pre-built callbacks ===

    /**
     * Filter the name to match
     *
     * @param QueryBuilder $qb
     * @param string $name
     * @param bool $exactMatch Must match the name perfectly, false by default
     * @return void
     */
    public static function filterName(QueryBuilder $qb, string $name, bool $exactMatch = false)
    {
        if ($exactMatch) {
            $qb->where('name', $name);

        } else {
            $qb->where('name', 'LIKE', '%' . $name . '%');
        }
    }

    /**
     * Filter the exact number of rooms in the office
     *
     * @param QueryBuilder $qb
     * @param integer $num
     * @return void
     */
    public static function filterOffices(QueryBuilder $qb, int $num)
    {
        $qb->where('office_count', $num);
    }

    /**
     * Filter the exact number of tables in the office
     *
     * @param QueryBuilder $qb
     * @param integer $num
     * @return void
     */
    public static function filterTables(QueryBuilder $qb, int $num)
    {
        $qb->where('table_count', $num);
    }

    /**
     * Apply an area filter, partially or ranged
     *
     * @param QueryBuilder $qb
     * @param integer|null $minNum If supplied, apply minimum area filter
     * @param integer|null $maxNum If supplied, apply maximum area filter
     * @return void
     */
    public static function filterArea(QueryBuilder $qb, ?int $minNum, ?int $maxNum)
    {
        // Apply min range
        if (!is_null($minNum)) {
            $qb->where('area_size', '>=', $minNum);
        }
        
        // Apply max range
        if (!is_null($maxNum)) {
            $qb->where('area_size', '<=', $maxNum);
        }
    }

    /**
     * Apply a price filter, partially or ranged
     *
     * @param QueryBuilder $qb
     * @param integer|null $minNum If supplied, apply minimum price filter
     * @param integer|null $maxNum If supplied, apply maximum price filter
     * @return void
     */
    public static function filterPrice(QueryBuilder $qb, ?int $minNum, ?int $maxNum)
    {
        // Apply min range
        if (!is_null($minNum)) {
            $qb->where('price', '>=', $minNum);
        }
        
        // Apply max range
        if (!is_null($maxNum)) {
            $qb->where('price', '<=', $maxNum);
        }
    }
}
