<?php
namespace Core\Shivalik\Managers;


use PHPBackend\Dao\DefaultDAOInterface;
use Core\Shivalik\Entities\RaportWithdrawal;
use PHPBackend\Dao\UtilitaireSQL;

/**
 *
 * @author Esaie MUHASA
 *        
 */
abstract class RaportWithdrawalDAOManager extends DefaultDAOInterface
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
    public abstract function checkRaportInInterval (\DateTime $dateMin, \DateTime $dateMax, ?int $officeId = null) : bool;
    
    /**
     * Renvoie le rapport dans l'intervale en parametre
     * @param \DateTime $dateMin
     * @param \DateTime $dateMax
     * @param int $officeId
     * @return RaportWithdrawal[]
     */
    public abstract function findRaportInInterval (\DateTime $dateMin, \DateTime $dateMax, ?int $officeId = null);
    
    /**
     * y-a-il aumon un raport pour le bureau en parametre????
     * @param int $officeId
     * @return bool
     */
    public function checkByOffice (int $officeId) : bool{
        return $this->columnValueExist('office', $officeId);
    }
    
    
    /**
     * renvoie les rapports deja envoyer au nom d'un bureau
     * @param int $officeId
     * @param int $limit
     * @param int $offset
     * @return RaportWithdrawal[]
     */
    public function findByOffice(int $officeId, ?int $limit = null, int $offset = 0) {
        return UtilitaireSQL::findAll($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), self::FIELD_DATE_AJOUT, array("office" => $officeId), $limit, $offset);
    }
}

