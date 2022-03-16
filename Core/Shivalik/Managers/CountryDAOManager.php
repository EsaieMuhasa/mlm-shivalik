<?php
namespace Core\Shivalik\Managers;

use PHPBackend\Dao\DAOInterface;

/**
 *
 * @author Esaie MHS
 *        
 */
interface CountryDAOManager extends DAOInterface
{
    /**
     * @param string $name
     * @param int $id
     * @return bool
     */
    public function checkByName (string $name, ?int $id = null) : bool;
    
    /**
     *verification de l'existance de l'abreviation dans la bse de donnee
     * @param string $abbreviation
     * @param int $id
     * @return bool
     */
    public function checkByAbreviation (string $abbreviation, ?int $id = null) : bool;
    
}

