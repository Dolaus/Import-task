CREATE TABLE Merchant
(
    id            BIGINT AUTO_INCREMENT PRIMARY KEY,
    merchant_id   BIGINT       NOT NULL,
    merchant_name VARCHAR(255) NOT NULL,
    UNIQUE KEY (merchant_id)
);

CREATE TABLE Batch
(
    id            BIGINT AUTO_INCREMENT PRIMARY KEY,
    merchant_id   BIGINT       NOT NULL,
    batch_date    DATE         NOT NULL,
    batch_ref_num VARCHAR(255) NOT NULL,
    UNIQUE KEY (merchant_id, batch_date, batch_ref_num),
    FOREIGN KEY (merchant_id) REFERENCES Merchant (merchant_id)
);

CREATE TABLE Transactions
(
    id                      BIGINT AUTO_INCREMENT PRIMARY KEY,
    batch_id                BIGINT         NOT NULL,
    transaction_amount      DECIMAL(10, 2) NOT NULL,
    transaction_date        DATE           NOT NULL,
    transaction_type        VARCHAR(20)    NOT NULL,
    transaction_card_type   VARCHAR(20)    NOT NULL,
    transaction_card_number VARCHAR(45)    NOT NULL,
    FOREIGN KEY (batch_id) REFERENCES Batch (id)
);
