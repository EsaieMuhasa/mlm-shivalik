<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\Output;
use Core\Shivalik\Managers\OutputDAOManager;
use PDO;
use PHPBackend\Dao\DefaultDAOInterface;
use PHPBackend\Dao\UtilitaireSQL;

/**
 * implementation par defaut pour l'interfacage ave la table qui sauvegrde les sorties en caisse
 */
class OutputDAOManagerImplementtion1 extends DefaultDAOInterface implements OutputDAOManager {

    /**
     * sauvegarde d'une sortie
     *
     * @param Output $entity
     * @param PDO $pdo
     * @return void
     */
    public function createInTransaction($entity, PDO $pdo): void
    {
        $id = UtilitaireSQL::insert($pdo, $this->getTableName(), [
            self::FIELD_DATE_AJOUT => $entity->getFormatedDateAjout(),
            'amount' => $entity->getAmount(),
            'rubric' => $entity->getRubric()->getId(),
            'description' => $entity->getDescription()
        ]);
    }

    public function checkByRubric(int $rubricId): bool
    {
        return $this->checkByColumnName('rubric', $rubricId);
    }

    public function findByRubric(int $rubricId): array
    {
        return $this->findAllByColumName('rubric', $rubricId);
    }
}