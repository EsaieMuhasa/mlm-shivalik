<?php
namespace Core\Shivalik\Managers;

use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\DAOInterface;

/**
 *
 * @author Esaie MHS
 *        
 */
interface GradeDAOManager extends DAOInterface
{
    /**
     * To update the pack icon
     * @param int $id
     * @param string $icon
     */
    public function updateIcon (int $id, string $icon) : void;
    
    /**
     * verif pack name in DB
     * @param string $name
     * @param int $id
     * @return bool
     */
    public function checkByName (string $name, ?int $id = null) : bool;
    
    /**
     * check packet by percent in DB
     * @param float $percentage
     * @param int $id
     * @return bool
     */
    public function checkByPercentage (float $percentage, ?int $id = null) : bool;
    
    
    /**
     * y-a il un grade superieur a celle en parametrre
     * @param int $id
     * @return bool
     */
    public function checkUpTo (int $id) : bool;
    
    /**
     * 
     * @param int $id
     * @throws DAOException
     * @return \Core\Shivalik\Entities\Grade[]
     */
    public function findUpTo (int $id) : array;
}

