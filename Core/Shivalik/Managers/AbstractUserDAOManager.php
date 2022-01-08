<?php
namespace Core\Shivalik\Managers;

use PHPBackend\DBEntity;
use PHPBackend\Dao\DefaultDAOInterface;
use PHPBackend\Dao\UtilitaireSQL;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class AbstractUserDAOManager extends DefaultDAOInterface
{
    
    /**
     * @var LocalisationDAOManager
     */
    protected $localisationDAOManager;
    
    /**
     * mis sen jour du statut d'un telephone
     * @param int $id
     * @param bool $enable
     */
    public function updateState (int $id, bool $enable) : void {
        UtilitaireSQL::update($this->getConnection(), $this->getTableName(), array('enable' => ($enable? 1:'0')), $id);
    }

    /**
     * ce numero telephonique exist dans la BD?
     * @param string $telephone
     * @param int $id
     * @return bool
     */
    public function checkByTelephone (string $telephone, ?int $id = null) : bool {
        return $this->columnValueExist('telephone', $telephone, $id);
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DefaultDAOInterface::findByColumnName()
     */
    public function findByColumnName(string $columnName, $value, bool $forward = true)
    {
        $user =parent::findByColumnName($columnName, $value, $forward);
        if ($user->getLocalisation() != null) {
            $user->setLocalisation($this->localisationDAOManager->findById($user->getLocalisation()->getId()));
        }
        return $user;
    }

    /**
     * ce pseudo existe dans la BD?
     * @param string $pseudo
     * @param int $id
     * @return bool
     */
    public function checkByPseudo (string $pseudo, ?int $id = null) : bool {
        return $this->columnValueExist('pseudo', $pseudo, $id);
    }
    
    
    /**
     * cette e-mail existe dans la BD?
     * @param string $email
     * @param int $id
     * @return bool
     */
    public function checkByMail (string $email, ?int $id = null) : bool {
        return $this->columnValueExist('email', $email, $id);
    }
    
    /**
     * revoie le proprietaire du pseudo en parmatere
     * @param string $pseudo
     * @param bool $forward
     * @return DBEntity
     */
    public function findByPseudo (string $pseudo, bool $forward = false) {
        return $this->findByColumnName("pseudo", $pseudo, $forward);
    }
    
    
    /**
     * recuperation de l'utilisateur propritaire de l'email en parmetre
     * @param string $email
     * @param bool $forward
     * @return DBEntity
     */
    public function findByEmail (string $email, bool $forward = false) {
        return $this->findByColumnName('email', $email, $forward);
    }
    
    
    /**
     * recuperation du proprietaire du mero de telephone en parmetre
     * @param string $telephone
     * @param bool $forward
     * @return DBEntity
     */
    public function findByTelephone (string $telephone, bool $forward = false) {
        return $this->findByColumnName('telephone', $telephone);
    }
    
    /**
     * mise en jour de la photo d'un utilisateur
     * @param int $userId
     * @param string $photo
     */
    public function updatePhoto (int $userId, string $photo) : void {
        UtilitaireSQL::update($this->getConnection(), $this->getTableName(), array('photo' => $photo), $userId);
    }
    
    
    /**
     * mis en jour du mot de passe d'un utilisateur
     * @param int $userId
     * @param string $password
     */
    public function updatePassword (int $userId, string $password) : void {
        UtilitaireSQL::update($this->getConnection(), $this->getTableName(), array('password' => $password), $userId);
    }
}

