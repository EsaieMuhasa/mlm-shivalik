<?php
namespace Managers;

use Library\AbstractDAOManager;
use Entities\Grade;
use Library\DAOException;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class GradeDAOManager extends AbstractDAOManager
{
    
    /**
     * @var GenerationDAOManager
     */
    protected $generationDAOManager;
    
    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::getAll()
     * @return Grade[]
     */
    public function getAll($limit = -1, $offset = -1)
    {
        $grades = parent::getAll($limit, $offset);
        foreach ($grades as $grade) {
            $grade->setMaxGeneration($this->generationDAOManager->getForId($grade->getMaxGeneration()->getId()));
        }
        return $grades;
    }


    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::getForId()
     * @return Grade
     */
    public function getForId(int $id, bool $forward = true)
    {
        $grade = parent::getForId($id, $forward);
        $grade->setMaxGeneration($this->generationDAOManager->getForId($grade->getMaxGeneration()->getId()));
        return $grade;
    }

    /**
     * To
     * @param int $id
     * @param string $icon
     */
    public function updateIcon (int $id, string $icon) : void {
        $this->pdo_updateInTable($this->getTableName(), array(
            'icon' => $icon
        ), $id, false);
    }
    /**
     * @param string $name
     * @param int $id
     * @return bool
     */
    public function nameExist (string $name, int $id =-1) : bool {
        return $this->columnValueExist('name', $name, $id);
    }
    
    /**
     * @param float $percentage
     * @param int $id
     * @return bool
     */
    public function percentageExist (float $percentage, int $id =-1) : bool {
        return $this->columnValueExist('percentage', $percentage, $id);
    }
    
    
    /**
     * y-a il un grade superieur a celle en parametrre
     * @param int $id
     * @return bool
     */
    public function upExist (int $id) : bool{
        $all = $this->getAll();
        $current = $this->getForId($id);
        
        foreach ($all as $grade) {
            if ($current->getAmount() < $grade->getAmount()) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * 
     * @param int $id
     * @throws DAOException
     * @return \Entities\Grade[]
     */
    public function getUpTo (int $id) {        
        $current = $this->getForId($id);
        
        if (!$this->upExist($id)) {
            throw new DAOException("no packet greater than '{$current->getName()}' paket");
        }
        
        $up = array();
        $all = $this->getAll();
        
        foreach ($all as $grade) {
            if ($current->getAmount() < $grade->getAmount()) {
                $up[] = $grade;
            }
        }
        
        return $up;
    }
}

