--Query1
select import.transactions.id as "transaction id",
       import.batch.merchant_id,
       import.batch.batch_date,
       import.batch.batch_ref_num,
       import.transactions.transaction_amount,
       import.transactions.transaction_date,
       import.transactions.transaction_type,
       import.transactions.transaction_card_type,
       import.transactions.transaction_card_number
from import.batch
         inner join import.transactions on import.batch.id = import.transactions.batch_id
where import.batch.merchant_id = '2264135688721936'
  and import.batch.batch_date = '2018-05-05'
  and import.batch.batch_ref_num = '431731103030341346529798';


--Query2
select import.transactions.transaction_card_type,
       count(import.transactions.id)               as transaction_count,
       sum(import.transactions.transaction_amount) as total_transaction_amount,
       avg(import.transactions.transaction_amount) as average_transaction_amount
from import.transactions
         inner join
     import.batch on import.transactions.batch_id = import.batch.id
where import.batch.merchant_id = '79524081202206784'
  and import.batch.batch_date = '2018-05-05'
  and import.batch.batch_ref_num = '865311392860455095554114'
group by import.transactions.transaction_card_type;


--Query3
select import.merchant.merchant_id,
       import.merchant.merchant_name,
       sum(import.transactions.transaction_amount) as 'total amount',
        count(import.transactions.id) as "number of transactions"
from import.merchant
         inner join import.batch
                    on import.batch.merchant_id = import.merchant.merchant_id
         inner join import.transactions
                    on import.transactions.batch_id = import.batch.id
where import.batch.batch_date between '2015-01-01' and '2029-01-01'
group by
    import.merchant.merchant_name,
    import.merchant.merchant_id
order by
    sum(import.transactions.transaction_amount)
        desc limit 10;