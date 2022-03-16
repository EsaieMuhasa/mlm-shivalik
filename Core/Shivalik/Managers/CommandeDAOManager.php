<?php
namespace Core\Shivalik\Managers;

use Core\Shivalik\Entities\Commande;
use PHPBackend\Dao\DAOInterface;

/**
 *
 * @author Esaie MUHASA
 *        
 */
interface CommandeDAOManager extends DAOInterface
{
    
    /**
     * verification des commandes deja effectuer par un membre
     * @param int $memberId
     * @param bool $delivered
     * @return bool
     */
    public function checkByMember (int $memberId, ?bool $delivered=null) : bool;
    
    /**
     * comptage de commandes deja effectuer par un membre
     * @param int $memberId
     * @param bool $delivered
     * @return int
     */
    public function countByMember (int $memberId, ?bool $delivered=null) : int;
    
    /**
     * Recuperation des commandes d'un membre
     * @param int $memberId
     * @param bool $delivered, filtrage du colone deliveryDate
     * @return Commande
     */
    public function findByMember (int $memberId, ?bool $delivered=null) : array;
    
    /**
     * 
     * @param bool $delivered
     * @param int $limit
     * @param int $offset
     * @return bool
     */
    public function checkByStatus (bool $delivered=false, ?int $limit = null, int $offset=0) : bool;
    
    /**
     * recuperation des commandes ayant le status en parametre
     * @param bool $delivered
     * @param int $limit
     * @param int $offset
     * @return Commande[]
     */
    public function findByStatus (bool $delivered=false, ?int $limit = null, int $offset=0) : array;
    
    /**
     * comptage des commandes ayants le status en parametre
     * @param bool $delivered
     * @return int
     */
    public function countByStatus (bool $delivered=false) : int;
}

