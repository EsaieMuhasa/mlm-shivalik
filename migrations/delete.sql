-- Active: 1686405938442@@127.0.0.1@3306@shivalik_db1

SELECT PointValue WHERE member IS NULL;
SELECT BonusGeneration WHERE member IS NULL;

DELETE FROM PointValue WHERE generator = 1372;
DELETE FROM BonusGeneration WHERE generator = 1372;
DELETE FROM MoneyGradeMember WHERE gradeMember = 1372;
DELETE FROM GradeMember WHERE id = 1372;
DELETE FROM Member WHERE id = 1111;

-- == B1118
DELETE FROM PointValue WHERE generator = 1388;
DELETE FROM BonusGeneration WHERE generator = 1388;
DELETE FROM MoneyGradeMember WHERE gradeMember = 1388;
DELETE FROM GradeMember WHERE id = 1388;
DELETE FROM Member WHERE id = 1118;
-- ==

-- == B1117
DELETE FROM PointValue WHERE generator = 1387;
DELETE FROM BonusGeneration WHERE generator = 1387;
DELETE FROM MoneyGradeMember WHERE gradeMember = 1387;
DELETE FROM GradeMember WHERE id = 1387;
DELETE FROM Member WHERE id = 1117;
-- ==

-- == B1116
DELETE FROM PointValue WHERE generator = 1386;
DELETE FROM BonusGeneration WHERE generator = 1386;
DELETE FROM MoneyGradeMember WHERE gradeMember = 1386;
DELETE FROM GradeMember WHERE id = 1386;
DELETE FROM Member WHERE id = 1116;
-- ==

-- == K1125
DELETE FROM PointValue WHERE generator = 1396;
DELETE FROM BonusGeneration WHERE generator = 1396;
DELETE FROM MoneyGradeMember WHERE gradeMember = 1396;
DELETE FROM GradeMember WHERE id = 1396;
DELETE FROM Member WHERE id = 1125;
-- ==

-- == M1591
DELETE FROM PointValue WHERE generator = 1967;
DELETE FROM BonusGeneration WHERE generator = 1967;
DELETE FROM MoneyGradeMember WHERE gradeMember = 1967;
DELETE FROM GradeMember WHERE id = 1967;
DELETE FROM Member WHERE id = 1591;
-- ==

-- K1684
DELETE FROM PointValue WHERE generator = (SELECT id FROM GradeMember WHERE member = 1684);
DELETE FROM BonusGeneration WHERE generator = (SELECT id FROM GradeMember WHERE member = 1684);
DELETE FROM MoneyGradeMember WHERE gradeMember = (SELECT id FROM GradeMember WHERE member = 1684);
DELETE FROM GradeMember WHERE id = (SELECT id FROM GradeMember WHERE member = 1684);
DELETE FROM Member WHERE id = 1591;
-- ==============

-- suppresion virual
DELETE FROM OfficeBonus WHERE virtualMoney = 257;
DELETE FROM VirtualMoney WHERE id = 257;

-- suppression des comptes
-- ----------------------------------

-- K2543
DELETE FROM PointValue WHERE generator = (SELECT id FROM GradeMember WHERE member = 2543);
DELETE FROM BonusGeneration WHERE generator = (SELECT id FROM GradeMember WHERE member = 2543);
DELETE FROM MoneyGradeMember WHERE gradeMember = (SELECT id FROM GradeMember WHERE member = 2543);
DELETE FROM GradeMember WHERE member = 2543;
DELETE FROM Member WHERE id = 2543;

-- M2544
DELETE FROM PointValue WHERE generator = (SELECT id FROM GradeMember WHERE member = 2544);
DELETE FROM BonusGeneration WHERE generator = (SELECT id FROM GradeMember WHERE member = 2544);
DELETE FROM MoneyGradeMember WHERE gradeMember = (SELECT id FROM GradeMember WHERE member = 2544);
DELETE FROM GradeMember WHERE member = 2544;
DELETE FROM Member WHERE id = 2544;

