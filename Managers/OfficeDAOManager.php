<?php
namespace Managers;

use Library\AbstractDAOManager;
use Entities\Office;
use Library\DAOException;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class OfficeDAOManager extends AbstractDAOManager
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
        $this->pdo_updateInTable($this->getTableName(), array(
            'photo' =>$photo
        ), $id, false);
    }
    
    /**
	 * {@inheritDoc}
	 * @see \Library\AbstractDAOManager::getForId()
	 */
	public function getForId(int $id, bool $forward = true) {
		$o = parent::getForId ($id, $forward);
		if ($o->member != null) {
			$o->setMember($this->memberDAOManager->getForId($o->member->id));
		}
		$o->setLocalisation($this->localisationDAOManager->getForId($o->localisation->id));
		return $o;
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
    public function forMember (int $memberId) : Office {
    	$o = $this->pdo_uniqueFromTableColumnValue($this->getTableName(), $this->getMetadata()->getName(), 'member', $memberId);
    	$o->setLocalisation($this->localisationDAOManager->getForId($o->localisation->id));
    	return $o;
    }
    
	/**
	 * {@inheritDoc}
	 * @see \Library\AbstractDAOManager::getAll()
	 */
	public function getAll($limit = - 1, $offset = - 1) {
		$all = parent::getAll ($limit, $offset);
		foreach ($all as $o) {
			if ($o->member != null) {
				$o->setMember($this->memberDAOManager->getForId($o->member->id));
			}
			$o->setLocalisation($this->localisationDAOManager->getForId($o->localisation->id));
		}
		return $all;
	}

}

