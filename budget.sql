CREATE TABLE
    `BudgetConfig` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `dateAjout` DATETIME NOT NULL,
        `dateModif` DATETIME NULL DEFAULT NULL,
        `deleted` BOOLEAN NOT NULL DEFAULT FALSE,
        `available` BOOLEAN NOT NULL DEFAULT TRUE,
        PRIMARY KEY (`id`)
    ) ENGINE = InnoDB;

CREATE TABLE
    `ConfigElement` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `dateAjout` DATETIME NOT NULL,
        `dateModif` DATETIME NULL DEFAULT NULL,
        `deleted` BOOLEAN NULL DEFAULT FALSE,
        `percent` FLOAT NOT NULL,
        `config` INT UNSIGNED NOT NULL,
        `rubric` INT UNSIGNED NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE = InnoDB;

CREATE TABLE
    `SubConfigElement` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `dateAjout` DATETIME NOT NULL,
        `dateModif` DATETIME NULL DEFAULT NULL,
        `deleted` BOOLEAN NULL DEFAULT FALSE,
        `percent` FLOAT NOT NULL,
        `config` INT UNSIGNED NOT NULL,
        `rubric` INT UNSIGNED NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE = InnoDB;

CREATE TABLE
    `Output` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `dateAjout` DATETIME NOT NULL,
        `dateModif` DATETIME NULL DEFAULT NULL,
        `deleted` BOOLEAN NOT NULL DEFAULT FALSE,
        `amount` FLOAT NOT NULL,
        `rubric` INT UNSIGNED NOT NULL,
        `description` TEXT DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE = InnoDB;

ALTER TABLE `SubConfigElement`
ADD
    CONSTRAINT `fk_SubConfigElement_rubric` FOREIGN KEY (`rubric`) REFERENCES `BudgetRubric`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `SubConfigElement`
ADD
    CONSTRAINT `fk_SubConfigElement_config` FOREIGN KEY (`config`) REFERENCES `ConfigElement`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `ConfigElement`
ADD
    CONSTRAINT `fk_ConfigElement_rubric` FOREIGN KEY (`rubric`) REFERENCES `BudgetRubric`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `ConfigElement`
ADD
    CONSTRAINT `fk_ConfigElement_config` FOREIGN KEY (`config`) REFERENCES `BudgetConfig`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
    
ALTER TABLE `virtualmoney`
ADD
    CONSTRAINT `fk_VirualMoney_config` FOREIGN KEY (`config`) REFERENCES `budgetconfig`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `output`
ADD
    CONSTRAINT `fk_Output_rubric` FOREIGN KEY (`rubric`) REFERENCES `budgetrubric`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;