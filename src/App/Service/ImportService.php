<?php

namespace App\Service;

use PDO;
use PDOException;

class ImportService
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function importMerchant(array $row, array $mapping): void
    {
        try {
            $this->pdo->beginTransaction();

            $checkSql = 'SELECT COUNT(*) FROM MERCHANT WHERE merchant_id = :merchant_id';
            $checkStmt = $this->pdo->prepare($checkSql);
            $checkStmt->bindParam(':merchant_id', $row[$mapping['MERCHANT_ID']]);
            $checkStmt->execute();
            $exists = $checkStmt->fetchColumn();

            if ($exists > 0) {
                $this->pdo->commit();
                return;
            }

            $sql = 'INSERT INTO MERCHANT (merchant_id, merchant_name) VALUES (:merchant_id, :merchant_name)';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':merchant_id', $row[$mapping['MERCHANT_ID']]);
            $stmt->bindParam(':merchant_name', $row[$mapping['MERCHANT_NAME']]);
            $stmt->execute();

            $this->pdo->commit();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function importBatches(array $row, array $mapping): void
    {
        try {
            $this->pdo->beginTransaction();

            $checkSql = 'SELECT id FROM Batch WHERE merchant_id = :merchant_id AND batch_date = :batch_date AND batch_ref_num = :batch_ref_num';
            $checkStmt = $this->pdo->prepare($checkSql);
            $checkStmt->bindParam(':merchant_id', $row[$mapping['MERCHANT_ID']]);
            $checkStmt->bindParam(':batch_date', $row[$mapping['BATCH_DATE']]);
            $checkStmt->bindParam(':batch_ref_num', $row[$mapping['BATCH_REFERENCE_NUM']]);
            $checkStmt->execute();
            $batchId = $checkStmt->fetchColumn();

            if ($batchId !== false) {
                $this->pdo->commit();
                $this->importTransaction($row, $mapping, $batchId);
                return;
            }

            $sql = 'INSERT INTO BATCH (merchant_id, batch_date, batch_ref_num) VALUES (:merchant_id, :batch_date, :batch_ref_num)';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':merchant_id', $row[$mapping['MERCHANT_ID']]);
            $stmt->bindParam(':batch_date', $row[$mapping['BATCH_DATE']]);
            $stmt->bindParam(':batch_ref_num', $row[$mapping['BATCH_REFERENCE_NUM']]);
            $stmt->execute();

            $lastInsertBatchId = $this->pdo->lastInsertId();

            $this->pdo->commit();
            $this->importTransaction($row, $mapping, $lastInsertBatchId);
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function importTransaction(array $row, array $mapping, int $batchId): void
    {
        try {
            $this->pdo->beginTransaction();

            $sql = 'INSERT INTO Transactions (batch_id, transaction_date, transaction_type, transaction_card_type, transaction_card_number, transaction_amount) VALUES (:batch_id, :transaction_date, :transaction_type, :transaction_card_type, :transaction_card_number, :transaction_amount)';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':batch_id', $batchId);
            $stmt->bindParam(':transaction_date', $row[$mapping['TRANSACTION_DATE']]);
            $stmt->bindParam(':transaction_type', $row[$mapping['TRANSACTION_TYPE']]);
            $stmt->bindParam(':transaction_card_type', $row[$mapping['TRANSACTION_CARD_TYPE']]);
            $stmt->bindParam(':transaction_card_number', $row[$mapping['TRANSACTION_CARD_NUMBER']]);
            $stmt->bindParam(':transaction_amount', $row[$mapping['TRANSACTION_AMOUNT']]);
            $stmt->execute();

            $this->pdo->commit();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
