<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Managers\LocalisationDAOManager;
use Core\Shivalik\Managers\UserDAOManager;
use PHPBackend\Dao\DefaultDAOInterface;
use PHPBackend\Dao\UtilitaireSQL;

/**
 *
 * @author Esaie MUHASA
 *        
 */
abstract class AbstractUserDAOManager extends DefaultDAOInterface implements UserDAOManager
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
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\UserDAOManager::checkByTelephone()
     */
    public function checkByTelephone (string $telephone, ?int $id = null) : bool {
        return $this->columnValueExist('telephone', $telephone, $id);
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DefaultDAOInterface::findByColumnName()
     */
    public function findByColumnName (string $columnName, $value, bool $forward = true)
    {
        $user =parent::findByColumnName($columnName, $value, $forward);
        if ($user->getLocalisation() != null) {
            $user->setLocalisation($this->localisationDAOManager->findById($user->getLocalisation()->getId()));
        }
        return $user;
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\UserDAOManager::checkByPseudo()
     */
    public function checkByPseudo (string $pseudo, ?int $id = null) : bool {
        return $this->columnValueExist('pseudo', $pseudo, $id);
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\UserDAOManager::checkByEmail()
     */
    public function checkByEmail (string $email, ?int $id = null) : bool {
        return $this->columnValueExist('email', $email, $id);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\UserDAOManager::findByPseudo()
     */
    public function findByPseudo (string $pseudo, bool $forward = false) {
        return $this->findByColumnName("pseudo", $pseudo, $forward);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\UserDAOManager::findByEmail()
     */
    public function findByEmail (string $email, bool $forward = false) {
        return $this->findByColumnName('email', $email, $forward);
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\UserDAOManager::findByTelephone()
     */
    public function findByTelephone (string $telephone, bool $forward = false) {
        return $this->findByColumnName('telephone', $telephone);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\UserDAOManager::updatePhoto()
     */
    public function updatePhoto (int $userId, string $photo) : void {
        UtilitaireSQL::update($this->getConnection(), $this->getTableName(), array('photo' => $photo), $userId);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\UserDAOManager::updatePassword()
     */
    public function updatePassword (int $userId, string $password) : void {
        UtilitaireSQL::update($this->getConnection(), $this->getTableName(), array('password' => $password), $userId);
    }
}

