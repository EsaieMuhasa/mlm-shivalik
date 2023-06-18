<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Managers\MoneyGradeMemberDAOManager;
use PHPBackend\Dao\DefaultDAOInterface;
use PHPBackend\Dao\DAOException;
use Core\Shivalik\Entities\MoneyGradeMember;
use Core\Shivalik\Entities\Office;
use DateTimeInterface;
use PHPBackend\Dao\UtilitaireSQL;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class MoneyGradeMemberDAOManagerImplementation1 extends DefaultDAOInterface implements MoneyGradeMemberDAOManager {
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DefaultDAOInterface::createInTransaction()
     * @param MoneyGradeMember $entity
     */
    public function createInTransaction ($entity, \PDO $pdo): void {
        $id = UtilitaireSQL::insert($pdo, $this->getTableName(), [
            'product' => $entity->getProduct(),
            'afiliate' => $entity->getAfiliate(),
            'gradeMember' => $entity->getGradeMember()->getId(),
            'virtualMoney' => $entity->getVirtualMoney()->getId(),
            self::FIELD_DATE_AJOUT => $entity->getFormatedDateAjout(\DateTime::W3C)
        ]);
        $entity->setId($id);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MoneyGradeMemberDAOManager::checkByGradeMember()
     */
    public function checkByGradeMember (int $gradeMember): bool {
        return $this->checkByColumnName('gradeMember', $gradeMember);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MoneyGradeMemberDAOManager::checkByVirtualMoney()
     */
    public function checkByVirtualMoney(int $virtualMoney, int $offset = 0): bool {
        return $this->checkAllByColumName('virtualMoney', $virtualMoney, 1, $offset);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MoneyGradeMemberDAOManager::findByGradeMember()
     */
    public function findByGradeMember(int $gradeMember): array {
        return $this->findAllByColumName('gradeMember', $gradeMember);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MoneyGradeMemberDAOManager::findByVirtualMoney()
     */
    public function findByVirtualMoney(int $virtualMoney, ?int $limit = null, int $offset = 0): array {
        return $this->findAllByColumName('virtualMoney', $virtualMoney, $limit, $offset);
    }

    public function findByOffice (Office $office, ?DateTimeInterface $date = null, ?DateTimeInterface $max = null) : array {
        $data = [];

        $sql = "SELECT * FROM {$this->getViewName()} WHERE virtualMoney IN (SELECT `id` FROM VirtualMoney WHERE office = :office)";

        $params = [
            'office' => $office->getId()
        ];

        if ($date != null) {
            $sql .= " AND ( dateAjout BETWEEN :min AND :max )";

            $params['min'] = "{$date->format('Y-m-d')} 00:00:00";

            if ($max == null) {
                $params['max'] = "{$date->format('Y-m-d')} 23:59:59";
            } else {
                $params['max'] = "{$max->format('Y-m-d')} 23:59:59";
            }
        }

        $sql .= " ORDER BY dateAjout DESC";

        // var_dump($sql);

        
        try {
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), $sql, $params);
            while ($row = $statement->fetch()) {
                $data[] = new MoneyGradeMember($row);
            }
        } catch (\Exception $e) {
            throw new DAOException($e->getMessage(), 500, $e);
        }

        return $data;
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::update()
     */
    public function update($entity, $id): void {
        throw new DAOException("We cannot perform update operation");
    }


}

