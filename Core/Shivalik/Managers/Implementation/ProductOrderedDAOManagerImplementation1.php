<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Managers\ProductOrderedDAOManager;
use PHPBackend\Dao\DefaultDAOInterface;
use PHPBackend\Dao\UtilitaireSQL;
use Core\Shivalik\Entities\ProductOrdered;
use Core\Shivalik\Managers\ProductDAOManager;
use Core\Shivalik\Managers\AuxiliaryStockDAOManager;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class ProductOrderedDAOManagerImplementation1 extends DefaultDAOInterface implements ProductOrderedDAOManager {
    
    /**
     * @var ProductDAOManager
     */
    private $productDAOManager;
    
    /**
     * @var AuxiliaryStockDAOManager
     */
    private $auxiliaryStockDAOManager;
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DefaultDAOInterface::createInTransaction()
     * @param ProductOrdered $entity
     */
    public function createInTransaction($entity, \PDO $pdo): void {
        $id = UtilitaireSQL::insert($this->getConnection(), $this->getTableName(), [
            self::FIELD_DATE_AJOUT => $entity->getFormatedDateAjout(),
            'product' => $entity->getProduct()->getId(),
            'command' => $entity->getCommand()->getId(),
            'stock' => $entity->getStock()->getId(),
            'quantity' => $entity->getQuantity()
        ]);
        $entity->setId($id);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\ProductOrderedDAOManager::checkByCommand()
     */
    public function checkByCommand(int $commandId): bool {
        return $this->checkByColumnName("command", $commandId);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\ProductOrderedDAOManager::checkByProduct()
     */
    public function checkByProduct(int $productId, ?int $limit = null, int $offset = 0): bool {
        return UtilitaireSQL::checkAll($this->getConnection(), $this->getTableName(), ['product' => $productId], $limit, $offset);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\ProductOrderedDAOManager::countByCommand()
     */
    public function countByCommand(int $commandId): int {
        return UtilitaireSQL::count($this->getConnection(), $this->getTableName(), ['command' => $commandId]);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\ProductOrderedDAOManager::countByProduct()
     */
    public function countByProduct(int $productId): int {
        return UtilitaireSQL::count($this->getConnection(), $this->getTableName(), ['product' => $productId]);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\ProductOrderedDAOManager::findByCommand()
     */
    public function findByCommand(int $commandId): array {
        $data = UtilitaireSQL::findAll($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(),
            self::FIELD_DATE_AJOUT, true, ['command' => $commandId]);
        foreach ($data as $d) {
            $d->setProduct($this->productDAOManager->findById($d->getProduct()->getId()));
            $d->setStock($this->auxiliaryStockDAOManager->load($d->getStock()->getId()));
        }
        return $data;
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\ProductOrderedDAOManager::findByProduct()
     */
    public function findByProduct(int $productId, ?int $limit = null, int $offset = 0): array {
        return UtilitaireSQL::findAll($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(),
            self::FIELD_DATE_AJOUT, true, ['product' => $productId], $limit, $offset);
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::update()
     * @param ProductOrdered $entity
     */
    public function update($entity, $id): void {
        UtilitaireSQL::update($this->getConnection(), $this->getTableName(), [
            self::FIELD_DATE_MODIF => $entity->getFormatedDateModif(),
            'quantity' => $entity->getQuantity(),
            'stock' => $entity->getStock()->getId(),
            'product' => $entity->getProduct()->getId()
        ], $id);
    }

}

