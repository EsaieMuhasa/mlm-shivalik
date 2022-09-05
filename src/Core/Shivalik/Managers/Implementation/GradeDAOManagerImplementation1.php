<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\Grade;
use Core\Shivalik\Managers\GenerationDAOManager;
use Core\Shivalik\Managers\GradeDAOManager;
use PHPBackend\Dao\DAOEvent;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\DefaultDAOInterface;
use PHPBackend\Dao\UtilitaireSQL;

/**
 *
 * @author Esaie MHS
 *        
 */
class GradeDAOManagerImplementation1 extends DefaultDAOInterface implements GradeDAOManager
{
    
    /**
     * @var GenerationDAOManager
     */
    protected $generationDAOManager;
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DefaultDAOInterface::createInTransaction()
     * @param Grade $entity
     */
    public function createInTransaction($entity, \PDO $pdo): void
    {
        $id = UtilitaireSQL::insert($pdo, $this->getTableName(), [
            'name' => $entity->getName(),
            'icon' => $entity->getIcon(),
            'maxGeneration' => $entity->getMaxGeneration()->getId(),
            'percentage' => $entity->getPercentage(),
            'amount' => $entity->getAmount(),
            self::FIELD_DATE_AJOUT => $entity->getFormatedDateAjout()
        ]);
        $entity->setId($id);
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::update()
     * @param Grade $entity
     */
    public function update($entity, $id) : void
    {
        
        $data = [
            'name' => $entity->getName(),
            'maxGeneration' => $entity->getMaxGeneration()->getId(),
            'percentage' => $entity->getPercentage(),
            'amount' => $entity->getAmount(),
            self::FIELD_DATE_MODIF => $entity->getFormatedDateModif()
        ];
        
        if ($entity->getIcon() != null ) {
            $data['icon'] = $entity->getIcon();
        }
        
        UtilitaireSQL::update($this->getConnection(), $this->getTableName(), $data, $id);
        
        $event = new DAOEvent($this, DAOEvent::TYPE_UPDATION, $entity);
        $this->dispatchEvent($event);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\GradeDAOManager::findAll()
     * @return Grade[]
     */
    public function findAll(?int $limit = null, int $offset = 0) : array
    {
        $all = UtilitaireSQL::findAll($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), "percentage", true, [], $limit, $offset);
        
        foreach ($all as $grade) {
            $grade->setMaxGeneration($this->generationDAOManager->findById($grade->getMaxGeneration()->getId()));
        }
        return $all;
    }
    
    
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DefaultDAOInterface::findById()
     * @return Grade
     */
    public function findById($id, bool $forward = true)
    {
        $grade = parent::findById($id, $forward);
        $grade->setMaxGeneration($this->generationDAOManager->findById($grade->getMaxGeneration()->getId(), false));
        return $grade;
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\GradeDAOManager::updateIcon()
     */
    public function updateIcon (int $id, string $icon) : void {
        UtilitaireSQL::update($this->getConnection(), $this->getTableName(), array('icon' => $icon), $id);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\GradeDAOManager::checkByName()
     */
    public function checkByName (string $name, ?int $id = null) : bool {
        return $this->columnValueExist('name', $name, $id);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\GradeDAOManager::checkByPercentage()
     */
    public function checkByPercentage (float $percentage, ?int $id = null) : bool {
        return $this->columnValueExist('percentage', $percentage, $id);
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\GradeDAOManager::checkUpTo()
     */
    public function checkUpTo (int $id) : bool{
        $all = $this->findAll();
        $current = $this->findById($id);
        
        foreach ($all as $grade) {
            if ($current->getAmount() < $grade->getAmount()) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\GradeDAOManager::findUpTo()
     */
    public function findUpTo (int $id) : array {
        $current = $this->findById($id);
        
        if (!$this->checkUpTo($id)) {
            throw new DAOException("no packet greater than '{$current->getName()}' paket");
        }
        
        $up = array();
        $all = $this->findAll();
        
        foreach ($all as $grade) {
            if ($current->getAmount() < $grade->getAmount()) {
                $up[] = $grade;
            }
        }
        
        return $up;
    }

}

