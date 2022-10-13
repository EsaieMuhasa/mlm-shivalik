<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\ConfigElement;
use Core\Shivalik\Managers\ConfigElementDAOManager;
use PDO;
use PHPBackend\Dao\DefaultDAOInterface;
use PHPBackend\Dao\UtilitaireSQL;

class ConfigElementDAOManagerImplementtion1 extends DefaultDAOInterface implements ConfigElementDAOManager {

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
        return $this->findAllByColumName('config', $configId);
    }
}