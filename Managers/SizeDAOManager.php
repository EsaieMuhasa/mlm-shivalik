<?php
namespace Managers;

use Library\AbstractDAOManager;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class SizeDAOManager extends AbstractDAOManager
{
    /**
     * @param string $abbreviation
     * @param int $id
     * @return bool
     */
    public function abbreviationExist (string $abbreviation, int $id = -1) : bool{
        return $this->columnValueExist('abbreviation', $abbreviation, $id);
    }
    
    /**
     * @param string $name
     * @param int $id
     * @return bool
     */
    public function nameExist (string $name, int $id = -1) : bool {
        return $this->columnValueExist('name', $name, $id);
    }
}

