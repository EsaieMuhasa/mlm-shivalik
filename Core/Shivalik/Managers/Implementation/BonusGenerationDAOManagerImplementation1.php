<?php
namespace Core\Shivalik\Managers\Implementation;


use Core\Shivalik\Entities\BonusGeneration;
use Core\Shivalik\Managers\BonusGenerationDAOManager;
use PHPBackend\Dao\UtilitaireSQL;

/**
 *
 * @author Esaie MHS
 *        
 */
class BonusGenerationDAOManagerImplementation1 extends BonusGenerationDAOManager
{
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::createInTransaction()
     * @param BonusGeneration $bonus
     */
    public function createInTransaction($bonus, \PDO $pdo): void
    {        
        $id = UtilitaireSQL::insert($this->getConnection(), $this->getTableName(), array(            
            'generator' => $bonus->getGenerator()->getId(),
            'member' => $bonus->getMember()->getId(),
            'generation' => $bonus->getGeneration()? $bonus->getGeneration()->getId() : null,
            'amount' => $bonus->getAmount(),
            'dateAjout' => $bonus->getDateAjout()->format('Y-m-d')
        ), true);
        
        $bonus->setId($id);
    }

}

