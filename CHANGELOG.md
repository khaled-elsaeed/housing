To do:
- we last time make best practice of file upload dont forget and work 
 first you should update payment paid method from controller and from db you create invoice detail table
 to contain deteails than in invoice and make invoice general only with few details you update ui of payments also 
- you should create reservation second term for student who create their 

------
1- apply migrate to table
2- edit media table constrains make all null and remove unique constraints of meida hash
3- insert payments media to media table with date (INSERT INTO media (path, collection, updated_at, created_at) SELECT payments.receipt_image, 'payments', payments.updated_at, payments.updated_at FROM payments;) 
4- inner join media table on  invoice table to add fk of media to invoice (UPDATE invoices
INNER JOIN payments ON payments.reservation_id = invoices.reservation_id
INNER JOIN media ON media.path = payments.receipt_image
SET invoices.media_id = media.id;
)
5- delte payment table 
6- insert invoice details from invoice table (for each invoice fee and insurance) (
    INSERT INTO invoice_details (invoice_id, category, amount, created_at, updated_at)
SELECT invoices.id, 'fees', '10000', invoices.created_at, invoices.updated_at FROM invoices
UNION
SELECT invoices.id, 'insurance', '10000', invoices.created_at, invoices.updated_at FROM invoices;

)
7- delete amount and category from invoice 
8- make seeder to update db to get file detials final .

-- update student payment controller for upload or add new invoice 