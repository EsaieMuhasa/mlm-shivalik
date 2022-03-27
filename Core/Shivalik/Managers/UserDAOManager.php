<?php
namespace Core\Shivalik\Managers;

use PHPBackend\Dao\DAOInterface;
use Core\Shivalik\Entities\User;

/**
 *
 * @author Esaie MHS
 *        
 */
interface UserDAOManager extends DAOInterface
{

    /**
     * mis sen jour du statut d'un membre
     * @param int $id
     * @param bool $enable
     */
    public function updateState (int $id, bool $enable) : void;

    /**
     * ce numero telephonique exist dans la BD?
     * @param string $telephone
     * @param int $id
     * @return bool
     */
    public function checkByTelephone (string $telephone, ?int $id = null) : bool;
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DefaultDAOInterface::findByColumnName()
     */
    public function findByColumnName(string $columnName, $value, bool $forward = true);

    /**
     * ce pseudo existe dans la BD?
     * @param string $pseudo
     * @param int $id
     * @return bool
     */
    public function checkByPseudo (string $pseudo, ?int $id = null) : bool ;
    
    
    /**
     * cette e-mail existe dans la BD?
     * @param string $email
     * @param int $id
     * @return bool
     */
    public function checkByEmail (string $email, ?int $id = null) : bool ;
    
    /**
     * revoie le proprietaire du pseudo en parmatere
     * @param string $pseudo
     * @param bool $forward
     * @return \Core\Shivalik\Entities\User
     */
    public function findByPseudo (string $pseudo, bool $forward = false);
    
    
    /**
     * recuperation de l'utilisateur propritaire de l'email en parmetre
     * @param string $email
     * @param bool $forward
     * @return User
     */
    public function findByEmail (string $email, bool $forward = false);
    
    
    /**
     * recuperation du proprietaire du mero de telephone en parmetre
     * @param string $telephone
     * @param bool $forward
     * @return User
     */
    public function findByTelephone (string $telephone, bool $forward = false) ;
    
    /**
     * mise en jour de la photo d'un utilisateur
     * @param int $userId
     * @param string $photo
     */
    public function updatePhoto (int $userId, string $photo) : void;
    
    
    /**
     * mis en jour du mot de passe d'un utilisateur
     * @param int $userId
     * @param string $password
     */
    public function updatePassword (int $userId, string $password) : void;
}

