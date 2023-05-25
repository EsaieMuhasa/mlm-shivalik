-- Active: 1680322687513@@127.0.0.1@3306@shivalik_db2

SELECT * FROM V_MonthlyOrder WHERE member IN (603, 653);
-- 603 => 393
-- 653 => 366

SELECT * FROM SellSheetRow WHERE monthlyOrder IN (393, 366)
-- 366 => 1025
-- 366 => 1026
-- 366 => 1037
-- 393 => 1183

SELECT * FROM `GradeMember` WHERE monthlyOrder IN (393, 366)
-- 366 => 1465
-- 393 => 1537