<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\RubricCategory;
use Core\Shivalik\Managers\RubricCategoryDAOManager;
use PDO;
use PHPBackend\Dao\DefaultDAOInterface;
use PHPBackend\Dao\UtilitaireSQL;

/**
 * implementation par defaut de la table de categorisation des rubrique budgetaire
 */
class RubricCategoryDAOManagerImpementation1  extends DefaultDAOInterface implements RubricCategoryDAOManager {
    
    /**
     * sauvegarde la classification des rubrique budgetaire dans la base de donnee
     *
     * @param RubricCategory $entity
     * @param PDO $pdo
     * @return void
     */
    public function createInTransaction($entity, PDO $pdo): void
    {
        $id = UtilitaireSQL::insert($pdo, $this->getTableName(), [
            'label' => $entity->getLabel(),
            'description' => $entity->getDescription(),
            self::FIELD_DATE_AJOUT => $entity->getFormatedDateAjout(),
            'ownable' => ($entity->isOwnable()? 1 : '0')
        ]);
        $entity->setId($id);
    }

    public function checkOwnable(bool $ownable = true): bool
    {
        return $this->checkByColumnName('ownable', $ownable? 1 : '0');
    }

    public function findOwnable(bool $ownable = true): array
    {
        return $this->findAllByColumName('ownable', $ownable? 1 : '0');
    }
}