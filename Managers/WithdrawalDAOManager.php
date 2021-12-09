<?php
namespace Managers;

use Entities\Withdrawal;
use Library\DAOException;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class WithdrawalDAOManager extends AbstractOperationDAOManager
{
    
    /**
     * @var OfficeDAOManager
     */
    protected $officeDAOManager;
    
    /**
     * @var MemberDAOManager
     */
    protected $memberDAOManager;
    
    /**
     * 
     * @param int $retraitId
     * @param int $adminId
     */
    public function validate (int $retraitId, int $adminId) : void {
        $this->pdo_updateInTable($this->getTableName(), array(
            'admin' => $adminId
        ), $retraitId, false);
    }
    
    /**
     * {@inheritDoc}
     * @see \Managers\AbstractOperationDAOManager::forMember()
     * @return Withdrawal[]
     */
    public function forMember(int $memberId, int $limit = -1, int $offset = -1): array
    {
        $operations = parent::forMember($memberId, $limit, $offset);
        foreach ($operations as $operation) {
            $operation->setOffice($this->officeDAOManager->getForId($operation->office->id));
        }
        return $operations;
    }
    
    /**
     * Recuperaion des element d'un raport
     * @param int $raportId
     * @param int $limit
     * @param int $offset
     * @return Withdrawal[]
     * @throws DAOException
     */
    public function forRaport(int $raportId, int $limit = -1, int $offset = -1): array{
        /**
         * @var Withdrawal[] $raports
         */
        $raports = $this->pdo_fromTableColumnValue($this->getTableName(), $this->getMetadata()->getName(), 'raport', $raportId, $limit, $offset);
        foreach ($raports as $raport) {
            $raport->setMember($this->memberDAOManager->getForId($raport->getMember()->getId(), false));
        }
        return $raports;
    }

    /**
     * @param int $memberId
     * @return Withdrawal[]
     * @throws DAOException
     */
    public function getRequested (int $memberId) {
        $withdrawels = $this->pdo_fromTableColumnValue($this->getTableName(), $this->getMetadata()->getName(), 'member', $memberId);
        
        foreach ($withdrawels as $withdrawel) {
            $withdrawel->setOffice($this->officeDAOManager->getForId($withdrawel->office->id, false));
            $withdrawel->setMember($this->memberDAOManager->getForId($withdrawel->member->id, false));
        }
        
        return $withdrawels;
    }
    
    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::getCreationHistory()
     */
    public function getCreationHistory(\DateTime $dateMin, \DateTime $dateMax = null, array $filters = array(), $limit = - 1, $offset = - 1)
    {
        /**
         * @var Withdrawal[] $withs
         */
        $withs = parent::getCreationHistory($dateMin, $dateMax, $filters, $limit, $offset);
        foreach ($withs as $withdrawel) {
            $withdrawel->setOffice($this->officeDAOManager->getForId($withdrawel->office->id, false));
            $withdrawel->setMember($this->memberDAOManager->getForId($withdrawel->member->id, false));
        }
        return $withs;
    }
    
    /**
     * 
     * @param int $officeId
     * @param bool $state
     * @param bool $sended
     * @return bool
     */
    public  abstract function hasRequest (int $officeId, ?bool $state = false, ?bool $sended=null) : bool;
    
    /**
     * Redirection d'une demande de matching
     * @param Withdrawal $with
     * @throws DAOException
     */
    public abstract function redirect (Withdrawal $with) : void;
    
    /**
     * 
     * @param int $officeId
     * @param bool $state
     * @param bool $sended
     * @return Withdrawal[]
     */
    public abstract function getOfficeRequests (int $officeId, ?bool $state = false, ?bool $sended=null);

}