-- K2545
DELETE FROM PointValue WHERE generator = (SELECT id FROM GradeMember WHERE member = 2545);
DELETE FROM BonusGeneration WHERE generator = (SELECT id FROM GradeMember WHERE member = 2545);
DELETE FROM MoneyGradeMember WHERE gradeMember = (SELECT id FROM GradeMember WHERE member = 2545);
DELETE FROM GradeMember WHERE member = 2545;
DELETE FROM Member WHERE id = 2545;

-- N2546
DELETE FROM PointValue WHERE generator = (SELECT id FROM GradeMember WHERE member = 2546);
DELETE FROM BonusGeneration WHERE generator = (SELECT id FROM GradeMember WHERE member = 2546);
DELETE FROM MoneyGradeMember WHERE gradeMember = (SELECT id FROM GradeMember WHERE member = 2546);
DELETE FROM GradeMember WHERE member = 2546;
DELETE FROM Member WHERE id = 2546;

-- K2547
DELETE FROM PointValue WHERE generator = (SELECT id FROM GradeMember WHERE member = 2547);
DELETE FROM BonusGeneration WHERE generator = (SELECT id FROM GradeMember WHERE member = 2547);
DELETE FROM MoneyGradeMember WHERE gradeMember = (SELECT id FROM GradeMember WHERE member = 2547);
DELETE FROM GradeMember WHERE member = 2547;
DELETE FROM Member WHERE id = 2547;

-- K2548
DELETE FROM PointValue WHERE generator = (SELECT id FROM GradeMember WHERE member = 2548);
DELETE FROM BonusGeneration WHERE generator = (SELECT id FROM GradeMember WHERE member = 2548);
DELETE FROM MoneyGradeMember WHERE gradeMember = (SELECT id FROM GradeMember WHERE member = 2548);
DELETE FROM GradeMember WHERE member = 2548;
DELETE FROM Member WHERE id = 2548;

-- K2549
DELETE FROM PointValue WHERE generator = (SELECT id FROM GradeMember WHERE member = 2549);
DELETE FROM BonusGeneration WHERE generator = (SELECT id FROM GradeMember WHERE member = 2549);
DELETE FROM MoneyGradeMember WHERE gradeMember = (SELECT id FROM GradeMember WHERE member = 2549);
DELETE FROM GradeMember WHERE member = 2549;
DELETE FROM Member WHERE id = 2549;

-- K2550
DELETE FROM PointValue WHERE generator = (SELECT id FROM GradeMember WHERE member = 2550);
DELETE FROM BonusGeneration WHERE generator = (SELECT id FROM GradeMember WHERE member = 2550);
DELETE FROM MoneyGradeMember WHERE gradeMember = (SELECT id FROM GradeMember WHERE member = 2550);
DELETE FROM GradeMember WHERE member = 2550;
DELETE FROM Member WHERE id = 2550;

-- K2551
DELETE FROM PointValue WHERE generator = (SELECT id FROM GradeMember WHERE member = 2551);
DELETE FROM BonusGeneration WHERE generator = (SELECT id FROM GradeMember WHERE member = 2551);
DELETE FROM MoneyGradeMember WHERE gradeMember = (SELECT id FROM GradeMember WHERE member = 2551);
DELETE FROM GradeMember WHERE member = 2551;
DELETE FROM Member WHERE id = 2551;


-- ===============================================================================================
SELECT * FROM `MonthlyOrder` WHERE member = 334;-- 155
SELECT * FROM `SellSheetRow` WHERE monthlyOrder = 155;
DELETE FROM `SellSheetRowVirtualMoney` WHERE `sheet` = 3414;
DELETE FROM `SellSheetRow` WHERE `id` = 3414;
DELETE FROM `SellSheetRowVirtualMoney` WHERE `sheet` = 3413;
DELETE FROM `SellSheetRow` WHERE `id` = 3413;
-- SellSheetRowVirtualMoney
-- ===============================================================================================
SELECT * FROM `Member` WHERE id = 2738;
SELECT * FROM `GradeMember` WHERE member = 2738;-- 3436
SELECT * FROM `MoneyGradeMember` WHERE gradeMember = 3436;
SELECT * FROM `Office` WHERE `id` = 6;-- member: 576
SELECT * FROM `MoneyGradeMember` WHERE virtualMoney IN (SELECT id FROM VirtualMoney WHERE office = 6) ORDER BY dateAjout DESC;


