<?php
namespace Core\Shivalik\Managers;

use Core\Shivalik\Entities\GradeMember;
use Core\Shivalik\Entities\Office;
use Core\Shivalik\Entities\RequestVirtualMoney;
use Core\Shivalik\Entities\VirtualMoney;
use Core\Shivalik\Entities\Withdrawal;
use PHPBackend\DAOException;
use PHPBackend\Dao\DefaultDAOInterface;
use PHPBackend\Dao\UtilitaireSQL;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class OfficeDAOManager extends DefaultDAOInterface
{
	/**
	 * @var MemberDAOManager
	 */
	protected $memberDAOManager;
	
	/**
	 * @var LocalisationDAOManager
	 */
	protected $localisationDAOManager;
	
    /**
     * @param string $name
     * @param int $id
     * @return bool
     */
    public function nameExist (string $name, int $id =-1) : bool {
        return $this->columnValueExist('name', $name, $id);
    }
    
    /**
     * @param int $id
     * @param string $photo
     */
    public function updatePhoto (int $id, string $photo) : void {
        UtilitaireSQL::update($this->getConnection(), $this->getTableName(), array('photo' =>$photo), $id);
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DefaultDAOInterface::findByColumnName()
     */
    public function findByColumnName(string $columnName, $value, bool $forward = true)
    {
        $office = parent::findByColumnName($columnName, $value, $forward);
		if ($office->member != null) {
			$office->setMember($this->memberDAOManager->findById($office->member->id));
		}
		$office->setLocalisation($this->localisationDAOManager->findById($office->localisation->id));        
        return $office;
    }

	/**
	 * revoie l'office dont l'ID est en parametre.
	 * l'office revoyer est chargee au comptet (tout les operations deja faite par l'office sont directement cherger dans l'object retourner) 
	 * @param int|Office $office
	 * @return Office
	 */
	public function load ($office) : Office {
	    /**
	     * @var Office $return
	     */
	    $return = ($office instanceof Office)? $office : $this->findById($office);
	    
	    if ($this->getDaoManager()->getManagerOf(Withdrawal::class)->checkByOffice($return->getId(), null)) {//operation de matching dans le bureau
	        $withdrawals = $this->getDaoManager()->getManagerOf(Withdrawal::class)->findByOffice($return->getId(), null);
	        $return->setWithdrawals($withdrawals);
	    }
	    
	    if ($this->getDaoManager()->getManagerOf(GradeMember::class)->checkByOffice($return->getId())) {//chargement des operations qui touches les membres adherant
	        $return->setOperations($this->getDaoManager()->getManagerOf(GradeMember::class)->findByOffice($return->getId()));
	    }
	    
	    if ($this->getDaoManager()->getManagerOf(VirtualMoney::class)->checkByOffice($return->getId())) {//operations qui touches le monais virtuel pour facilister l'adhesion des membre
	        $return->setVirtualMoneys($this->getDaoManager()->getManagerOf(VirtualMoney::class)->findByOffice($return->getId()));
	    }
	    
	    if ($this->getDaoManager()->getManagerOf(RequestVirtualMoney::class)-checkWaiting($return->getId())) {//Les demandes des monais virtuel effectuer par le proprietaire de l'argent
	        $requests = $this->getDaoManager()->getManagerOf(RequestVirtualMoney::class)->findWaiting($return->getId());
	        $return->setRequests($requests);
	    }
	    
	    return $return;
	}

	/**
     * le membre as-t-elle un bureau
     * @param int $memberId
     * @return bool
     */
    public function hasOffice (int $memberId) : bool {
    	return $this->columnValueExist('member', $memberId);
    }
    
    /**
     * revoie le compte office d'un membre, pour les membre qui ont des comptes
     * @param int $memberId
     * @return Office
     * @throws DAOException
     */
    public function findByMember (int $memberId) : Office {
    	$o = $this->pdo_uniqueFromTableColumnValue($this->getTableName(), $this->getMetadata()->getName(), 'member', $memberId);
    	$o->setLocalisation($this->localisationDAOManager->getForId($o->localisation->id));
    	return $this->findByColumnName("member", $memberId);
    }
    
	/**
	 * {@inheritDoc}
	 * @see \PHPBackend\Dao\DefaultDAOInterface::findAll()
	 */
	public function findAll(?int $limit = null, int $offset = 0) {
		$all = parent::findAll($limit, $offset);
		foreach ($all as $o) {
			if ($o->member != null) {
				$o->setMember($this->memberDAOManager->findById($o->member->id, false));
			}
			$o->setLocalisation($this->localisationDAOManager->findById($o->localisation->id));
		}
		return $all;
	}

}

