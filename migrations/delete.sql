
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