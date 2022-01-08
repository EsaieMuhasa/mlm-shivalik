<?php
namespace Core\Shivalik\Managers;

use Core\Shivalik\Entities\Withdrawal;
use PHPBackend\DAOException;
use PHPBackend\Dao\UtilitaireSQL;

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
     * validation du retrait d'argent par un administrateur d'un office
     * @param int $d
     * @param int $adminId
     */
    public function validate (int $id, int $adminId) : void {
        UtilitaireSQL::update($this->getConnection(), $this->getTableName(), array('admin' => $adminId), $id);
    }
    

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\AbstractOperationDAOManager::findByMember()
     * @return Withdrawal[]
     */
    public function findByMember(int $memberId, ?int $limit = null, int $offset = 0): array
    {
        $operations = parent::findByMember($memberId, $limit, $offset);
        foreach ($operations as $operation) {
            $operation->setOffice($this->officeDAOManager->findById($operation->office->id, false));
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
    public function findByRapport(int $raportId, ?int $limit = null, int $offset = 0): array{
        /**
         * @var Withdrawal[] $raports
         */
        $raports = UtilitaireSQL::findAll($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), self::FIELD_DATE_AJOUT, true, array('rapport' => $raportId), $limit, $offset);
        foreach ($raports as $raport) {
            $raport->setMember($this->memberDAOManager->findById($raport->getMember()->getId(), false));
        }
        return $raports;
    }
    
    /**
     * comptage des opperations qui ont ete envoyer dans un rappot
     * @param int $rapportId
     * @return int
     */
    public function countByRapport (int $rapportId) : int  {
        return UtilitaireSQL::count($this->getConnection(), $this->getTableName(), array('rapport' => $rapportId));
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DefaultDAOInterface::findByCreationHistory()
     */
    public function findByCreationHistory(\DateTime $dateMin, \DateTime $dateMax = null, array $filters = array(), $limit = - 1, $offset = - 1)
    {
        /**
         * @var Withdrawal[] $withs
         */
        $withs = parent::findByCreationHistory($dateMin, $dateMax, $filters, $limit, $offset);
        foreach ($withs as $withdrawel) {
            $withdrawel->setOffice($this->officeDAOManager->findById($withdrawel->office->id, false));
            $withdrawel->setMember($this->memberDAOManager->findById($withdrawel->member->id, false));
        }
        return $withs;
    }
    
    /**
     * verifie si l'office as des operations qui y ont transiter
     * @param int $officeId
     * @param bool $state
     * @param bool $sended
     * @return bool
     */
    public  abstract function checkByOffice (int $officeId, ?bool $state = false, ?bool $sended=null, ?int $limit = null, int $offset = 0) : bool;
    
    /**
     * renvoie les operations qui ont transiter par l'office en premier parametre
     * @param int $officeId
     * @param bool $state
     * @param bool $sended
     * @param int $limit
     * @param int $offset
     * @return Withdrawal[]
     */
    public abstract function findByOffice (int $officeId, ?bool $state = false, ?bool $sended=null, ?int $limit = null, int $offset = 0);

    /**
     * Redirection d'une demande de matching
     * @param Withdrawal $with
     * @throws DAOException
     */
    public abstract function redirect (Withdrawal $with) : void;

}
