<?php
namespace PHPBackend\Dao;

use PHPBackend\DBEntity;
use PHPBackend\Config\EntityMetadata;

/**
 *
 * @author Esaie MUHASA
 *        
 */
interface DAOInterface
{
    const DEFAULT_REQUEST_ID = 0;
    ///METHODE POUR ACCEDER AUX METADONNES ET AU MANAGER DES DAO
    //ET LA GESTION DES EVEMENTS
    //==========================================================
    
    /**
     * revoie le gestionnaire des DAO
     * @return DAOManagerFactory
     */
    public function getManagerFactory () : DAOManagerFactory;
    
    /**
     * Renvoie les metadonnes sur le mapage
     * @return EntityMetadata
     */
    public function getMetadata () : EntityMetadata;
    
    /**
     * ajout d'un listener
     * @param DAOListener $listener
     * @param int|int[] $types le type d'evenement ecouter.
     * s'il veau null, alors le listener sera notifier de toutt evement
     */
    public function addListener (DAOListener $listener, $types = null) : void;
    
    /**
     * Supresion d'un listener
     * @param DAOListener $listener
     */
    public function removeListener (DAOListener $listener) : void;
    
    /**
     * Revoie une collection des listeners
     * @return DAOListenerItem[]
     */
    public function getListeners () : array;
    
    
    ////METHODES DU CRUD
    ///=======================================
    
    /**
     * creation d'une nouvelle occurence
     * @param DBEntity $entity
     * @return void
     * @throws DAOException
     */
    public function create($entity) : void;
    
    /**
     * creation d'une collection d'uccurences en parametre
     * @param DBEntity[] $entities
     */
    public function createAll(array $entities) : void;
    
    /**
     * creation d'une occurence dans une trasaction
     * @param DBEntity $entity
     * @param \PDO $pdo
     */
    public function createInTransaction ($entity, \PDO $pdo) : void;

    /**
     * mis en jour d'une occurence
     * @param DBEntity $entity
     * @param int|string $id
     * @return void
     * @throws DAOException
     */
    public function update ($entity, $id) : void;
    
    /**
     * mise en jour d'une occurerence dans une transaction
     * @param DBEntity $entity
     * @param int|string $entity
     * @param \PDO $pdo
     */
    public function updateInTransaction ($entity, $id, \PDO $pdo) : void;
    
    /**
     * surpession definitive d'un element dans dans la bdd
     * @param int|string $id
     */
    public function delete($id) : void;
   
    /**
     * supression des occurences dont leurs ID sont dans le tableau en parametre.
     * si le table est vide, alors la table dans son entiertee est troncater
     * @param array $ids
     * @return array collection d'identifiants des occureces suprimer
     */
    public function deleteAll(array $ids = array()) : array;

    /**
     * supression d'une collection d'occurence dans une transaction
     * @param \PDO $pdo
     * @param array $ids
     * @return array
     */
    public function deleteAllInTransaction(\PDO $pdo, array $ids = array()) : array;
    
    /**
     * depplace une occurence dans la corbeil
     * @param int|string $id
     */
    public function moveToTrash ($id) : void;
    
    /**
     * Evoie d'une collection des d'ocurence, en corbeill
     * @param array $ids
     * @return array
     */
    public function moveAllToTrash (array $ids = array()) : array;

    /**
     * depacement de tout les occurences dont leurs IDs sont en parametre dans la corbeille.
     * l'operation s'effectue dans une transaction
     * @param array $ids
     * @param \PDO $pdo
     * @return array
     */
    public function moveAllToTrashInTransaction(array $ids = array(), \PDO $pdo) : array;

    /**
     * Revoie l'occurence dont l'ID est en parametre
     * @param int|string $id
     * @param bool $forward
     * @return DBEntity
     * @throws DAOException
     */
    public function findById ($id, bool $forward=true);

    /**
     * cet identifiant existe dans la BDD??
     * @param int|string $id
     * @return bool
     * @throws DAOException
     */
    public function checkById ($id) : bool;
    
    /**
     * revoie une collection doccurence dont leurs ID sont dans le tableau en parametre
     * @param array $ids
     * @param bool $forward
     * @return array
     */
    public function findAllById (array $ids, bool $forward = true) : array;
    
    /**
     * revoie le premier occurence dont la colonne en premier parametre contiens la valeur en 2 eme parmetre
     * @param string $columnName
     * @param mixed $value
     * @param bool $forward
     * @return DBEntity
     * @throws DAOException
     */
    public function findByColumnName (string $columnName, $value, bool $forward = true);
    
    
    /**
     * est-ce que cette valeur existe dans la table concerner??
     * @param string $columName
     * @param mixed $value
     * @param mixed $id
     * @return bool
     * @throws DAOException
     */
    public function checkByColumnName (string $columName, $value, $id = null) : bool;
    
    
    /**
     * revoie la collection des occurence pour l'intervalle en parametre
     * @param int $limit
     * @param int $offset
     * @return DBEntity[]
     */
    public function findAll(?int $limit=null, int $offset = 0) : array;
    
    
    /**
     * comptage de tout les occurence d'une table
     * @return int
     * @throws DAOException s'il ya erreur lors de la communication avec la BDD
     */
    public function countAll () : int;
    
    /**
     * verifie s'il ya aumoin une donnee dans l'intervale en parametre
     * @param int $limit
     * @param int $offset
     * @return bool
     * @throws DAOException
     */
    public function checkAll(?int $limit = null, int $offset = 0) : bool;
    
    /**
     * revoie une collection des occurences dont la collonne en premier parametre contiens la valeur en 2em parametre
     * @param string $columName
     * @param mixed $value
     * @param int $limit
     * @param int $offset
     * @return DBEntity[]
     * @throws DAOException
     */
    public function findAllByColumName (string $columName, $value, ?int $limit=null, int $offset = 0) : array;
    
    /**
     * comptage de tout les occurences, avec filtrage dur une colone
     * @param string $columName
     * @param mixed $value
     * @return int
     * @throws DAOException
     */
    public function countAllByColumName(string $columName, $value) : int;
    
    /**
     * verifie s'il y a des donnes dans cette interval
     * @param string $columName
     * @param mixed $value
     * @param int $limit
     * @param int $offset
     * @throws DAOException
     */
    public function checkAllByColumName(string $columName, $value, ?int $limit = null, int $offset = 0) : bool;
    
    /**
     * revoie une collection des elements creer en une date ou un intervale des date en parametre
     * @param \DateTime $dateMin
     * @param \DateTime $dateMax
     * @param int $limit
     * @param int $offset
     * @throws DAOException
     */
    public function findByCreationHistory (\DateTime $dateMin, ?\DateTime $dateMax = null, ?int $limit = null, int $offset= 0) : array;
    
    /**
     * verifie s'il y a une occurence creer dans l'intervate des dates en paramtres
     * @param \DateTime $dateMin
     * @param \DateTime $dateMax
     * @param array $filters
     * @param int $limit
     * @param int $offset
     * @return bool
     * @throws DAOException
     */
    public function checkByCreationHistory (\DateTime $dateMin, ?\DateTime $dateMax = null, ?int $limit = null, int $offset= 0) : bool;
    
    /**
     * comptage des occurences cree en une date ou une intervale des dates donnees 
     * @param \DateTime $dateMin
     * @param \DateTime $dateMax
     * @return int
     */
    public function countByCreationHistory (\DateTime $dateMin, ?\DateTime $dateMax = null) : int;
    
    /**
     * verifie s'il y a aumoin une donnees dans la table concernee
     * @return bool
     * @throws DAOException
     */
    public function hasData () : bool; 


}

