<?php
namespace Library;


use Library\Config\EntityMetadata;

/**
 * Classe de base pour tout les interfaces des tables de la base de donnee
 * @tutorial Par defaut l'API PDO est celle utiliser.
 * Les requette des base sont regrouper dans le trait SQLManagerPDO.
 * si jamais vous faite l'implementation de cette specification dans un aute API, alors il faut redefinir 
 * les methodes lnons abstraite de cette classe.
 * @author Esaie MHS
 *        
 */
abstract class AbstractDAOManager
{
    /**
     * une reference vers le gestionnaire des managers
     * @var DAOManager
     */
    private $managers;
    
    /**
     * Le nom de la table
     * @var string
     */
    private $tableName=null;
    /**
     * Le nom simple de la classe representant l'entite
     * @var string
     */
    private $entityShortName=null;
    
    /**
     * Le nom de la vue materiel de la table
     * @var string
     */
    private $viewName = null;
    /**
     * Le nom de la table parente
     * @var string
     */
    private $parentTableName = null;
    
    /**
     * Les metas donnees du DAO dans le fichier de configuration
     * @var EntityMetadata
     */
    private $metadata;
    
    use DAOAutoload, SQLManagerPDO;

    /**
     * Constructeur d'initialisation d'un Manager
     * @param DAOManager $daoManager
     */
    public function __construct(DAOManager $daoManager)
    {
        $this->managers = $daoManager;
        $class=get_class($this);
        if (!preg_match('#^\\\\(.+)#', $class)) {
            $class = "\\{$class}";
        }
        $this->metadata = $daoManager->getEntitiesConfig()->forImplementation($class);//getMetadata($this->getEntityShortName());
        $this->autoHydrate($daoManager);
        
        $methodInit ='init';
        
        if (is_callable(array($this, $methodInit))) {
            $this->$methodInit($daoManager);
        }
    }
    
    /**
     * @return \Library\DAOManager
     */
    public final function getDaoManager()
    {
        return $this->managers;
    }
    
    /**
     * @return \Library\Config\EntityMetadata
     */
    public function getMetadata() : EntityMetadata
    {
        return $this->metadata;
    }

    /**
     * Recuperation de la connexion vers la base de donnees
     * @param string $name
     * @return \PDO
     */
    public final function getConnection (?string $name=null) : \PDO {
        return $this->getDaoManager()->getConnection($name);
    }

    /**
     * Pour l'enregistrement d'une nouvelle ocuurence dans la base de donnee
     * @param DBEntity $entity
     * @return void
     * @throws DAOException
     */
    public abstract function create($entity);
    
