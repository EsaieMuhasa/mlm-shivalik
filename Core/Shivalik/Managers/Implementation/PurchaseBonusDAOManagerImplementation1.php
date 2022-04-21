<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Managers\PurchaseBonusDAOManager;
use Core\Shivalik\Entities\PurchaseBonus;
use PHPBackend\Dao\UtilitaireSQL;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class PurchaseBonusDAOManagerImplementation1 extends AbstractBonusDAOManager implements PurchaseBonusDAOManager {
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DefaultDAOInterface::createInTransaction()
     * @param PurchaseBonus $bonus
     */
    public function createInTransaction($bonus, \PDO $pdo): void {
        $id = UtilitaireSQL::insert($this->getConnection(), $this->getTableName(), array(
            'generator' => $bonus->getGenerator()->getId(),
            'member' => $bonus->getMember()->getId(),
            'generation' => $bonus->getGeneration(),
            'command' => $bonus->getCommand()->getId(),
            'amount' => $bonus->getAmount(),
            'dateAjout' => $bonus->getDateAjout()->format('Y-m-d')
        ), true);
        $bonus->setId($id);
    }


}

