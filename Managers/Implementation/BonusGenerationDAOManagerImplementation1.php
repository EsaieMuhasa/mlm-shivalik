<?php
namespace Managers\Implementation;

use Managers\BonusGenerationDAOManager;
use Library\DAOException;
use Managers\MemberDAOManager;
use Entities\BonusGeneration;
use Managers\GenerationDAOManager;
use Managers\GradeMemberDAOManager;

/**
 *
 * @author Esaie MHS
 *        
 */
class BonusGenerationDAOManagerImplementation1 extends BonusGenerationDAOManager
{
    /**
     * @var MemberDAOManager
     */
    protected $memberDAOManager;
    
    /**
     * @var GenerationDAOManager
     */
    protected $generationDAOManager;
    
    /**
     * @var GradeMemberDAOManager
     */
    protected $gradeMemberDAOManager;
    
    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::create()
     */
    public function create($entity)
    {
        try {
            if ($this->pdo->beginTransaction()) {
                $this->createInTransaction($entity, $this->pdo);
                $this->pdo->commit();
            }else {
                throw new DAOException("An error occurred while creating the transaction");
            }
        } catch (\PDOException $e) {
            throw new DAOException("An error occurred in the plain banefice sharing transaction: {$e->getMessage()}", intval($e->getCode()), $e);
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::update()
     */
    public function update($entity, $id)
    {
        throw new DAOException("no subsequent update of the benefit is authorized");
    }
    
    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::createInTransaction()
     * @param BonusGeneration $bonus
     */
    public function createInTransaction($bonus, $api): void
    {        
        $id = $this->pdo_insertInTableTansactionnel($api, $this->getTableName(), array(
            'generator' => $bonus->getGenerator()->getId(),
            'member' => $bonus->getMember()->getId(),
            'generation' => $bonus->getGeneration()? $bonus->getGeneration()->getId() : null,
            'amount' => $bonus->getAmount()
        ));
        $bonus->setId($id);
    }

}