DELETE FROM `SellSheetRow` WHERE monthlyOrder = 757;
DELETE FROM `SellSheetRowVirtualMoney` WHERE `sheet` = 757;
DELETE FROM `MonthlyOrder` WHERE member = 2341;
-- ============
SELECT * FROM `MonthlyOrder` WHERE member = 2341;
DELETE FROM `SellSheetRow` WHERE monthlyOrder = 757;
DELETE FROM `SellSheetRowVirtualMoney` WHERE `sheet` = 757;


-- =============================================================
DELETE FROM PointValue WHERE generator = (SELECT id FROM GradeMember WHERE member = 2808);
DELETE FROM BonusGeneration WHERE generator = (SELECT id FROM GradeMember WHERE member = 2808);
DELETE FROM MoneyGradeMember WHERE gradeMember = (SELECT id FROM GradeMember WHERE member = 2808);
DELETE FROM GradeMember WHERE member = 2808;
DELETE FROM Member WHERE id = 2808;
-- ============================================================

-- migration virtual
SELECT * FROM MonthlyOrder WHERE member = 1532; -- 702
INSERT INTO `MonthlyOrder` (`id`, `dateAjout`, `dateModif`, `deleted`, `disabilityDate`, `member`, `manualAmount`, `office`) VALUES (NULL, '2023-07-14 22:25:00', NULL, '0', NULL, '2211', '0.00', '11');

-- suppression du compte, demande d'izrael
SET AUTOCOMMIT = 0;
START TRANSACTION;
DELETE FROM PointValue WHERE generator = (SELECT id FROM GradeMember WHERE member = 3155);
DELETE FROM BonusGeneration WHERE generator = (SELECT id FROM GradeMember WHERE member = 3155);
DELETE FROM MoneyGradeMember WHERE gradeMember = (SELECT id FROM GradeMember WHERE member = 3155);
DELETE FROM GradeMember WHERE member = 3155;
DELETE FROM Member WHERE id = 3155;
COMMIT;
-- ================

-- sppression, demande du chef
SET AUTOCOMMIT = 0;
START TRANSACTION;
DELETE FROM PointValue WHERE generator = (SELECT id FROM GradeMember WHERE member = 3139);
DELETE FROM BonusGeneration WHERE generator = (SELECT id FROM GradeMember WHERE member = 3139);
DELETE FROM MoneyGradeMember WHERE gradeMember = (SELECT id FROM GradeMember WHERE member = 3139);
DELETE FROM GradeMember WHERE member = 3139;
DELETE FROM Member WHERE id = 3139;
COMMIT;
-- ======================

SELECT * FROM MonthlyOrder WHERE member = 1172;--645
SELECT * FROM SellSheetRow WHERE monthlyOrder = (SELECT id FROM MonthlyOrder WHERE member = 1172);

SELECT * FROM MonthlyOrder WHERE member = 2095;--802
SELECT * FROM SellSheetRow WHERE monthlyOrder = (SELECT id FROM MonthlyOrder WHERE member = 2095);


SET AUTOCOMMIT = 0;
START TRANSACTION;
DELETE FROM PointValue WHERE generator = (SELECT id FROM GradeMember WHERE member = 3167);
DELETE FROM BonusGeneration WHERE generator = (SELECT id FROM GradeMember WHERE member = 3167);
DELETE FROM MoneyGradeMember WHERE gradeMember = (SELECT id FROM GradeMember WHERE member = 3167);
DELETE FROM GradeMember WHERE member = 3167;
DELETE FROM Member WHERE id = 3167;
COMMIT;


SET AUTOCOMMIT = 0;
START TRANSACTION;
DELETE FROM `SellSheetRowVirtualMoney` WHERE `sheet` IN(3772, 3763, 4337);
DELETE FROM `SellSheetRow` WHERE id IN(3772, 3763, 4337);
COMMIT;


SET AUTOCOMMIT = 0;
START TRANSACTION;
DELETE FROM PointValue WHERE generator = (SELECT id FROM GradeMember WHERE member = 3585);
DELETE FROM BonusGeneration WHERE generator = (SELECT id FROM GradeMember WHERE member = 3585);
DELETE FROM MoneyGradeMember WHERE gradeMember = (SELECT id FROM GradeMember WHERE member = 3585);
DELETE FROM GradeMember WHERE member = 3585;
DELETE FROM Member WHERE id = 3585;
COMMIT;

