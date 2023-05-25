
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