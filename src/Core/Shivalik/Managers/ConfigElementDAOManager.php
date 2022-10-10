<?php
namespace Core\Shivalik\Managers;

use Core\Shivalik\Entities\ConfigElement;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\DAOInterface;

/**
 * interface de communication avec la table qui contiens
 * les elements associee a une configuration
 */
interface ConfigElementDAOManager extends DAOInterface {

    /**
     * renvoie la collection des elements d'une configuration
     *
     * @param int $configId
     * @return ConfigElement[]
     * @throws DAOException s'il y a une erreur lors de la communication avec la BDD
     */
    public function findByConfig(int $configId) : array;

    /**
     * verifie si la configuration contiens aumoin n element
     *
     * @param int $configId
     * @return bool
     * @throws DAOException s'il y a erreur lors de la communication avec le SGBD
     */
    public function checkByConfig(int $configId) : bool;
}