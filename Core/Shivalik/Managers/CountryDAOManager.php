<?php
namespace Core\Shivalik\Managers;

use PHPBackend\Dao\DefaultDAOInterface;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class CountryDAOManager extends DefaultDAOInterface
{
    /**
     * @param string $name
     * @param int $id
     * @return bool
     */
    public function checkByName (string $name, ?int $id = null) : bool {
        return $this->columnValueExist('name', $name, $id);
    }
    
    /**
     *
     * @param string $abbreviation
     * @param int $id
     * @return bool
     */
    public function checkByAbreviation (string $abbreviation, ?int $id = null) : bool {
        return $this->columnValueExist('abbreviation', $abbreviation, $id);
    }
    
}

