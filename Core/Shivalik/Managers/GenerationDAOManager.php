<?php
namespace Core\Shivalik\Managers;

use Core\Shivalik\Entities\Generation;
use PHPBackend\Dao\DAOInterface;

/**
 *
 * @author Esaie MHS
 *        
 */
interface GenerationDAOManager extends DAOInterface
{
    /**
     * verifie l'existance du nom de la generation
     * @param string $name
     * @param int $id
     * @return bool
     */
    public function checkByName (string $name, ?int $id = null) : bool;
    
    /**
     * verifie l'existance de l'abreviation dans le bdd
     * @param string $abbreviation
     * @param int $id
     * @return bool
     */
    public function checkByAbreviation(string $abbreviation, ?int $id = null) : bool;
    
    /**
     * verifie s'il y a une generation reprenser par le numero en parmatre
     * @param int $number
     * @param int $id
     * @return bool
     */
    public function checkByNumber (int $number, ?int $id = null) : bool;
    
    /**
     * renvoie la generation reprensenter par le numero en paramtre
     * @param int $number
     * @return Generation
     */
    public function findByNumber (int $number) : Generation ;

}

