<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\SubConfigElement;
use Core\Shivalik\Managers\SubConfigElementDAOManager;
use PDO;
use PHPBackend\Dao\DefaultDAOInterface;
use PHPBackend\Dao\UtilitaireSQL;

/**
 * implementation par defaut d'interfacage avec la table qui contiens les sous-configuration 
 * de la repartition du budget
 */
class SubConfigElementDAOManagerImplementation1 extends DefaultDAOInterface implements SubConfigElementDAOManager {

    /**
     * sauvegarde de la sous-configuration
     * {@inheritDoc}
     *
     * @param SubConfigElement $entity
     * @param PDO $pdo
     * @return void
     */
    public function createInTransaction($entity, PDO $pdo): void
    {
        $id = UtilitaireSQL::insert($pdo, $this->getTableName(), [
            'percent' => $entity->getPercent(),
            self::FIELD_DATE_AJOUT => $entity->getFormatedDateAjout(),
            'element' => $entity->getRubric()->getId(),
            'rubric' => $entity->getRubric()->getId()
        ]);
        $entity->setId($id);
    }

    public function checkByElement(int $elementId): bool
    {
        return $this->checkByColumnName('element', $elementId);
    }

    public function findByElement(int $elementId): array
    {
        return $this->findAllByColumName('element', $elementId);
    }

}