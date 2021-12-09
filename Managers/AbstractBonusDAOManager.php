<?php
namespace Managers;

use Entities\AbstractBonus;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class AbstractBonusDAOManager extends AbstractOperationDAOManager
{
    /**
     *
     * @param int $number
     * @return AbstractBonus[]
     */
    public function forMember (int $memberId, int $limit = -1, int $offset = -1) : array{
        return $this->pdo_fromTableColumnValue($this->getTableName(), $this->getMetadata()->getName(), 'member', $memberId);
    }
    
    /**
     * la personne at-elle aumoin un bonus
     * @param int $memberId
     * @return bool
     */
    public function hasBonus (int $memberId) : bool {
        return $this->columnValueExist('member', $memberId);
    }
}

