<?php
namespace Core\Shivalik\Managers;

use Core\Shivalik\Entities\Command;
use PHPBackend\Dao\DAOInterface;
use PHPBackend\Dao\DAOException;

/**
 *
 * @author Esaie MUHASA
 *        
 */
interface CommandDAOManager extends DAOInterface {
    
    /**
     * verification des commands deja effectuer par un membre
     * @param int $memberId
     * @param bool $delivered
     * @param int $limit
     * @param int $offset
     * @return bool
     * @throws DAOException
     */
    public function checkByMember (int $memberId, ?bool $delivered=null, ?int $limit = null, int $offset = 0) : bool;
    
    /**
     * Verification des commande faite par un membre en une date ou une intervale de date
     * @param int $memberId
     * @param \DateTime $min
     * @param \DateTime $max
     * @param bool $delivered
     * @param int $limit
     * @param int $offset
     * @return bool
     * @throws DAOException
     */
    public function checkByMemberAtDate (int $memberId, \DateTime $min, ?\DateTime $max = null, ?bool $delivered=null, ?int $limit = null, int $offset = 0) : bool;
    
    /**
     * Renvoie la collection des commande effectuer dans un office
     * @param int $officeId
     * @param bool $delivered
     * @param int $limit
     * @param int $offset
     * @return Command[]
     * @throws DAOException
     */
    public function findByOffice (int $officeId, ?bool $delivered = null, ?int $limit=null, int $offset = 0) : array;
    
    /**
     * Les commandes faite dans un office en une date (ou une itervale de date)
     * @param int $officeId
     * @param \DateTime $min
     * @param \DateTime $max
     * @param bool $delivered
     * @param int $limit
     * @param int $offset
     * @return Command[]
     * @throws DAOException
     */
    public function findByOfficeAtDate (int $officeId, \DateTime $min, ?\DateTime $max = null, ?bool $delivered = null, ?int $limit=null, int $offset = 0) : array;
    
    /** 
     * verifivcation de commande effectuer dans un office
     * @param int $officeId
     * @param bool $delivered
     * @param int $limit
     * @param int $offset
     * @return bool
     * @throws DAOException s'il y a erreur lors de la communication avec le DGBD
     */
    public function checkByOffice (int $officeId, ?bool $delivered = null, ?int $limit=null, int $offset = 0): bool;
    
    /**
     * verification des commandes faite dans un office en une date (une intervale des dates)
     * @param int $officeId
     * @param \DateTime $min
     * @param \DateTime $max
     * @param bool $delivered
     * @param int $limit
     * @param int $offset
     * @return bool
     * @throws DAOException
     */
    public function checkByOfficeAtDate (int $officeId, \DateTime $min, ?\DateTime $max = null, ?bool $delivered = null, ?int $limit=null, int $offset = 0): bool;
    
    /**
     * comptage de commandes faite dans un office
     * @param int $officeId
     * @param bool $delivered
     * @return int
     * @throws DAOException s'ily a erreur la communication avec le systeme de stockage de donnee
     */
    public function countByOffice (int $officeId, ?bool $delivered = null): int;
    
    /**
     * comptage des commandes faite dans un office en une date (une intervale des temps)
     * @param int $officeId
     * @param \DateTime $min
     * @param \DateTime $max
     * @param bool $delivered
     * @return int
     * @throws DAOException
     */
    public function countByOfficeAtDate (int $officeId, \DateTime $min, ?\DateTime $max = null, ?bool $delivered = null): int;
    
    /**
     * Pour singaler que la commande est deja delivrer
     * @param int $id
     * @throws DAOException
     */
    public function deliver (int $id) : void ;
    
    /**
     * comptage de commands deja effectuer par un membre
     * @param int $memberId
     * @param bool $delivered
     * @return int
     * @throws DAOException
     */
    public function countByMember (int $memberId, ?bool $delivered=null) : int;
    
    /**
     * comptage des commandes faite par un membre en une intervale de date
     * @param int $memberId
     * @param \DateTime $min
     * @param \DateTime $max
     * @param bool $delivered
     * @return int
     * @throws DAOException
     */
    public function countByMemberAtDate (int $memberId, \DateTime $min, ?\DateTime $max = null, ?bool $delivered=null) : int;
    
    /**
     * Recuperation des commands d'un membre
     * @param int $memberId
     * @param bool $delivered, filtrage du colone deliveryDate
     * @param int $limit
     * @param int $offset
     * @return Command[]
     * @throws DAOException
     */
    public function findByMember (int $memberId, ?bool $delivered=null, ?int $limit = null, int $offset = 0) : array;
    
    /**
     * Collection des commande faire par une membre en une date ou une intervalle des dates
     * @param int $memberId
     * @param \DateTime $min
     * @param \DateTime $max
     * @param bool $delivered
     * @param int $limit
     * @param int $offset
     * @return Command[]
     * @throws DAOException
     */
    public function findByMemberAtDate (int $memberId, \DateTime $min, ?\DateTime $max = null, ?bool $delivered=null, ?int $limit = null, int $offset = 0) : array;
    
    /**
     * verification des commandes ayant le status en premier parametre
     * @param bool $delivered
     * @param int $limit
     * @param int $offset
     * @return bool
     * @throws DAOException
     */
    public function checkByStatus (bool $delivered=false, ?int $limit = null, int $offset=0) : bool;
    
    /**
     * recuperation des commands ayant le status en parametre
     * @param bool $delivered
     * @param int $limit
     * @param int $offset
     * @return Command[]
     * @throws DAOException
     */
    public function findByStatus (bool $delivered=false, ?int $limit = null, int $offset=0) : array;
    
    /**
     * comptage des commands ayants le status en parametre
     * @param bool $delivered
     * @return int
     */
    public function countByStatus (bool $delivered=false) : int;
}

