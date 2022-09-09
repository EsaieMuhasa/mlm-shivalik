<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\SellSheetRowVirtualMoney;
use Core\Shivalik\Managers\SellSheetRowVirtualMoneyDAOManager;
use PDO;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\DefaultDAOInterface;
use PHPBackend\Dao\UtilitaireSQL;

/**
 * @author Esaie MUHASA <esaiemuhasa.dev@gmail.om>
 */
class SellSheetRowVirtualMoneyDAOManagerImplementation1 extends DefaultDAOInterface implements SellSheetRowVirtualMoneyDAOManager {

    public function findBySheet(int $sheetId): array
    {
        return UtilitaireSQL::findAll($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), self::FIELD_DATE_AJOUT, true, ['sheet' => $sheetId]);
    }

    public function countBySheet(int $sheetId): int
    {
        return UtilitaireSQL::count($this->getConnection(), $this->getTableName(), ['sheet' => $sheetId]);
    }

    /**
     * {@inheritDoc}
     *
     * @param SellSheetRowVirtualMoney $entity
     * @param PDO $pdo
     * @return void
     */
    public function createInTransaction($entity, PDO $pdo): void
    {
        $id = UtilitaireSQL::insert($this->getConnection(), $this->getTableName(), [
            self::FIELD_DATE_AJOUT => $entity->getFormatedDateAjout(),
            'sheet' => $entity->getSheet()->getId(),
            'money' => $entity->getMoney()->getId(),
            'amount' => $entity->getAmount()
        ]);
        $entity->setId($id);
    }

    public function updateInTransaction($entity, $id, PDO $pdo): void
    {
        throw new DAOException('We cannot perform updating operation at this level');
    }
}