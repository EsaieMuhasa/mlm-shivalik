<?php
namespace Core\Shivalik\Managers;


use PHPBackend\Dao\DefaultDAOInterface;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class SizeDAOManager extends DefaultDAOInterface
{
    /**
     * @param string $abbreviation
     * @param int $id
     * @return bool
     */
    public function checkAbbreviation (string $abbreviation, ?int $id = null) : bool{
        return $this->columnValueExist('abbreviation', $abbreviation, $id);
    }
    
    /**
     * @param string $name
     * @param int $id
     * @return bool
     */
    public function checkName (string $name, ?int $id = null) : bool {
        return $this->columnValueExist('name', $name, $id);
    }
}

