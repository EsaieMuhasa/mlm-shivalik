<?php
namespace Core\Shivalik\Managers;

use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\DAOInterface;
use Core\Shivalik\Entities\MoneyGradeMember;
use Core\Shivalik\Entities\Office;
use DateTimeInterface;

/**
 *
 * @author Esaie MUHASA
 *        
 */
interface MoneyGradeMemberDAOManager extends DAOInterface{
    
    /**
     * verifie si le packet d'un membre a ete afilier grace a quel virtuel
     * @param int $gradeMember
     * @return bool
     * @throws DAOException s'il y a erreur lors de la communication avec la BDD
     */
    public function checkByGradeMember (int $gradeMember) : bool;
    
    /**
     * renvoie la collection des references des virtuel qui on ete toucher lors de l'affiliation 
     * d'un membre
     * @param int $gradeMember
     * @return MoneyGradeMember[]
     * @throws DAOException s'il y a erreur lors dela communication avec la BDD ou aucun resultat
     */
    public function findByGradeMember (int $gradeMember) : array;
    
    /**
     * verification de operations qui font reference au virtual en parametre
     * @param int $virtualMoney
     * @param int $offset
     * @return bool
     * @throws DAOException s'il y a erreur lors de la communication avec le SGBD
     */
    public function checkByVirtualMoney (int $virtualMoney, int $offset = 0) : bool;
    
    /**
     * renvoie la collection des operation qui font reference au virtuel en premier parametre
     * @param int $virtualMoney
     * @param int $limit
     * @param int $offset
     * @return MoneyGradeMember[]
     */
    public function findByVirtualMoney (int $virtualMoney, ?int $limit = null, int $offset = 0) : array;

    /**
     * Selection des operations faite pour un office (tous sans restruction ou en une date).
     * @param Office $office
     * @param DateTimeInterface|null $date
     * @param DateTimeInterface|null $max
     * @return MoneyGradeMember[]
     */
    public function findByOffice (Office $office, ?DateTimeInterface $date = null, ?DateTimeInterface $max = null) : array;
}

