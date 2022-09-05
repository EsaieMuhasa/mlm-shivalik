<?php
namespace Core\Shivalik\Managers;


use PHPBackend\Dao\DAOInterface;

/**
 *
 * @author Esaie MHS
 *        
 */
interface SizeDAOManager extends DAOInterface
{
    /**
     * @param string $abbreviation
     * @param int $id
     * @return bool
     */
    public function checkByAbbreviation (string $abbreviation, ?int $id = null) : bool;
    
    /**
     * @param string $name
     * @param int $id
     * @return bool
     */
    public function checkByName (string $name, ?int $id = null) : bool;
}

