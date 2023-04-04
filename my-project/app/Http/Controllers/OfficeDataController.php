<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;

use App\Services\OfficeQueryBuilder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class OfficeDataController extends BaseController
{

    /**
     * Query office data from the database
     *
     * @param Request $req
     * @return void
     */
    public function getData(Request $req): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($req->all(), [
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer',

            // Filters
            'name' => 'sometimes|string',
            'offices' => 'sometimes|integer|min:0',
            'tables' => 'sometimes|integer|min:0',
            'min_area' => 'sometimes|integer|min:0',
            'max_area' => 'sometimes|integer|min:0',
            'min_price' => 'sometimes|integer|min:0',
            'max_price' => 'sometimes|integer|min:0',
        ]);

        // Block upon failure
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->getMessageBag()->toArray()], 400);
        }
    
        // Get variables and pages
        $vars = $validator->safe();
        $page = $vars['page'] ?? 1;
        $perPage = $vars['per_page'] ?? 20;

        // Create Query Builder
        $qb = OfficeQueryBuilder::createQB(function ($qb) use ($vars) {

            if (isset($vars['name'])) {
                OfficeQueryBuilder::filterName($qb, $vars['name']);
            }

            if (isset($vars['offices'])) {
                OfficeQueryBuilder::filterOffices($qb, $vars['offices']);
            }

            if (isset($vars['tables'])) {
                OfficeQueryBuilder::filterTables($qb, $vars['tables']);
            }

            if (isset($vars['min_area']) || isset($vars['max_area'])) {
                OfficeQueryBuilder::filterPrice($qb, $vars['min_area'] ?? null, $vars['max_area'] ?? null);
            }

            if (isset($vars['min_price']) || isset($vars['max_price'])) {
                OfficeQueryBuilder::filterPrice($qb, $vars['min_price'] ?? null, $vars['max_price'] ?? null);
            }

        });

        return response()->json($qb->paginate($perPage, ['*'], 'page', $page));
    }
}