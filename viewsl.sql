
CREATE OR REPLACE VIEW V_Stock AS 
    SELECT 
        Stock.id AS id,
        Stock.dateAjout AS dateAjout,
        Stock.dateModif AS dateModif,
        Stock.deleted AS deleted,
        Stock.quantity AS quantity,
        Stock.unitPrice AS unitPrice,
        Stock.comment AS `comment`,
        Stock.expiryDate AS expiryDate,
        Stock.manufacturingDate AS manufacturingDate,
        Stock.product AS product,
        (SELECT SUM(AuxiliaryStock.quantity) FROM AuxiliaryStock WHERE AuxiliaryStock.parent = Stock.id) AS served 
    FROM Stock;

CREATE OR REPLACE VIEW V_Product AS 
    SELECT 
        Product.id AS id,
        Product.dateAjout AS dateAjout,
        Product.dateModif AS dateModif,
        Product.deleted AS deleted,
        Product.name AS `name`,
        Product.defaultUnitPrice AS defaultUnitPrice,
        Product.description AS `description`,
        Product.picture AS picture,
        Product.category AS category,
        Product.packagingSize AS packagingSize,
        (SELECT (SUM(V_Stock.quantity) - SUM(V_Stock.served)) FROM V_Stock WHERE V_Stock.product = Product.id) AS available 
    FROM Product;

CREATE OR REPLACE VIEW V_AuxiliaryStock AS 
    SELECT 
        AuxiliaryStock.id AS id,
        AuxiliaryStock.dateAjout AS dateAjout,
        AuxiliaryStock.dateModif AS dateModif,
        AuxiliaryStock.deleted AS deleted,
        AuxiliaryStock.quantity AS quantity,
        AuxiliaryStock.parent AS parent,
        AuxiliaryStock.office AS office,
        Stock.expiryDate AS expiryDate,
        Stock.manufacturingDate AS manufacturingDate,
        Stock.product AS product,
        Stock.unitPrice AS unitPrice,
        (SELECT SUM(ProductOrdered.quantity) FROM ProductOrdered WHERE ProductOrdered.stock = AuxiliaryStock.id) AS served 
    FROM AuxiliaryStock INNER JOIN Stock ON Stock.id = AuxiliaryStock.parent;

CREATE OR REPLACE VIEW V_ProductOrdered AS
    SELECT 
        ProductOrdered.id AS id,
        ProductOrdered.dateAjout AS dateAjout,
        ProductOrdered.dateModif AS dateModif,
        ProductOrdered.deleted AS deleted,
        ProductOrdered.quantity AS quantity,
        ProductOrdered.reduction AS reduction,
        ProductOrdered.product AS product,
        ProductOrdered.command AS command,
        ProductOrdered.stock AS stock,
        V_AuxiliaryStock.unitPrice AS unitPrice,
        ((V_AuxiliaryStock.unitPrice - ProductOrdered.reduction) * ProductOrdered.quantity) AS amount
    FROM ProductOrdered INNER JOIN V_AuxiliaryStock ON V_AuxiliaryStock.id = ProductOrdered.stock;

CREATE OR REPLACE VIEW V_Command AS
    SELECT 
        Command.id  AS id,
        Command.dateAjout AS dateAjout,
        Command.dateModif AS dateModif,
        Command.deleted AS deleted,
        Command.deliveryDate  AS deliveryDate,
        Command.office AS office,
        Command.officeAdmin AS officeAdmin,
        Command.monthlyOrder AS monthlyOrder,
        Command.member AS member,
        Command.note AS note,

        (SELECT SUM(V_ProductOrdered.unitPrice) FROM V_ProductOrdered WHERE Command.id = V_ProductOrdered.command) AS totalUnitPrice,
        (SELECT (SUM(V_ProductOrdered.quantity)) FROM V_ProductOrdered WHERE Command.id = V_ProductOrdered.command) AS totalQantity,
        (SELECT (SUM(V_ProductOrdered.amount)) FROM V_ProductOrdered WHERE Command.id = V_ProductOrdered.command) AS amount
    FROM Command;-- INNER JOIN V_ProductOrdered ON Command.id = V_ProductOrdered.command;

