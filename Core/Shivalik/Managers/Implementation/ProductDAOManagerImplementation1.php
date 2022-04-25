<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Managers\ProductDAOManager;
use Core\Shivalik\Entities\Product;
use PHPBackend\Dao\UtilitaireSQL;
use PHPBackend\Dao\DefaultDAOInterface;
use Core\Shivalik\Entities\Category;
use PHPBackend\Dao\DAOException;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class ProductDAOManagerImplementation1 extends DefaultDAOInterface implements ProductDAOManager
{
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DefaultDAOInterface::hasView()
     */
    protected function hasView(): bool {
        return true;
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::createInTransaction()
     * @param Product $entity
     */
    public function createInTransaction($entity, \PDO $pdo): void {
        $id = UtilitaireSQL::insert($pdo, $this->getTableName(), [
            self::FIELD_DATE_AJOUT => $entity->getFormatedDateAjout(),
            'name' => $entity->getName(),
            'defaultUnitPrice' => $entity->getDefaultUnitPrice(),
            'description' => $entity->getDescription(),
            'packagingSize' => $entity->getPackagingSize(),
            'category' => $entity->getCategory()->getId()
        ]);
        $entity->setId($id);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\ProductDAOManager::findByName()
     */
    public function findByName (string $name) : Product {
        return  $this->findByColumnName('name', $name);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\ProductDAOManager::checkByName()
     */
    public function checkByName (string $name,  ?int $id = null) : bool {
        return  $this->checkByColumnName('name', $name, $id);
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DefaultDAOInterface::findByColumnName()
     */
    public function findByColumnName(string $columnName, $value, bool $forward = true) {
        $data = parent::findByColumnName($columnName, $value, $forward);
        $data->setCategory($this->getDaoManager()->getManagerOf(Category::class)->findById($data->getCategory()->getId()));
        return $data;
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::update()
     * @param Product $entity
     */
    public function update($entity, $id): void {
        UtilitaireSQL::update($this->getConnection(), $this->getTableName(), [
            self::FIELD_DATE_MODIF => $entity->getFormatedDateModif(),
            'name' => $entity->getName(),
            'defaultUnitPrice' => $entity->getDefaultUnitPrice(),
            'description' => $entity->getDescription(),
            'packagingSize' => $entity->getPackagingSize(),
            'category' => $entity->getCategory()->getId()
        ], $id);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\ProductDAOManager::updatePicture()
     */
    public function updatePicture(int $id, string $path): void {
        UtilitaireSQL::update($this->getConnection(), $this->getTableName(), [
            'picture' => $path
        ], $id);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\ProductDAOManager::checkByCategory()
     */
    public function checkByCategory(int $categoryId, ?int $limit = null, int $offset = 0): bool {
        return UtilitaireSQL::checkAll($this->getConnection(), $this->getTableName(), ['category' => $categoryId], $limit, $offset);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\ProductDAOManager::countByCategory()
     */
    public function countByCategory(int $categoryId): int {
        return UtilitaireSQL::count($this->getConnection(), $this->getTableName(), ['category' => $categoryId]);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\ProductDAOManager::findByCategory()
     */
    public function findByCategory(int $categoryId, ?int $limit = null, int $offset = 0): array {
        return UtilitaireSQL::findAll($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), self::FIELD_DATE_AJOUT, true, ['category' => $categoryId], $limit, $offset);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\ProductDAOManager::ckeckVailableByOffice()
     */
    public function checkVailableByOffice (int $officeId, ?int $limit = null, int $offset = 0) : bool {
        $result = false;
        try {
            $SQL = "SELECT * FROM {$this->getViewName()} WHERE id IN(SELECT product FROM V_AuxiliaryStock WHERE office =:office)";
            $SQL .= $limit!==null? " LIMIT {$limit} OFFSET {$offset}":"";
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), $SQL, ['office' => $officeId]);
            if ($statement->fetch()) {
                $result=true;
            }
            $statement->closeCursor();
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        return $result;
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\ProductDAOManager::countVailableByOffice()
     */
    public function countVailableByOffice (int $officeId): int {
        $count = 0;
        try {
            $SQL = "SELECT COUNT(*) AS nombre FROM {$this->getViewName()} WHERE id IN(SELECT product FROM V_AuxiliaryStock WHERE office =:office)";
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), $SQL, ['office' => $officeId]);
            if ($row = $statement->fetch()) {
                $count = $row['nombre'];
            }
            $statement->closeCursor();
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        return $count;
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\ProductDAOManager::findVailableByOffice()
     */
    public function findVailableByOffice(int $officeId, ?int $limit = null, int $offset = 0): array {
        $data = [];
        try {
            $SQL = "SELECT * FROM {$this->getViewName()} WHERE id IN(SELECT product FROM V_AuxiliaryStock WHERE office =:office)";
            $SQL .= $limit!==null? " LIMIT {$limit} OFFSET {$offset}":"";
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), $SQL, ['office' => $officeId]);
            while ($row = $statement->fetch()) {
                $data[] = new Product($row);
            }
            $statement->closeCursor();
            
            if (empty($data)) {
                throw new DAOException("non data matched by selection query");
            }
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        return $data;
    }

}
