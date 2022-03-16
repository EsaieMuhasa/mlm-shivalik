<?php
namespace PHPBackend\Dao;

use PHPBackend\DBEntity;
use PHPBackend\Config\EntityMetadata;

/**
 * Implemetation par defaut d'une interface du DAO
 * @author Esaie MUHASA   
 */
abstract class DefaultDAOInterface implements DAOInterface
{
    const FIELD_ID = "id";
    const FIELD_DATE_AJOUT = "dateAjout";
    const FIELD_DATE_MODIF = "dateModif";
    const FIELD_DELETED = "deleted";
    
    /**
     * une reference vers le gestionnaire des managers
     * @var DAOManagerFactory
     */
    private $factory;
    
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
     * @var DAOListenerItem[]
     */
    private $listeners = [];
    
    /**
     * Les metas donnees du DAO dans le fichier de configuration
     * @var EntityMetadata
     */
    private $metadata;
    
    use DAOAutoload;
    
    /**
     * constructeur d'initilisation
     * @param DAOManagerFactory $factory
     */
    public function __construct(DAOManagerFactory $factory) {
        $this->factory = $factory;
        
        $class=get_class($this);
        if (!preg_match('#^\\\\(.+)#', $class)) {
            $class = "\\{$class}";
        }
        $this->metadata = $factory->getEntitiesConfig()->forImplementation($class);
        
        $this->hydrateInterfaces($factory);
    }
    
    
    /**
     * creation d'une occurence dans une trasaction
     * @param DBEntity $entity
     * @param \PDO $pdo
     */
    public abstract function createInTransaction ($entity, \PDO $pdo) : void;
    
    /**
     * supression d'une collection d'occurence dans une transaction
     * @param \PDO $pdo
     * @param array $ids
     * @return array
     */
    public function deleteAllInTransaction(\PDO $pdo, array $ids = array()): array
    {
        UtilitaireSQL::deleteAll($pdo, $this->getTableName(), $ids);
    }
    
    /**
     * depacement de tout les occurences dont leurs IDs sont en parametre dans la corbeille.
     * l'operation s'effectue dans une transaction
     * @param array $ids
     * @param \PDO $pdo
     * @return array
     */
    public function moveAllToTrashInTransaction(array $ids = array(), \PDO $pdo): array
    {
        throw new DAOException("La mis en corbeil transactionnel n'est pas prise en charge");
    }
    
