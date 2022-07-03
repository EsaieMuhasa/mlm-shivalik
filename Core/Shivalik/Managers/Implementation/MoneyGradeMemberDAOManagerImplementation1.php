<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Managers\MoneyGradeMemberDAOManager;
use PHPBackend\Dao\DefaultDAOInterface;
use PHPBackend\Dao\DAOException;
use Core\Shivalik\Entities\MoneyGradeMember;
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

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::update()
     */
    public function update($entity, $id): void {
        throw new DAOException("We cannot perform update operation");
    }


}

