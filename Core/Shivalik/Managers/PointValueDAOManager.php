<?php
namespace Core\Shivalik\Managers;


use Core\Shivalik\Entities\PointValue;
use PHPBackend\Dao\DAOException;

/**
 *
 * @author Esaie MHS
 *        
 */
interface PointValueDAOManager extends BonusDAOManager
{
    
    /**
     * memher has point value on foot(s)
     * <br/> Dans le cas où $product == null, aucun filtrage n'est fait sur l'origine des PV
     * @param int $memberId
     * @param int $foot
     * @param bool $product : true = uniquement les PVs sur l'achat des produits, false = uniquement pour le PVs generer lors de l'inscription au packet
     * @return bool
     * @throws DAOException s'il y a erreur lors de la communication avec le SGBD
     */
    public function checkPv (int $memberId, ?int $foot = null, ?bool $product = null) : bool;
    
    /**
     * verification des points valeurs sur les pieds d'un membre
     * @param int $memberId
     * @param bool $product confert la methode checkPv()
     * @return bool
     * @throws DAOException s'il y a une erreur lors de la communication avec le SGBD
     */
    public function checkLeftPv (int $memberId, ?bool $product = null) : bool;
    
    /**
     * verification des PVs sour le pieds droit du compte d'un membre
     * @param int $memberId
     * @param bool $product confert la methode checkPv()
     * @return bool
     * @throws DAOException si l'erreur surviens lors de la communication avec le SGBD
     */
    public function checkRightPv (int $memberId, ?bool $product = null) : bool;
    
    /**
     * verification des PVs sur le pieds milieux du compte d'un membre
     * @param int $memberId
     * @param bool $product confert la method checkPv()
     * @return bool
     * @throws DAOException s'il y a erreur lors de la communication avec le SGBD
     */
    public function checkMiddlePv (int $memberId, ?bool $product = null) : bool;
    
    /**
     * Evifie si le compte d'un membre a de pv, pour le effors personnel
     * @param int $memberId
     * @return bool
     * @throws DAOException s'il y a erreur lors de la communication avec le SGBD
     */
    public function checkProductPvByMember (int $memberId) : bool;
    
    
    /**
     * renvoie les PVs des effots personne du compte personnel d'un membre
     * @param int $memberId
     * @return PointValue[]
     * @throws DAOException danas le cas où il y a une erreur lors de la communication avec le SGBD
     * ou aucun PVs en efforts personnel pour le compte du membre
     */
    public function findProductPvByMember (int $memberId) : array;

    /**
     * renvoie le PV sur l'un des pieds du membre.
     * @param int $memberId
     * @param int $memberFoot
     * @return PointValue[]
     * @throws DAOException when an error occurred in process to interact with database
     */
    public function findPvByMember (int $memberId, ?int $memberFoot = null, ?bool $product = null) : array;
    
    /**
     * return all left point value of member
     * @param int $memberId
     * @return PointValue[]
     * @throws DAOException when an error occurred in communication at database server process
     */
    public function findLeftByMember (int $memberId, ?bool $product = null) : array;

    /**
     * return all points values at right foot of member account
     * @param int $memberId
     * @param bool $product to see more, same findPvBymember() method
     * @return PointValue[]
     * @throws DAOException wen un error occurred in communication at database
     */
    public function findRightByMember (int $memberId, ?bool $product = null) : array;
    
    /**
     * return all middle points values in member account
     * @param int $memberId
     * @param bool $product to see more, same findPvByMember() method
     * @return PointValue[]
     * @throws DAOException
     */
    public function findMiddleByMember (int $memberId, ?bool $product = null) : array;
    
    /**
     * verification des points valeurs generer par le generateur en parametre
     * @param int $gradMember
     * @param int $limit
     * @param int $offset
     * @return bool
     * @throws DAOException 
     */
    public function checkByGenerator (int $gradMember, ?int $limit = null, int $offset = 0) : bool;
    
    /**
     * renvoie la collection des points valeurs generer par le generateur en parametre
     * @param int $gradMember
     * @param int $limit
     * @param int $offset
     * @return PointValue[]
     * @throws DAOException
     */
    public function findByGenerator (int $gradMember, ?int $limit = null, int $offset = 0) : array;
    
}

