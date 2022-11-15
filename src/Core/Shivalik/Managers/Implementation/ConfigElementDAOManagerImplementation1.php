<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\BudgetRubric;
use Core\Shivalik\Entities\ConfigElement;
use Core\Shivalik\Managers\ConfigElementDAOManager;
use PDO;
use PHPBackend\Dao\DefaultDAOInterface;
use PHPBackend\Dao\UtilitaireSQL;

class ConfigElementDAOManagerImplementation1 extends DefaultDAOInterface implements ConfigElementDAOManager {

    /**
     * {@inheritDoc}
     *
     * @param ConfigElement $entity
     * @param PDO $pdo
     * @return void
     */
    public function createInTransaction($entity, PDO $pdo): void
    {
        $id = UtilitaireSQL::insert($pdo, $this->getTableName(), [
            self::FIELD_DATE_AJOUT => $entity->getFormatedDateAjout(),
            'percent' => $entity->getPercent(),
            'config' => $entity->getConfig()->getId(),
            'rubric' => $entity->getRubric()->getId()
        ]);
        $entity->setId($id);
    }

    public function checkByConfig(int $configId): bool
    {
        return $this->checkByColumnName('config', $configId);
    }

    public function findByConfig(int $configId): array
    {
        $elements = $this->findAllByColumName('config', $configId);
        foreach ($elements as $element) {
            $element->setRubric($this->getManagerFactory()->getManagerOf(BudgetRubric::class)->findById($element->getRubric()->getId()));
        }
        return $elements;
    }
}