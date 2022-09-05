<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Managers\CategoryDAOManager;
use PHPBackend\Dao\DefaultDAOInterface;
use Core\Shivalik\Entities\Category;
use PHPBackend\Dao\UtilitaireSQL;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class CategoryDAOManagerImplementation1 extends DefaultDAOInterface implements CategoryDAOManager
{

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::update()
     * @param Category $entity
     */
    public function update($entity, $id): void
    {
        UtilitaireSQL::update($this->getConnection(), $this->getTableName(), [
            'title' => $entity->getTitle(),
            'description' => $entity->getDescription(),
            self::FIELD_DATE_MODIF => $entity->getFormatedDateModif()
        ], $id);
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DefaultDAOInterface::createInTransaction()
     * @param Category $entity
     */
    public function createInTransaction($entity, \PDO $pdo): void
    {
        $id = UtilitaireSQL::insert($pdo, $this->getTableName(), [
            'title' => $entity->getTitle(),
            'description' => $entity->getDescription(),
            self::FIELD_DATE_AJOUT => $entity->getFormatedDateAjout()
        ]);
        $entity->setId($id);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\CategoryDAOManager::checkByTitle()
     */
    public function checkByTitle(string $title, ?int $id = null): bool
    {
        return $this->checkByColumnName("title", $title, $id);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\CategoryDAOManager::findByTitle()
     */
    public function findByTitle(string $title): Category
    {
        return $this->findByColumnName("title", $title);
    }

}

