<?php
namespace Core\Shivalik\Managers;

use Core\Shivalik\Entities\Generation;
use PHPBackend\Dao\DefaultDAOInterface;
use PHPBackend\Dao\UtilitaireSQL;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class GenerationDAOManager extends DefaultDAOInterface
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
    
    /**
     * 
     * @param int $number
     * @param int $id
     * @return bool
     */
    public function numberExist (int $number, int $id =-1) : bool {
        return $this->columnValueExist('number', $number, $id);
    }
    
    /**
     *
     * @param int $number
     * @return Generation
     */
    public function findByNumber (int $number) : Generation {
        return UtilitaireSQL::findUnique($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), "number", $number);
    }

}

