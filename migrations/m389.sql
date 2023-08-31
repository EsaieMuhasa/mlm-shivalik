-- Active: 1686405938442@@127.0.0.1@3306@shivalik_db1

SELECT * FROM `MonthlyOrder` WHERE `member` = 389;
SELECT * FROM `SellSheetRow` WHERE `monthlyOrder` IN (SELECT `id` FROM MonthlyOrder WHERE `member` = 389);
SELECT SUM(`unitPrice`) FROM `SellSheetRow` WHERE `monthlyOrder` IN (SELECT `id` FROM MonthlyOrder WHERE `member` = 389);

SELECT * FROM `GradeMember` WHERE `monthlyOrder` IN (SELECT `id` FROM MonthlyOrder WHERE `member` = 389);
SELECT * FROM `MoneyGradeMember` WHERE `gradeMember` = 1706;
-- reduction du montant produit du GradeMember id = 1706: 600 USD => 200 USD