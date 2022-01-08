<?php
namespace Core\Shivalik\Managers;

use PHPBackend\DAOException;
use PHPBackend\Dao\DefaultDAOInterface;
use PHPBackend\Dao\UtilitaireSQL;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class GradeDAOManager extends DefaultDAOInterface
{
    
    /**
     * @var GenerationDAOManager
     */
    protected $generationDAOManager;
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DefaultDAOInterface::findAll()
     */
    public function findAll(?int $limit = null, int $offset = 0) 
    {
        $grades = parent::findAll($limit, $offset);
        foreach ($grades as $grade) {
            $grade->setMaxGeneration($this->generationDAOManager->findById($grade->getMaxGeneration()->getId()));
        }
        return $grades;
    }


    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DefaultDAOInterface::findById()
     */
    public function findById(int $id, bool $forward = true)
    {
        $grade = parent::findById($id, $forward);
        $grade->setMaxGeneration($this->generationDAOManager->findById($grade->getMaxGeneration()->getId(), false));
        return $grade;
    }

    /**
     * To update the pack icon
     * @param int $id
     * @param string $icon
     */
    public function updateIcon (int $id, string $icon) : void {
        UtilitaireSQL::update($this->getConnection(), $this->getTableName(), array('icon' => $icon), $id);
    }
    
    /**
     * @param string $name
     * @param int $id
     * @return bool
     */
    public function checkByName (string $name, ?int $id = null) : bool {
        return $this->columnValueExist('name', $name, $id);
    }
    
    /**
     * @param float $percentage
     * @param int $id
     * @return bool
     */
    public function checkByPercentage (float $percentage, ?int $id = null) : bool {
        return $this->columnValueExist('percentage', $percentage, $id);
    }
    
    
    /**
     * y-a il un grade superieur a celle en parametrre
     * @param int $id
     * @return bool
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
     * 
     * @param int $id
     * @throws DAOException
     * @return \Core\Shivalik\Entities\Grade[]
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

