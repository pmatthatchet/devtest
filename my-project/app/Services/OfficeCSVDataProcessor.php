<?php

namespace App\Services;

use App\Models\Office as OfficeModel;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

/**
 * Class responsible for processing the CSV data
 */
class OfficeCSVDataProcessor
{
    private const CSV_LOCATION = 'files/office-data.csv';

    /**
     * Process the CSV file located at self::CSV_LOCATION
     * 
     * Note: Currently does not validate the input of the CSV
     *
     * @throws \ErrorException
     * @return array
     */
    public static function processCSV(): array
    {
        $ret = [];
        $row = 0;
        
        if (($handle = fopen(self::CSV_LOCATION, "r")) !== false) {
            // Open file
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $num = count($data);

                // Heading check
                if ($num != 5) {
                    throw new \ErrorException('The CSV should always contain 5 columns');
                }
                
                // Check for the following fields for the first line
                if ($row == 0) {
                    // Check for exact heading format
                    if (md5(json_encode($data)) != md5(json_encode(['Name', 'Price', 'Offices', 'Tables', 'Sqm']))) {
                        throw new \ErrorException('Incorrect CSV format');
                    }

                } else {
                    // Append data
                    $ret[] = [
                        'name' => $data[0],
                        'price' => intval($data[1]),
                        'office_count' => intval($data[2]),
                        'table_count' => intval($data[3]),
                        'area_size' => intval($data[4]),
                    ];
                }

                $row++;
            }
            fclose($handle);

            return $ret;

        } else {
            // File could not be read
            throw new \ErrorException('File could not be read');
        }
    }
}
