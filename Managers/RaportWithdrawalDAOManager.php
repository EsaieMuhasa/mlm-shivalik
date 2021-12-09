<?php
namespace Managers;

use Library\AbstractDAOManager;
use Entities\RaportWithdrawal;

/**
 *
 * @author Esaie MUHASA
 *        
 */
abstract class RaportWithdrawalDAOManager extends AbstractDAOManager
{
    /**
     * Peut-ont envoyer le rapport
     * @param int $officeId
     * @return bool
     */
    public abstract function canSendRaport(int $officeId) : bool;
    
    /**
     * y-a-il aumoin un rapport dans cette intervalle???
     * @param \DateTime $dateMin
     * @param \DateTime $dateMax
     * @param int $officeId
     * @return boolean
     */
    public abstract function hasRaportInInterval (\DateTime $dateMin, \DateTime $dateMax, ?int $officeId = null) : bool;
    
    /**
     * Renvoie le rapport dans l'intervale en parametre
     * @param \DateTime $dateMin
     * @param \DateTime $dateMax
     * @param int $officeId
     * @return RaportWithdrawal[]
     */
    public abstract function getRaportInInterval (\DateTime $dateMin, \DateTime $dateMax, ?int $officeId = null);
    
    /**
     * y-a-il aumon un raport pour le bureau en parametre????
     * @param int $officeId
     * @return bool
     */
    public function hasRaport (int $officeId) : bool{
        return $this->columnValueExist('office', $officeId);
    }
    
    
    /**
     * revoie les rapports deja envoyer au nom d'un bureau
     * @param int $officeId
     * @param int $limit
     * @param int $offset
     * @return RaportWithdrawal[]
     */
    public function getRaports (int $officeId, $limit=-1, $offset=-1) {
        return $this->pdo_fromTableColumnValue($this->getTableName(), $this->getMetadata()->getName(), 'office', $officeId, $limit, $offset);
    }
}

