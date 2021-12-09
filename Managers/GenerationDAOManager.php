<?php
namespace Managers;

use Library\AbstractDAOManager;
use Entities\Generation;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class GenerationDAOManager extends AbstractDAOManager
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
    public function forNumber (int $number) : Generation {
        return $this->pdo_uniqueFromTableColumnValue($this->getTableName(), $this->getMetadata()->getName(), 'number', $number);
    }

}

