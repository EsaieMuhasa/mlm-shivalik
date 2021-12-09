<?php
namespace Managers;

use Library\AbstractDAOManager;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class CountryDAOManager extends AbstractDAOManager
{
    /**
     * @param string $name
     * @param int $id
     * @return bool
     */
    public function nameExist (string $name, int $id =-1) : bool {
        return $this->columnValueExist('name', $name, $id);
    }
    
    /**
     *
     * @param string $abbreviation
     * @param int $id
     * @return bool
     */
    public function abreviationExist (string $abbreviation, int $id =-1) : bool {
        return $this->columnValueExist('abbreviation', $abbreviation, $id);
    }
    
}

