<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\BudgetConfig;
use Core\Shivalik\Entities\ConfigElement;
use Core\Shivalik\Managers\BudgetConfigDAOManager;
use PDO;
use PHPBackend\Dao\DefaultDAOInterface;
use PHPBackend\Dao\UtilitaireSQL;

/**
 * implementation par deafaut d'un point de repere de la configuration de repartiton du budget
 */
class BudgetConfigDAOManagerImplementation1 extends DefaultDAOInterface implements BudgetConfigDAOManager{

    /**
     * renregitrement d'une nouvelle onfiguration
     * {@inheritDoc}
     * @param BudgetConfig $entity
     * @param PDO $pdo
     * @return void
     */
    public function createInTransaction($entity, PDO $pdo): void
    {
        if($this->checkAvailable()) {
            $av = $this->findAvailable();
            UtilitaireSQL::update($pdo, $this->getTableName(), [
                self::FIELD_DATE_MODIF => $entity->getFormatedDateAjout(),
                'available' => '0'
            ], $av->getId());
        }
        $id = UtilitaireSQL::insert($pdo, $this->getTableName(), [
            self::FIELD_DATE_AJOUT => $entity->getFormatedDateAjout()
        ]);
        $entity->setId($id);

        //item de la configuration
        foreach ($entity->getElements() as $item){
            $this->getManagerFactory()->getManagerOf(ConfigElement::class)->createInTransaction($item, $pdo);
        }
        //==
        $entity->setId($id);
    }

    /**
     * selection de la configuration active
     *{@inheritDoc}
     * @return BudgetConfig
     */
    public function findAvailable (): BudgetConfig
    {
        return UtilitaireSQL::findUnique($this->getConnection(),
            $this->getTableName(), $this->getMetadata()->getName(), 'available', '1');
    }

    /**
     * verification de la configuration qui est toutjours activee
     * {@inheritDoc}
     * @return boolean
     */
    public function checkAvailable(): bool
    {
        return $this->checkByColumnName('available', '1');
    }
}