    /**
     * mise en jour d'une occurerence dans une transaction
     * @param DBEntity $entity
     * @param int|string $entity
     * @param \PDO $pdo
     */
    public function updateInTransaction($entity, $id, \PDO $pdo): void
    {
        throw new DAOException("les mise en jours transactionnel ne sont pas pris en charge");
    }
    /**
     * revoie la connection dont le nom est en parametre
     * @param string $name
     * @return \PDO
     */
    protected final function getConnection (?string $name=null) : \PDO {
        return $this->factory->getConnection($name);
    }
    
    
    /**
     * methode utilitaire de deduction de maniere automatique du nom de la table
     * @tutorial Cette methode respecte la nomenclature de Base de la Lib
     * dont le nom d'un manager doit avoir la syntax {NomDelaEntite}DAOManager{API_utiliser}
     * @throws DAOException si la nomenclature du nom de la classe n'est pas respecter
     * @return string
     */
    protected function getTableName() : string{
        
        if($this->tableName==null){
            $ref = new \ReflectionClass($this);
            $this->tableName = $this->factory->getEntitiesConfig()->forImplementation('\\'.$ref->getName())->getSimpleName();
        }
        
        return $this->tableName;
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::addListener()
     */
    public function addListener(\PHPBackend\Dao\DAOListener $listener, $types = null): void
    {
        foreach ($this->listeners as $ls) {
            if ($ls == $listener) {
                return ;
            }
        }
        
        $this->listeners[] = new DAOListenerItem($listener, $types);
    }
    
    /**
     * transmission de l'evenement aux ecouteurs
     * @param DAOEvent $event
     */
    protected function dispatchEvent (DAOEvent $event) : void{
        foreach ($this->listeners as $listener) {
            if ($listener->isListen($event->getType())) {
                $listener->getListener()->onEvent($event);
            }
        }
    }
    
    /**
     * alias de la methode checkByColumnName();
     * @param string $columName
     * @param mixed $value
     * @param int|string $id
     * @return bool
     */
    protected function columnValueExist (string $columName, $value, $id = null) : bool {
        return $this->checkByColumnName($columName, $value, $id);
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::create()
     */
    public function create($entity) : void
    {
        try {
            $pdo = $this->getConnection();
            if ($pdo->beginTransaction()) {
                $this->createInTransaction($entity, $pdo);
                $pdo->commit();
                $event = new DAOEvent($this, DAOEvent::TYPE_CREATION, $entity);
                $this->dispatchEvent($event);
            }else {
                throw new DAOException("An error occurred while creating the transaction");
            }
        } catch (\PDOException $e) {
            throw new DAOException("An error occurred in the plain banefice sharing transaction: {$e->getMessage()}", intval($e->getCode()), $e);
        }
    }
    

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::checkByColumnName()
     */
    public function checkByColumnName(string $columName, $value, $id = null): bool
    {
        return UtilitaireSQL::columnValueExist($this->getConnection(), $this->getTableName(), $columName, $value, $id);
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::countAll()
     */
    public function countAll(): int
    {
        return UtilitaireSQL::count($this->getConnection(), $this->getTableName());
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::countByCreationHistory()
     */
    public function countByCreationHistory(\DateTime $dateMin, \DateTime $dateMax = null): int
    {      
        return UtilitaireSQL::countByCreationHistory($this->getConnection(), $this->getTableName(), self::FIELD_DATE_AJOUT, TRUE, $dateMin, $dateMax);        
    }


    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::createAll()
     */
    public function createAll(array $entities): void
    {
        throw new DAOException("Insersion multiple non pris en charge");
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::delete()
     */
    public function delete($id): void
    {
        UtilitaireSQL::delete($this->getConnection(), $this->getTableName(), "id", $id);
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::deleteAll()
     */
    public function deleteAll(array $ids = array()): array
    {
        return UtilitaireSQL::deleteAll($this->getConnection(), $this->getTableName(), $ids);
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::findAll()
     */
    public function findAll(?int $limit = null, int $offset = 0): array
    {
        return UtilitaireSQL::findAll($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), self::FIELD_DATE_AJOUT, true, [], $limit, $offset);
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::findAllByColumName()
     */
    public function findAllByColumName(string $columName, $value, ?int $limit = null, int $offset = 0): array
    {
        return UtilitaireSQL::findAll($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), $columName, true, array($columName => $value), $limit, $offset);
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::findAllById()
     */
    public function findAllById(array $ids, bool $forward = true): array
    {
        return UtilitaireSQL::findIn($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), "id", $ids);
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::findByColumnName()
     */
    public function findByColumnName(string $columnName, $value, bool $forward = true)
    {
        return UtilitaireSQL::findUnique($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), $columnName, $value);
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::findByCreationHistory()
     */
    public function findByCreationHistory(\DateTime $dateMin, \DateTime $dateMax = null, ?int $limit = null, int $offset = 0): array
    {
        return UtilitaireSQL::findCreationHistory($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), self::FIELD_DATE_AJOUT, true, $dateMin, $dateMax, array(), $limit, $offset);
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::findById()
     */
    public function findById($id, bool $forward = true)
    {
        return $this->findByColumnName(self::FIELD_ID, $id, $forward);
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::getListeners()
     * @return DAOListenerItem[]
     */
    public function getListeners(): array
    {
        return $this->listeners;
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::getManagerFactory()
     */
    public function getManagerFactory(): DAOManagerFactory
    {
        return $this->factory;
    }
    
    /**
     * alance de la methode getManagerFactory()
     * @return DAOManagerFactory
     */
    public function getDaoManager () : DAOManagerFactory {
        return $this->getManagerFactory();
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::getMetadata()
     */
    public function getMetadata(): EntityMetadata
    {
        return $this->metadata;
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::checkByCreationHistory()
     */
    public function checkByCreationHistory(\DateTime $dateMin, ?\DateTime $dateMax = null, ?int $limit = null, int $offset = 0): bool
    {
        return UtilitaireSQL::hasCreationHistory($this->getConnection(), $this->getTableName(), self::FIELD_DATE_AJOUT, TRUE, $dateMin, $dateMax, array(), $limit, $offset);
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::hasData()
     */
    public function hasData(): bool
    {
        return UtilitaireSQL::hasData($this->getConnection(), $this->getTableName());
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::checkById()
     */
    public function checkById($id): bool
    {
        return $this->columnValueExist(self::FIELD_ID, $id);
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::checkAll()
     */
    public function checkAll(?int $limit = null, int $offset = 0): bool
    {
        return UtilitaireSQL::checkAll($this->getConnection(), $this->getTableName(), array(), $limit, $offset);
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::checkAllByColumName()
     */
    public function checkAllByColumName(string $columName, $value, int $limit = null, int $offset = 0): bool
    {
        return UtilitaireSQL::checkAll($this->getConnection(), $this->getTableName(), array($columName => $value), $limit, $offset);
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::countAllByColumName()
     */
    public function countAllByColumName(string $columName, $value): int
    {
        return UtilitaireSQL::count($this->getConnection(), $this->getTableName(), array($columName => $value));
    }


    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::moveAllToTrash()
     */
    public function moveAllToTrash(array $ids = array()): array
    {
        throw new DAOException("La mise en corbeil n'est pas pris en charge");
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::moveToTrash()
     */
    public function moveToTrash($id): void
    {
        throw new DAOException("La mis en corbeil n'est pas prise en charge");
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::removeListener()
     */
    public function removeListener(\PHPBackend\Dao\DAOListener $listener): void
    {
        foreach ($this->listeners as $key => $ls) {
            if ($ls->getListener() == $listener) {
                unset($this->listeners[$key]);
                break;
            }
        }
    }

}

