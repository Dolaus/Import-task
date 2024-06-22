<?php

require_once 'src\App\Service\ImportService.php';
require_once 'src\App\database\DBConection.php';
require_once 'config\DB.php';

use App\database\DBConnection;
use App\Service\ImportService;

$mapping = [
    'MERCHANT_ID' => 'Merchant ID',
    'MERCHANT_NAME' => 'Merchant Name',
    'BATCH_DATE' => 'Batch Date',
    'BATCH_REFERENCE_NUM' => 'Batch Reference Number',
    'TRANSACTION_DATE' => 'Transaction Date',
    'TRANSACTION_TYPE' => 'Transaction Type',
    'TRANSACTION_CARD_TYPE' => 'Transaction Card Type',
    'TRANSACTION_CARD_NUMBER' => 'Transaction Card Number',
    'TRANSACTION_AMOUNT' => 'Transaction Amount',
];

$csvFile = 'report.csv';

try {
    $startTime = microtime(true);
    $startMemory = memory_get_usage();

    $pdo = DBConnection::getConnection();
    echo "Connected successfully\n";

    $importer = new ImportService($pdo);

    if (($handle = fopen($csvFile, 'r')) !== false) {
        $header = fgetcsv($handle);

        while (($row = fgetcsv($handle)) !== false) {
            $combinedRow = array_combine($header, $row);

            $importer->importMerchant($combinedRow, $mapping);
            $importer->importBatches($combinedRow, $mapping);
        }
        fclose($handle);
    } else {
        echo "Failed to open file";
    }

    $endTime = microtime(true);
    $executionTime = $endTime - $startTime;
    $endMemory = memory_get_usage();
    $memoryUsed = $endMemory - $startMemory;
    $memoryUsedMB = $memoryUsed / (1024 * 1024);

    echo "Execution time: " . $executionTime . " seconds\n";
    echo "Memory used: " . $memoryUsedMB . " MB\n";
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}
