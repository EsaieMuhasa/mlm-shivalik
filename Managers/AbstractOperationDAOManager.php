<?php
namespace Managers;

use Library\AbstractDAOManager;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class AbstractOperationDAOManager extends AbstractDAOManager
{
    /**
     * @param int $memberId
     * @return bool
     */
    public function hasOperation (int $memberId) :bool {
        return $this->columnValueExist('member', $memberId);
    }
    /**
     * @param int $memberId
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function forMember (int $memberId, int $limit = -1, int $offset = -1) : array {
        return $this->pdo_fromTableColumnValue($this->getTableName(), $this->getMetadata()->getName(), 'member', $memberId, $limit, $offset);
    }

}

