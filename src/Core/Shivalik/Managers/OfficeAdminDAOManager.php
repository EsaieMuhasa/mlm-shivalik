<?php
namespace Core\Shivalik\Managers;


use Core\Shivalik\Entities\OfficeAdmin;

/**
 *
 * @author Esaie MHS
 *        
 */
interface OfficeAdminDAOManager extends UserDAOManager
{

    
    /**
     * exist-il aumoin un compte admin pour l'office dont l'ID est en premier paramtre?
     * @param int $officeId
     * @param bool $active
     * @return bool
     */
    public function checkByOffice (int $officeId, bool $active = true) : bool ;
    
    /**
     * renvoie le compte administrateur active d'un office
     * @param int $officeId identifiant de l'office
     * @return OfficeAdmin
     */
    public function findActiveByOffice (int $officeId) : OfficeAdmin;
    
    /**
     * renvoie le compte de l'administrateur d'un office
     * N.B: le dernier compte enregistrer
     * @param int $officeId
     * @return OfficeAdmin
     */
    public function findAdminByOffice (int $officeId) : OfficeAdmin ;
  
}

