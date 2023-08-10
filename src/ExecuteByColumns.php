<?php
ini_set('memory_limit', '12288M');

define('ROOT', dirname(__DIR__));

require_once ROOT . "/vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\IOFactory;


function getFileName($fileprefix = "", $filename = "part", $filepart = 0, $fileextension = ".xlsx") {
    return ROOT . "/resources/{$fileprefix}-{$filename}{$filepart}{$fileextension}";
}

$filepart = 1;

$readerInputFile = getFileName(filepart: $filepart);

while (file_exists($readerInputFile)) {
    echo "\n Start process with $readerInputFile ". date('Y-m-d H:i:s') ." \n";

    $reader = IOFactory::createReader('Xlsx');

    $spreadsheet = $reader->load($readerInputFile);

    echo "\n\t Reader is ready ". date('Y-m-d H:i:s') ." \n";

    $activeSheet = $spreadsheet->getActiveSheet();
    $sheetData = $activeSheet->toArray();

    echo "\n\t Readers array is ready ". date('Y-m-d H:i:s') ." \n";

    $header = [];

    $resultPath = __DIR__ . "/result/";
    mkdir($resultPath, recursive: true);

    echo "\n\t Start array processed ". date('Y-m-d H:i:s') ." \n";

    foreach ($sheetData as $row => $values) {
        if ($row === 0) {
            $header = $values;

            echo "\n\t Header created ". date('Y-m-d H:i:s') ." \n";

            foreach ($header as $caption) {
                if (!is_null($caption)) {
                    if (!file_exists($resultPath . $caption . ".csv")) {
                        file_put_contents($resultPath . $caption . ".csv", 'external_id,phone,email,gender,birthdate' . "\n");
                        file_put_contents($resultPath . $caption . ".counts", '1');

                        echo "\n\t\t File created $caption.csv ". date('Y-m-d H:i:s') ." \n";
                    }
                }
            }
        } else {
            echo "\n\t\t Row: $row in process ". date('Y-m-d H:i:s') ." \n";

            $writedCols = [];

            foreach ($values as $valuesPosition => $value) {
                if ($valuesPosition > 1) {
                    if (array_search($header[$valuesPosition], $writedCols) === false) {
                        if (!is_null($value) AND $value !== "0") {
                            $writed = false;

                            $colsCount = file_get_contents($resultPath . $header[$valuesPosition] . ".counts");

                            while (!$writed) {
                                $writed = file_put_contents($resultPath . $header[$valuesPosition] . ".csv", "$colsCount,,{$values[1]},,\n", FILE_APPEND);
                            }

                            if ($writed) {
                                array_push($writedCols, $header[$valuesPosition]);
                                file_put_contents($resultPath . $header[$valuesPosition] . ".counts", $colsCount + 1);
                            }
                        }
                    }
                }
            }
        }
    }

    echo "\n\t Array is ended ". date('Y-m-d H:i:s') ." \n";

    $filepart++;
    $readerInputFile = getFileName(filepart: $filepart);
}
