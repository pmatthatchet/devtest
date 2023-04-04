<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\Office as OfficeModel;
use App\Services\OfficeQueryBuilder;
use App\Services\OfficeCSVDataProcessor;

class OfficeDataController extends BaseController
{
    /**
     * Query office data from the database
     *
     * @param Request $req
     * @return \Illuminate\Http\JsonResponse
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
                OfficeQueryBuilder::filterOffices($qb, intval($vars['offices']));
            }

            if (isset($vars['tables'])) {
                OfficeQueryBuilder::filterTables($qb, intval($vars['tables']));
            }

            if (isset($vars['min_area']) || isset($vars['max_area'])) {
                $min = isset($vars['min_area']) ? intval($vars['min_area']) : null;
                $max = isset($vars['max_area']) ? intval($vars['max_area']) : null;
                OfficeQueryBuilder::filterArea($qb, $min, $max);
            }

            if (isset($vars['min_price']) || isset($vars['max_price'])) {
                $min = isset($vars['min_price']) ? intval($vars['min_price']) : null;
                $max = isset($vars['max_price']) ? intval($vars['max_price']) : null;
                OfficeQueryBuilder::filterPrice($qb, $min, $max);
            }

        });

        return response()->json($qb->paginate($perPage, ['*'], 'page', $page));
    }

    /**
     * Format the table and reimport new updated data
     *
     * @param Request $req
     * @return \Illuminate\Http\JsonResponse
     */
    public function reimportData(Request $req): \Illuminate\Http\JsonResponse
    {
        try {
            $newData = OfficeCSVDataProcessor::processCSV();
            
            // Check if there is data
            if (count($newData) == 0) {
                return response()->json(['errors' => 'No data to import and replace'], 400);
            }

            // Delete and insert new
            $deleted = OfficeModel::formatAll();
            OfficeModel::insert($newData);

            return response()->json(['deleted' => $deleted, 'created' => count($newData)]);     
        
        } catch (\ErrorException $e) {
            return response()->json(['errors' => [$e->getMessage()]], 500);
        }
    }
}