    /**
     * Enregistrement d'une collection des donnees
     * @param DBEntity[] $entities
     * @throws DAOException
     * @return DBEntity[] la collection de DBEntity qui viens d'etre enregistrer
     */
    public function createAll(array $entities){
        try {
            $transaction = $this->pdo->beginTransaction();
            if ($transaction) {
                foreach ($entities as $entity) {
                    $this->createInTransaction($entity, $this->pdo);
                }
                $this->pdo->commit();
            }else {
                throw new DAOException("Une erreur est survenue lors de la creation de la transaction. Re-essayer ulterieurement svp!");
            }
        } catch (\Exception $e) {
            try {
                $this->pdo->rollBack();
            } catch (\Exception $e) {}
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
    }
    
    /**
     * Demande de creation d'un occurence dans la meme transaction
     * @param DBEntity $entity
     * @param \PDO $api
     * @throws DAOException
     */
    public function createInTransaction($entity, $api) : void{
        throw new DAOException("Assurez-vous d'avoir redefinie la methode createInTransaction()");
    }
    
    /**
     * Pour la mise a jour d'une occurence de la base de donnee
     * @param DBEntity $entity
     * @param int $id l'identifiant de l'occurance a metre ajour
     * @return void
     * @throws DAOException
     */
    public abstract function update($entity, $id);
    
    /**
     * Effectuer une mise enjour dans une transaction deja demarer d'avence
     * @param DBEntity $entity
     * @param int $id
     * @param \PDO $api
     * @throws DAOException
     */
    public function updateInTransaction($entity, ?int $id, $api) : void {
        throw new DAOException("Les mises en jours transactionnel ne sont pas prise en charge par le manager de l'entity {$this->getEntityShortName()}");
    }
    
    /**
     * Recuperation d'une occurance dont l'identifiant est en parametre de la dite methode
     * @param int $id
     * @param bool $forward faut-il faire le maping aprofodie des objets???
     * @return DBEntity
     * @throws DAOException s'il y a erreur lors de la communication avec le SGBD ou aucun resultat
     */
    public function getForId(int $id, bool $forward = true){
        return $this->pdo_uniqueFromTableColumnValue(
            ($this->hasView()? $this->getViewName()  : $this->getTableName()),
            $this->getMetadata()->getName(),'id', $id
        );
    }
    
    /**
     * Selection des occurence dont les Identifiant sont en parametre
     * @param array $ids
     * @param bool $forward faut-il faire le maping des objets constituant de l'pbjet principale??
     * @return DBEntity[]
     * @throws DAOException s'il y a erreur lors dela communication avec la BDD soit
     * aucune occurence ne correspond aux id en parametre
     */
    public function forIDs(array $ids, bool $forward = true) : array{
        return $this->pdo_selectFromTableIn($this->getTableName(), $this->getMetadata()->getName(), $ids);
    }
    
    /**
     * Renvoie tout les occurence excepter ceux dont leurs identifiants sont en parametre
     * @param array $ids
     * @param int $limit
     * @param int $offset
     * @return array
     * @throws DAOException s'il y a erreur lors de la communication avec la base de donnee, soit aucun resultat n'est retourner
     */
    public function getOther(array $ids, $limit=-1, $offset=-1) : array{
        return $this->pdo_selectOtherInTable($this->getTableName(), $this->getMetadata()->getName(), $ids, $limit, $offset);
    }
    
    /**
     * Pour verifier si l'identifiant d'une occurance existe dans un table de la base de donnee
     * @param int $id
     * @return boolean
     * @throws DAOException s'il y a erreur lors de la communication avec la BDD
     */
    public function idExist($id) : bool{
        return $this->columnValueExist('id', $id);
    }
    
    /**
     * Verification si une valeur existe dans une colonne d'une table de la bdd
     * @param string $columnName
     * @param mixed $value
     * @param int $id
     * @return boolean
     * @throws DAOException
     */
    public function columnValueExist($columnName, $value, $id=-1) : bool{
        return $this->pdo_columnValueExistInTable(
            ($this->hasView()? $this->getViewName()  : $this->getTableName()),
            $columnName, $value, $id
        );
    }
    
    
    /**
     * Pour la supression d'une donnee dans une table  de la BDD.
     * Cette operation est ireversible. l'occurence suprimer l'est de maniere definitive
     * @param int $id
     * @return void
     * @throws DAOException
     */
    public function delete(int $id) : void{
        $this->pdo_deleteInTable($this->getTableName(), $id);
    }
    
    /**
     * Supression d'un element dans une tansaction
     * @param int $id
     * @param \PDO $api
     * @throws DAOException
     */
    public function deleteInTransaction(int $id, $api) : void{
        throw new DAOException("Une supression transactionnel n\'est pas pris en charge par le manager de {$this->getEntityShortName()}");
    }
    
    /**
     * Supression de tout les occurences d'une table.
     * si un rableau d'identifiant est en parametre, alors seul les occurences dont 
     * les identifiant sont dans le tableau en parametre serons suprimer
     * @param int[] $ids
     * @throws DAOException s'il y erreur lors de la communication, soit s'il n'y a aucune ocurence suprimer
     */
    public function deleteAll(array $ids = array()) : array{
        return $this->pdo_deleteAllInTable($this->getTableName(), $ids);
    }
    
    /**
     * Supression multiple dans une transation deja demanre d'avance
     * @param array $ids
     * @param \PDO $api
     * @throws DAOException
     * @return int[] les identifinant des entitee suprimer definitivements
     */
    public function deleteAllInTransaction(array $ids = array(), $api) : array{
        throw new DAOException("Les supressions transactionnel multiples ne sont pas prise en compte par le manager de l'entity {$this->getEntityShortName()}");
    }
    
    /**
     * Mise en corbeille d'une occurence de la table.
     * il est possible de recuperer l'occurence mise en corbeil
     * @param int $id
     * @throws DAOException s'il y erreur los de la mise en corbeil
     */
    public function remove(int $id) : void {
        $this->pdo_removeInTable($this->getTableName(), $id);
    }
    
    /**
     * Supression temporaire  dans une transaction deja demarer d'avance
     * @param int $id
     * @param \PDO $api
     * @throws DAOException
     * @return void
     */
    public function removeInTransaction(int $id, $api) : void{
        $this->pdo_removeInTableTransactionnel($api, $this->getTableName(), $id);
    }
    
    /**
     * Mise en corbeil d'une collection d'occurence dont leurs identifiant sont en parametre
     * Si le ableau en parametre est vide, alors tout les occurence de la table sont mise en corbeil
     * @param array $ids
     * @throws DAOException s'il y a erreur lors de la communication avec la bdd
     */
    public function removeAll(array $ids = array()) : array{
        return $this->pdo_removeAllInTable($this->getTableName(), $ids);
    }
    
    /**
     * supression temporaire multiple dans une transaction deja demarer d'avence
     * @param int[] $ids
     * @param \PDO $api
     * @return array
     */
    public function removeAllInTransaction(array $ids = array(), $api) : array {
        throw new DAOException("La supression temporaire multiple n'est pas prise ne charge par le manager de l'entite {$this->getEntityShortName()}");
    }
    
    /**
     * Recuperation d'une occurence qui ce trouve dans la corbeille.
     * @param int $id l'identifiant de l'occurence
     * @throws DAOException s'il y a erreur lors de la communication avec la base de donnes
     */
    public function recycle($id){
        $this->pdo_recycleInTable($this->getTableName(), $id);
    }
    
    /**
     * Verification si une occurence est dans la BDD
     * @param int $id
     * @return boolean
     * @throws DAOException s'il ya errreur lors de lea communication avec la BDD
     */
    public function isInTrash($id) : bool{
        return $this->pdo_isInTableTrash(($this->hasView()? $this->getViewName()  : $this->getTableName()), $id);
    }
    
    /**
     * Recuperation des occurences qui ce trouve dans la corbeille.
     * Si le tableau en paramtre n'est pas vide, alors seul les occurences dont leurs ids sont en parmetre serons suprimer
     * @param array $ids
     * @throws DAOException s'il y a erreur lors de la communication avec la bdd
     */
    public function recycleAll(array $ids = array()) : array{
        return $this->pdo_recycleAllInTable($this->getTableName(), $ids);
    }
    
    /**
     * Selection de tout les occurance d'une table de la base de donnee
     * On a aussi la possiblitee de recuperrer une intervalle biens precis
     * @param int $limit nombre d'occurance a selectionner. par defaut il est a -1, cequi signifie tout selectionner
     * @param int $offset le pas de section. par defaut il est aussi a -1.
     * @return DBEntity[]
     * @throws DAOException
     */
    public function getAll($limit=-1, $offset=-1){
        return $this->pdo_getAllInTable(
            ($this->hasView()? $this->getViewName()  : $this->getTableName()),
            $this->getMetadata()->getName(), $limit, $offset
        );
    }
    
    /**
     * Revoie une collection d'activite en une date, ou une intervale de date
     * @param \DateTime $dateMin
     * @param \DateTime $dateMax
     * @param mixed[] $filters collection des colones de filtrage (seul END est prise en compte pour plusier collone)
     * @param int $limit
     * @param int $offset
     * @throws DAOException
     * @return DBEntity
     */
    public function getCreationHistory (\DateTime $dateMin, ?\DateTime $dateMax = null, array $filters = array(), $limit = -1, $offset=-1) {
        return $this->pdo_getCreationHistory($this->getTableName(), $this->getMetadata()->getName(), $dateMin, $dateMax, $filters, $limit, $offset);
    }
    
    /***
     * 
     * @param \DateTime $dateMin
     * @param \DateTime $dateMax
     * @param array $filters
     * @param int $limit
     * @param int $offset
     * @return bool
     */
    public function hasCreationHistory (\DateTime $dateMin, ?\DateTime $dateMax = null, array $filters = array(), $limit = -1, $offset=-1) : bool {
        return $this->pdo_hasCreationHistory($this->getTableName(), $dateMin, $dateMax, $filters, $limit, $offset);
    }
    
    /**
     * Recuperation de tout les occurence qui sont dans la corbeille,
     * si limit et offset sont = -1
     * @param int $limit, nombre d'occurence a selectionner
     * @param int $offset, nombre d'occurence a sauter avant le comptage
     */
    public function getTrash($limit=-1, $offset=-1){
        return $this->pdo_getTrashInTable(
            ($this->hasView()? $this->getViewName()  : $this->getTableName()),
            $this->getMetadata()->getName(), $limit, $offset
         );
    }
    
    /**
     * Pour commpter le nombre d'occurance d'une table.
     * Lors du comptage on fais abstraction au occurence qui sont dans la corbeil
     * @return int le nombre d'occurance
     * @throws DAOException s'il y a erreur lors de la communication avec la BDD
     */
    public function countAll() : int{
        return $this->pdo_countAllInTable(
            ($this->hasView()? $this->getViewName()  : $this->getTableName())
        );
    }
    
    /**
     * Comptage des occurences qui sont dans la corbeil
     * @return int le nombre d'occurence qui sont dans la corbeil
     * @throws DAOException s'il y a erreur lors de la communication avec la bdd
     */
    public function countInTrash() : int{
        try {
            
            return $this->pdo_countInTrashInTable(
                ($this->hasView()? $this->getViewName()  : $this->getTableName())
            );
        } catch (\Exception $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
    }
    
    
    /**
     * methode utilitaire de deduction de maniere automatique du nom de la table
     * @tutorial Cette methode respecte la nomenclature de Base de la Lib
     * dont le nom d'un manager doit avoir la syntax {NomDelaEntite}DAOManager{API_utiliser}
     * @throws LibException si la nomenclature du nom de la classe n'est pas respecter
     * @return string
     */
    protected function getTableName() : string{
        
        if($this->tableName==null){
            $ref = new \ReflectionClass($this);            
            $this->tableName = $this->managers->getEntitiesConfig()->forImplementation('\\'.$ref->getName())->getSimpleName();
        }
        
        return $this->tableName;
    }
    
    /**
     * Si une table doit a une table parent, re-definisez cette methode et retourner le nom de la table parente
     * @return NULL
     * @throws DAOException
     */
    protected function getParentTableName() : ?string{
        if (!$this->hasParentTable()) {
            return null;
        }
        
        if($this->parentTableName==null){
            $ref = new \ReflectionClass($this);
            $refClass = new \ReflectionClass($this->managers->getEntitiesConfig()->forImplementation('\\'.$ref->getName())->getName());
            if ($refClass->getParentClass() == null) {
                throw new DAOException("Impossible d'acceder aux metadonnees de la super classe de {$this->managers->getEntitiesConfig()->forImplementation('\\'.$ref->getName())->getName()}");
            }
            $this->parentTableName = $refClass->getParentClass()->getShortName();
        }
        return $this->parentTableName;
    }
    
    /**
     * methode utilitaire de deduction de maniere automatique du nom de la vue d'une table
     * @tutorial Cette methode respecte la nomenclature de Base de la Lib
     * dont le nom d'un manager doit avoir la syntax {NomDelaEntite}DAOManager{APIutiliser}.
     * Si une table a une vue materiel, pensez a redefinir la metode hasView (retourner true).
     * Si le nom de la vue ne respecte pas les normes de nomenclature de Library alors vous devez redefinier 
     * une fois de plus la @method string getViewName et retourner le nom de la vue
     * @throws LibException si la nomenclature du nom de la classe n'est pas respecter
     * @return string|NULL
     */
    protected function getViewName() : ?string{
        if (!$this->hasView()) {//Pour les tables qui n'ont pas de vue
            return null;
        }
        
        if($this->viewName==null){
            $this->viewName = 'V_'.$this->getTableName();
        }
        
        return $this->viewName;
    }
    
    /**
     * Recuperation du nom de la classe de l'entite
     * @throws LibException
     * @return string
     * @deprecated cette methode est deprecien vue qu'elle renvoie le nom simple de la classe de l'entite. Cella risque d'entrainer des erreur lors du mapping 
     * d'un entite. Veau mieux utiliser la @method getMetadata() : \Library\Config\EntityMetadata qui revoie une reference vers les metadonnees de configuration d'une entite
     */
    protected function getEntityShortName(): string{
        if($this->entityShortName==null){
            $ref = new \ReflectionClass($this);
            $this->entityShortName = $this->managers->getEntitiesConfig()->forImplementation('\\'.$ref->getName())->getSimpleName();
        }
        
        return $this->entityShortName;
    }
    
    
    /**
     * Verification si une table a une vue materiel dans la base de donnee
     * @return boolean
     */
    public function hasView() : bool{
        return false;
    }
    
    
    /**
     * Su une table a une table parent, pour la manipulation de l'heritage
     * @return boolean
     */
    public function hasParentTable() : bool{
        return false;
    }
    
}