SET AUTOCOMMIT = 0;
START TRANSACTION;
DELETE FROM PointValue WHERE generator = (SELECT id FROM GradeMember WHERE member = 3033);
DELETE FROM BonusGeneration WHERE generator = (SELECT id FROM GradeMember WHERE member = 3033);
DELETE FROM MoneyGradeMember WHERE gradeMember = (SELECT id FROM GradeMember WHERE member = 3033);
DELETE FROM GradeMember WHERE member = 3033;
DELETE FROM Member WHERE id = 3033;
COMMIT;

SET AUTOCOMMIT = 0;
START TRANSACTION;
DELETE FROM PointValue WHERE generator = (SELECT id FROM GradeMember WHERE member = 3590);
DELETE FROM BonusGeneration WHERE generator = (SELECT id FROM GradeMember WHERE member = 3590);
DELETE FROM MoneyGradeMember WHERE gradeMember = (SELECT id FROM GradeMember WHERE member = 3590);
DELETE FROM GradeMember WHERE member = 3590;
DELETE FROM Member WHERE id = 3590;
COMMIT;


SET AUTOCOMMIT = 0;
START TRANSACTION;

-- K138
-- GradeMember 135 AND 2923 ruby

UPDATE `GradeMember` SET `closeDate` = NOW(), `enable` = 0, `dateModif` = NOW() WHERE member = 138 AND `closeDate` IS NULL;
INSERT INTO GradeMember (`initDate`, `product`, `membership`, `dateAjout`, `old`, `member`, `grade`, `office`, `enable`) 
VALUES(NOW(), 600, 0, NOW(), 2923, 138, 5, 1, 1);

-- ================================
-- K153
-- GradeMember 149 AND 521 rudy
UPDATE `GradeMember` SET `closeDate` = NOW(), `enable` = 0, `dateModif` = NOW() WHERE member = 153 AND `closeDate` IS NULL;
INSERT INTO GradeMember (`initDate`, `product`, `membership`, `dateAjout`, `old`, `member`, `grade`, `office`, `enable`) 
VALUES(NOW(), 600, 0, NOW(), 521, 153, 5, 1, 1);


-- ================================
-- K1060
-- GradeMember 1315 royal
UPDATE `GradeMember` SET `closeDate` = NOW(), `enable` = 0, `dateModif` = NOW() WHERE member = 1060 AND `closeDate` IS NULL;
INSERT INTO GradeMember (`initDate`, `product`, `membership`, `dateAjout`, `old`, `member`, `grade`, `office`, `enable`) 
VALUES(NOW(), 700, 0, NOW(), 1315, 1060, 5, 1, 1);

SELECT * FROM `GradeMember` WHERE `member` IN (138, 153, 1060) ORDER BY `member`;
COMMIT;


SET AUTOCOMMIT = 0;
START TRANSACTION;

-- M2482
-- GradeMember 3104 AND 3110 ruby

UPDATE `GradeMember` SET `closeDate` = NOW(), `enable` = 0, `dateModif` = NOW() WHERE member = 2482 AND `closeDate` IS NULL;
INSERT INTO GradeMember (`initDate`, `product`, `membership`, `dateAjout`, `old`, `member`, `grade`, `office`, `enable`) 
VALUES(NOW(), 400, 0, NOW(), 3110, 2482, 5, 1, 1);
SELECT * FROM `GradeMember` WHERE `member` = 2482;
COMMIT;

SET AUTOCOMMIT = 0;
START TRANSACTION;
DELETE FROM PointValue WHERE generator = (SELECT id FROM GradeMember WHERE member = 3934);
DELETE FROM BonusGeneration WHERE generator = (SELECT id FROM GradeMember WHERE member = 3934);
DELETE FROM MoneyGradeMember WHERE gradeMember = (SELECT id FROM GradeMember WHERE member = 3934);
DELETE FROM GradeMember WHERE member = 3934;
DELETE FROM Member WHERE id = 3934;
COMMIT;