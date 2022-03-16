<?php
namespace Core\Shivalik\Managers;

use Core\Shivalik\Entities\Office;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\DAOInterface;

/**
 *
 * @author Esaie MHS
 *        
 */
interface  OfficeDAOManager extends DAOInterface
{

    /**
     * @param string $name
     * @param int $id
     * @return bool
     */
    public function checkByName (string $name, ?int $id = null) : bool;
    
    /**
     * mis en jour de la visibilite d'un office
     * @param int $id
     * @param bool $visible
     */
    public function updateVisibility (int $id, bool $visible) : void;
    
    /**
     * Selection des offices ayant pour visibilitee, le boolean en parametre
     * @param bool $visible
     * @param int $limit
     * @param int $offset
     * @return object[]
     */
    public function findByVisibility (bool $visible = true, ?int $limit = null, int $offset = 0);
    
    /**
     * verification de l'existance d'un office ayant pour visibilite la valeur en parametre
     * @param bool $visible
     * @param int $limit
     * @param int $offset
     * @return boolean
     */
    public function checkByVisibility (bool $visible = true, ?int $limit = null, int $offset = 0);
    
    /**
     * @param int $id
     * @param string $photo
     */
    public function updatePhoto (int $id, string $photo) : void;


	/**
	 * revoie l'office dont l'ID est en parametre.
	 * l'office revoyer est chargee au comptet (tout les operations deja faite par l'office sont directement cherger dans l'object retourner) 
	 * @param int|Office $office
	 * @return Office
	 */
	public function load ($office) : Office;

	/**
     * le membre as-t-elle un bureau
     * @param int $memberId
     * @return bool
     */
    public function checkByMember (int $memberId) : bool;
    
    /**
     * revoie le compte office d'un membre, pour les membre qui ont des comptes
     * @param int $memberId
     * @return Office
     * @throws DAOException
     */
    public function findByMember (int $memberId) : Office;

}

