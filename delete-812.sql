
SELECT * FROM Member WHERE id = 812;
SELECT * FROM GradeMember WHERE member = 812;
SELECT * FROM BonusGeneration WHERE generator = 951;
SELECT * FROM PointValue  WHERE generator = 951;
SELECT * FROM Withdrawal WHERE member IN (SELECT member FROM BonusGeneration WHERE generator = 951);

DELETE FROM Withdrawal WHERE id IN (786, 784);
DELETE FROM PointValue WHERE generator = 951
DELETE FROM MoneyGradeMember WHERE gradeMember = 951
DELETE FROM BonusGeneration WHERE generator = 951;
DELETE FROM GradeMember WHERE member = 812;
DELETE FROM Member WHERE id = 812;
DELETE FROM Localisation WHERE id = 834;
