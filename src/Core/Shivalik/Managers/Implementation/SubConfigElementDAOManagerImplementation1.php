<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\SubConfigElement;
use Core\Shivalik\Managers\SubConfigElementDAOManager;
use PDO;
use PDOException;
use PHPBackend\Dao\DAOException;
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
            'config' => $entity->getConfig()->getId(),
            'rubric' => $entity->getRubric()->getId()
        ]);
        $entity->setId($id);
    }

    public function createAll(array $entities): void
    {
        try {
            $pdo = $this->getConnection();
            if(!$pdo->beginTransaction()) {
                throw new DAOException("Error on start stransation");
            }

            /** @var SubConfigElement */
            foreach ($entities as $entity) {
                $id = UtilitaireSQL::insert($pdo, $this->getTableName(), [
                    'percent' => $entity->getPercent(),
                    self::FIELD_DATE_AJOUT => $entity->getFormatedDateAjout(),
                    'config' => $entity->getConfig()->getId(),
                    'rubric' => $entity->getRubric()->getId()
                ]);
                $entity->setId($id);
            }

            $pdo->commit();

        } catch (PDOException $e) {
            throw new DAOException("Error occurend in transation: {$e->getMessage()}", 500, $e);
        }
    }

    public function checkByElement(int $elementId): bool
    {
        return $this->checkByColumnName('config', $elementId);
    }

    public function findByElement(int $elementId): array
    {
        return $this->findAllByColumName('config', $elementId);
    }

}