CREATE OR REPLACE VIEW V_SellSheetRow AS 
    SELECT 
        SellSheetRow.id AS id,
        SellSheetRow.quantity AS quantity,
        SellSheetRow.unitPrice AS unitPrice,
        SellSheetRow.dateAjout AS dateAjout,
        SellSheetRow.dateModif AS dateModif,
        SellSheetRow.product AS product,
        SellSheetRow.monthlyOrder AS monthlyOrder,
        SellSheetRow.office AS office,
        (SELECT SellSheetRow.unitPrice * SellSheetRow.quantity) AS totalPrice
    FROM SellSheetRow;

CREATE OR REPLACE VIEW V_MonthlyOrder AS 
    SELECT 
        MonthlyOrder.id AS id,
        MonthlyOrder.dateAjout AS dateAjout,
        MonthlyOrder.dateModif AS dateModif,
        MonthlyOrder.deleted AS deleted,
        MonthlyOrder.disabilityDate AS disabilityDate,
        MonthlyOrder.member AS member,
        MonthlyOrder.office AS office,
        (
            SELECT (SUM(GradeMember.product))
                FROM GradeMember WHERE GradeMember.monthlyOrder = MonthlyOrder.id
        ) AS used,
        (SELECT (SUM(V_SellSheetRow.totalPrice)) FROM V_SellSheetRow WHERE V_SellSheetRow.monthlyOrder = MonthlyOrder.id) AS amount
    FROM MonthlyOrder;-- INNER JOIN Command ON Command.monthlyOrder = MonthlyOrder.id;

CREATE OR REPLACE VIEW V_RequestVirtualMoney AS
    SELECT DISTINCT
        RequestVirtualMoney.id AS id,
        RequestVirtualMoney.dateAjout AS dateAjout,
        RequestVirtualMoney.dateModif AS dateModif,
        RequestVirtualMoney.deleted AS deleted,
        RequestVirtualMoney.amount AS amount,
        RequestVirtualMoney.product AS product,
        RequestVirtualMoney.affiliation AS affiliation,
        RequestVirtualMoney.office AS office,
        (SELECT VirtualMoney.id FROM VirtualMoney WHERE VirtualMoney.request = RequestVirtualMoney.id) AS `virtual`,
        (SELECT COUNT(id) As nombre FROM Withdrawal WHERE Withdrawal.raport = RequestVirtualMoney.id) AS withdrawalsCount
    FROM RequestVirtualMoney;

CREATE OR REPLACE VIEW V_VirtualMoney AS
    SELECT DISTINCT
        VirtualMoney.id AS id,
        VirtualMoney.dateAjout AS dateAjout,
        VirtualMoney.dateModif AS dateModif,
        VirtualMoney.deleted AS deleted,
        VirtualMoney.amount AS amount,
        VirtualMoney.expected AS expected,
        VirtualMoney.product AS product,
        VirtualMoney.afiliate AS afiliate,
        VirtualMoney.office AS office,
        VirtualMoney.request AS request,
        (
            SELECT (VirtualMoney.product - (SUM(MoneyGradeMember.product))) FROM MoneyGradeMember WHERE MoneyGradeMember.virtualMoney = VirtualMoney.id
        ) AS availableProduct,
        (
            SELECT (VirtualMoney.afiliate - (SUM(MoneyGradeMember.afiliate))) FROM MoneyGradeMember WHERE MoneyGradeMember.virtualMoney = VirtualMoney.id
        ) AS availableAfiliate,
        (
            SELECT SUM(MoneyGradeMember.product) FROM MoneyGradeMember WHERE MoneyGradeMember.virtualMoney = VirtualMoney.id
        ) AS usedProduct,
        (
            SELECT SUM(SellSheetRowVirtualMoney.amount) FROM SellSheetRowVirtualMoney WHERE SellSheetRowVirtualMoney.money = VirtualMoney.id
        ) AS usedPurchase,
        (
            SELECT SUM(MoneyGradeMember.afiliate) FROM MoneyGradeMember WHERE MoneyGradeMember.virtualMoney = VirtualMoney.id
        ) AS usedAfiliate
    FROM VirtualMoney LEFT OUTER JOIN MoneyGradeMember ON VirtualMoney.id = MoneyGradeMember.virtualMoney;

