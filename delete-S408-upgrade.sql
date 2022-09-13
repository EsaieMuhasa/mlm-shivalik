
SELECT * FROM Member WHERE id = 408;
SELECT * FROM GradeMember WHERE member = 408;
SELECT * FROM BonusGeneration WHERE generator = 988;
SELECT * FROM PointValue  WHERE generator = 988;
--SELECT * FROM Withdrawal WHERE member IN (SELECT member FROM BonusGeneration WHERE generator = 988);

DELETE FROM PointValue WHERE generator = 988
DELETE FROM MoneyGradeMember WHERE gradeMember = 988
DELETE FROM BonusGeneration WHERE generator = 988;
DELETE FROM GradeMember WHERE id = 988;
