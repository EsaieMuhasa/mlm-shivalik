<?php
namespace Core\Shivalik\Managers;

use Core\Shivalik\Entities\SellSheetRowVirtualMoney;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\DAOInterface;

interface SellSheetRowVirtualMoneyDAOManager extends DAOInterface {

    /**
     * comptage de occurence qui font reference a un item de la fiche d'un membre
     *
     * @param int $sheetId
     * @return int
     * @throws DAOException s'il y erreur lors de la communication avec le SGBD
     */
    public function countBySheet (int $sheetId)  : int;

    /**
     * selectionne toute les occurences qui font reference un item de la fiche d'un membre
     *
     * @param int $sheetId
     * @return SellSheetRowVirtualMoney[]
     * @throws DAOException s'il y a erreur lors de la communication avec le SGBD, ou aucun resultat
     * n'est renvoyer par la requette seletion
     */
    public function findBySheet (int $sheetId) : array;

}