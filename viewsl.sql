
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
        (SELECT Stock.expiryDate FROM Stock WHERE Stock.id = AuxiliaryStock.parent) AS expiryDate,
        (SELECT Stock.manufacturingDate FROM Stock WHERE Stock.id = AuxiliaryStock.parent) AS manufacturingDate,
        (SELECT Stock.product FROM Stock WHERE Stock.id = AuxiliaryStock.parent) AS product, 
        (SELECT SUM(ProductOrdered.quantity) FROM ProductOrdered WHERE ProductOrdered.stock = AuxiliaryStock.id) AS served 
    FROM AuxiliaryStock;