CREATE OR REPLACE VIEW V_Office AS 
    SELECT DISTINCT
        Office.id AS id,
        Office.dateAjout AS dateAjout,
        Office.dateModif AS dateModif,
        Office.central AS central,
        Office.`name` AS `name`,
        Office.photo AS photo,
        Office.localisation AS localisation,
        Office.member AS member,
        Office.visible AS visible,
        (
            SELECT SUM(V_VirtualMoney.availableProduct) FROM V_VirtualMoney WHERE V_VirtualMoney.office = Office.id
        ) AS availableProduct,
        (
            SELECT SUM(V_VirtualMoney.availableAfiliate) FROM V_VirtualMoney WHERE V_VirtualMoney.office = Office.id
        ) AS availableAfiliate
            
    FROM Office LEFT JOIN V_VirtualMoney ON Office.id = V_VirtualMoney.office;
    
CREATE OR REPLACE VIEW V_Account AS 
    SELECT DISTINCT
        Member.id AS id,
        Member.dateAjout AS dateAjout,
        Member.dateModif AS dateModif,
        Member.name AS `name`,
        Member.postName AS postName,
        Member.lastName AS lastName,
        Member.kind AS kind,
        Member.pseudo AS pseudo,
        Member.password AS `password`,
        Member.telephone AS telephone,
        Member.email AS email,
        Member.photo AS photo,
        Member.enable AS `enable`,
        Member.matricule AS matricule,
        Member.parent AS parent,
        Member.sponsor AS sponsor,
        Member.foot AS foot,
        Member.admin AS `admin`,
        Member.office AS `officeAdmin`,
        -- Member.packet AS packet,

        -- ============ PV ==============
        -- pv affiliations
        (
            SELECT SUM(PointValue.value) FROM PointValue 
                WHERE PointValue.member = Member.id AND PointValue.foot = 1 AND PointValue.monthlyOrder IS NULL
        ) AS leftMembershipPv,
        (
            SELECT SUM(PointValue.value) FROM PointValue 
                WHERE PointValue.member = Member.id AND PointValue.foot = 2 AND PointValue.monthlyOrder IS NULL
        ) AS middleMembershipPv,
        (
            SELECT SUM(PointValue.value) FROM PointValue 
                WHERE PointValue.member = Member.id AND PointValue.foot = 3 AND PointValue.monthlyOrder IS NULL
        ) AS rightMembershipPv,

        -- pv reachat =================
        (
            SELECT SUM(PointValue.value) FROM PointValue 
                WHERE PointValue.member = Member.id AND PointValue.foot = 1 AND PointValue.monthlyOrder IS NOT NULL
        ) AS leftProductPv,
        (
            SELECT SUM(PointValue.value) FROM PointValue 
                WHERE PointValue.member = Member.id AND PointValue.foot = 2 AND PointValue.monthlyOrder IS NOT NULL
        ) AS middleProductPv,
        (
            SELECT SUM(PointValue.value) FROM PointValue 
                WHERE PointValue.member = Member.id AND PointValue.foot = 3 AND PointValue.monthlyOrder IS NOT NULL
        ) AS rightProductPv,
        -- ===================== AND PV =============

        -- retrait du frick
        (
            SELECT SUM(Withdrawal.amount) FROM Withdrawal
                WHERE Withdrawal.member = Member.id AND Withdrawal.admin IS NOT NULL
        )AS withdrawal,
        (
            SELECT SUM(Withdrawal.amount) FROM Withdrawal
                WHERE Withdrawal.member = Member.id AND Withdrawal.admin IS NULL
        ) AS withdrawalsRequest,
        -- // retrait du frick

        -- bonus
        (
            SELECT SUM(BonusGeneration.amount) FROM BonusGeneration
                WHERE BonusGeneration.member = Member.id
        )AS soldGeneration,
        (
            SELECT SUM(PurchaseBonus.amount) FROM PurchaseBonus
                WHERE PurchaseBonus.member = Member.id
        )AS purchaseBonus,
        (
            SELECT SUM(OfficeBonus.amount) FROM OfficeBonus
                WHERE OfficeBonus.member = Member.id
        )AS soldOfficeBonus
        -- //bonus

    FROM Member;