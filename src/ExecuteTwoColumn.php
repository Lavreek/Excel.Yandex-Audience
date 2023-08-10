<?php
ini_set('memory_limit', '12288M');

define('ROOT', dirname(__DIR__));

require_once ROOT . "/vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\IOFactory;

$fileCount = 1;
$fileInput = "";
$readerInputFile = ROOT . "/resources/$fileInput";

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
        continue;
    } else {
        echo "\n\t\t Row: $row in process ". date('Y-m-d H:i:s') ." \n";

        if (!is_null($values[0]) AND !is_null($values[1])) {
            if (!file_exists($resultPath . $values[1] . ".csv")) {
                file_put_contents($resultPath . $values[1] . ".csv", 'external_id,phone,email,gender,birthdate' . "\n");
                file_put_contents($resultPath . $values[1] . ".counts", '1');

                echo "\n\t\t File created {$values[1]}.csv ". date('Y-m-d H:i:s') ." \n";
            }

            $writed = false;

            $colsCount = file_get_contents($resultPath . $values[1] . ".counts");

            while (!$writed) {
                $writed = file_put_contents($resultPath . $values[1] . ".csv", "$colsCount,,{$values[0]},,\n", FILE_APPEND);
            }

            if ($writed) {
                file_put_contents($resultPath . $values[1] . ".counts", $colsCount + 1);
            }
        }
    }
}

echo "\n\t Array is ended ". date('Y-m-d H:i:s') ." \n";
