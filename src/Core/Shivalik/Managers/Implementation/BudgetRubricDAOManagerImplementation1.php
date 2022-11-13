<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\BudgetRubric;
use Core\Shivalik\Managers\BudgetRubricDAOManager;
use PDO;
use PDOException;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\DefaultDAOInterface;
use PHPBackend\Dao\UtilitaireSQL;

/**
 * implementation par defaut de la table de entitee qui represante la description d'une rubrique budgetaire
 */
class BudgetRubricDAOManagerImplementation1 extends DefaultDAOInterface implements BudgetRubricDAOManager {

    /**
     * sauvegarde d'une nouvelle rubrique
     * {@inheritDoc}
     * @param BudgetRubric $entity
     * @param PDO $pdo
     * @return void
     */
    public function createInTransaction($entity, PDO $pdo): void
    {
        $id  = UtilitaireSQL::insert($pdo, $this->getTableName(), [
            self::FIELD_DATE_AJOUT => $entity->getFormatedDateAjout(),
            'label' => $entity->getLabel(),
            'description' => $entity->getDescription(),
            '`owner`' => ($entity->getOwner() != null? $entity->getOwner()->getId() : null),
            'category' => $entity->getCategory()->getId()
        ]);
        $entity->setId($id);
    }

    /**
     * mise en jour d'une rubrique budgetaire
     *
     * @param BudgetRubric $entity
     * @param int $id
     * @param PDO $pdo
     * @return void
     */
    public function updateInTransaction($entity, $id, PDO $pdo): void
    {
        UtilitaireSQL::update($pdo, $this->getTableName(), [
            self::FIELD_DATE_MODIF => $entity->getFormatedDateModif(),
            'label' => $entity->getLabel(),
            'description' => $entity->getDescription(),
            'category' => $entity->getCategory()
        ], $id);
    }

    public function checkOwnedByMember(int $ownerKey): bool
    {
        return $this->checkByColumnName('`owner`', $ownerKey);
    }

    public function checkOwned(bool $owned = true): bool
    {
        $result = false;
        try {
            $st = UtilitaireSQL::prepareStatement($this->getConnection(),"SELECT id FROM {$this->getTableName()} WHERE `owner` IS ".($owned? 'NOT' : '')." NULL" );
            if($st->fetch()){
                $result = true;
            }
            $st->closeCursor();
        } catch (PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        return $result;
    }

    public function checkUnowned(): bool
    {
        return $this->checkOwned(false);
    }

    public function findOwnedByMember(int $ownerKey): array
    {
        return $this->findAllByColumName('`owner`', $ownerKey);
    }

    public function findOwned(bool $owned = true): array
    {
        $result = [];
        try {
            $st = UtilitaireSQL::prepareStatement($this->getConnection(),"SELECT * FROM {$this->getTableName()} WHERE `owner` IS ".($owned? 'NOT' : '')." NULL" );
            while($row = $st->fetch()){
                $result[] = new BudgetRubric($row);
            }
            $st->closeCursor();
            if (empty($result)) {
                throw new DAOException("No data match SQL selection query");
            }
        } catch (PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        return $result;
    }

    public function findUnowned(): array
    {
        return $this->findOwned(false);
    }
}
