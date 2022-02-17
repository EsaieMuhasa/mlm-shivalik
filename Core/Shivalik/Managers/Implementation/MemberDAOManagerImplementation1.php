<?php
namespace Core\Shivalik\Managers\Implementation;


use Core\Shivalik\Entities\Localisation;
use Core\Shivalik\Entities\Member;
use Core\Shivalik\Managers\MemberDAOManager;
use PHPBackend\Dao\DAOEvent;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\UtilitaireSQL;

/**
 *
 * @author Esaie MHS
 *        
 */
class MemberDAOManagerImplementation1 extends MemberDAOManager
{
   
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::findChild()
     */
    public function findChild(int $memberId, int $foot): Member
    {
        
        $child = null;
        try {
            $statement = $this->getConnection()->prepare("SELECT * FROM {$this->getTableName()} WHERE parent=:parent AND foot=:foot");
            if($statement->execute(array('parent' => $memberId, 'foot' => $foot))){
                if($row = $statement->fetch()){
                    $child = new Member($row);
                } else {
                    $statement->closeCursor();
                    throw new DAOException("no children on the foot {$foot}");
                }
            } else {
                $statement->closeCursor();
                throw new DAOException("Query failed to execute");
            }
            
            $statement->closeCursor();
            
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        
        return $child;
    }

    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::checkChild()
     */
    public function checkChild(int $memberId, int $foot): bool
    {
        return UtilitaireSQL::checkAll(
            $this->getConnection(),
            $this->getTableName(),
            ['parent' => $memberId, 'foot' => $foot]
        );
    }

    /**
     * {@inheritDoc}
     * @see MemberDAOManager::hasChilds()
     */
    public function checkChilds(int $memberId): bool
    {
        return UtilitaireSQL::checkAll($this->getConnection(), $this->getTableName(), ['parent' => $memberId]);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::checkParent()
     */
    public function checkParent (int $memberId): bool
    {
        $return = false;
        try {
            $statement = $this->pdo->prepare("SELECT id FROM {$this->getTableName()} WHERE id=:id AND parent IS NOT NULL");
            if($statement->execute(array('id' => $memberId))){
                if($statement->fetch()){
                    $return = true;
                }
            } else {
                $statement->closeCursor();
                throw new DAOException("Query failed to execute");
            }
            
            $statement->closeCursor();
            
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        
        return $return;
    }

    /**
     * {@inheritDoc}
     * @see MemberDAOManager::hasSponsor()
     */
    public function checkSponsor(int $memberId): bool
    {
        $return = false;
        try {
            $statement = $this->pdo->prepare("SELECT id FROM {$this->getTableName()} WHERE id=:id AND sponsor IS NOT NULL");
            if($statement->execute(array('id' => $memberId))){
                if($statement->fetch()){
                    $return = true;
                }
            } else {
                $statement->closeCursor();
                throw new DAOException("Query failed to execute");
            }
            
            $statement->closeCursor();
            
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        
        return $return;
    }

    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::createInTransaction()
     * @param Member $entity
     */
    public function createInTransaction($entity, \PDO $pdo): void
    {
        if ($entity->getLocalisation() != null) {
            $this->getDaoManager()->getManagerOf(Localisation::class)->createInTransaction($entity->getLocalisation(), $pdo);
        }
        $id = UtilitaireSQL::insert($pdo, $this->getTableName(), [            
            'name' => $entity->getName(),
            'postName' => $entity->getPostName(),
            'lastName'=> $entity->getLastName(),
            'pseudo' => $entity->getPseudo(),
            'password' => $entity->getPassword(),
            'email' => $entity->getEmail(),
            'kind' => $entity->getKind(),
            'telephone' => $entity->getTelephone(),
            'foot' => $entity->getFoot(),
            'parent' => ($entity->getParent()!=null? $entity->getParent()->getId() : null),
            'sponsor' => ($entity->getSponsor()!=null? $entity->getSponsor()->getId() : null),
            'admin' => ($entity->getAdmin()!=null? $entity->getAdmin()->getId() : null),
            'office' => ($entity->getOffice()!=null? $entity->getOffice()->getId() : null),
            'localisation'=>($entity->getLocalisation()!=null? $entity->getLocalisation()->getId() : null),
            self::FIELD_DATE_AJOUT => $entity->getFormatedDateAjout()
        ]);
        
        $entity->setId($id);
        
        $matricule = $entity->generateMatricule();
        
        UtilitaireSQL::update($pdo, $this->getTableName(), ['matricule' => $matricule], $id);
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::update()
     * @param Member $entity
     */
    public function update($entity, $id) : void
    {
        UtilitaireSQL::update($this->getConnection(), $this->getTableName(), [
            'name' => $entity->getName(),
            'postName' => $entity->getPostName(),
            'lastName'=> $entity->getLastName(),
            'email' => $entity->getEmail(),
            'telephone' => $entity->getTelephone(),
            self::FIELD_DATE_MODIF => $entity->getFormatedDateModif()
        ], $id);
        $event = new DAOEvent($this, DAOEvent::TYPE_UPDATION, $entity);
        $this->dispatchEvent($event);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::search()
     */
    public function search($indexs) : array
    {
        $data = [];
        $indexs = is_array($indexs)? $indexs : [$indexs];
        $restruct = [];//conserve le identifiant des membres deja selectionner, pour faciliter la restruction pour les requettes suiventes
        $selectedIndex = [];//les indexs qui doivement etre utiliser dans la suite, apres selection selon lematricule et le pseudo de connexion
        foreach ($indexs as $index) {//premier niveau de recherche, pour est elements uniques
            $partiels = [];
            if ($this->checkByMatricule($index)) {
                $partiels[] = $this->findByMatricule($index);
            }
            
            if ($this->checkByPseudo($index)) {
                $partiels[] = $this->findByPseudo($index);
            }
            
            if (empty($partiels)) {//s'il on n'a rien eu pour la selection prioritarie
                $selectedIndex[] = $index;
                continue;
            }
            
            foreach ($partiels as $user) {
                if (!$this->inArray($data, $user)) {
                    $data [] = $user;
                    $restruct[] = $user->getId();
                }
            }
        }
        
        if (!empty($selectedIndex)) {
            $sql1 = "SELECT DISTINCT * FROM {$this->getTableName()} WHERE ";//deuxieme categorie de recherche, pour le index qui correpond exactement au nom des utilisateur
            $sql2 = $sql1;//et au finish, recherche aproximative
            
            $sqlParams = [];
            $sql2Params = [];
            
            $sqlContent = ["", "", ""];
            $sql2Content = ["", "", ""];
            
            $max = count($selectedIndex);
            foreach ($selectedIndex as $key => $index) {
                $last = $max == ($key+1);
                
                $sqlContent[0] .= ":name{$key}".($last? '':', ');
                $sqlContent[1] .= ":postName{$key}".($last? '':', ');
                $sqlContent[2] .= ":lastName{$key}".($last? '':', ');
                
                $sql2Content[0] .= "name LIKE :name{$key}".($last? '':' OR ');
                $sql2Content[1] .= "postName LIKE :postName{$key}".($last? '':' OR ');
                $sql2Content[2] .= "lastName LIKE :lastName{$key}".($last? '':' OR ');
                
                $sqlParams["name{$key}"] = strtoupper($index);
                $sqlParams["postName{$key}"] = strtoupper($index);
                $sqlParams["lastName{$key}"] = $index;
                
                $sql2Params["name{$key}"] = strtoupper("{$index}%");
                $sql2Params["postName{$key}"] = strtoupper("{$index}%");
                $sql2Params["lastName{$key}"] = "{$index}%";
            }
            
            $sql1 .= "((name IN ({$sqlContent[0]})) OR (postName IN({$sqlContent[1]})) OR (lastName IN ({$sqlContent[2]})))";
            $sql2 .= " ({$sql2Content[0]}) OR ({$sql2Content[1]}) OR ({$sql2Content[2]})";
            
            if (!empty($restruct)) {
                $sql1 .= " AND (id NOT IN (".implode(", ", $restruct)."))";
            }
            
            $sql1 .= "  ORDER BY name ";
            
            try {
                $pdo = $this->getConnection();
                $statement = UtilitaireSQL::prepareStatement($pdo, $sql1, $sqlParams);
                
                while ($row = $statement->fetch()) {
                    $member = new Member($row);
                    $data[] = $member;
                    $restruct[] = $member->getId();
                }
                $statement->closeCursor();
                
            
                if (!empty($restruct) ) {//pour faire une restruction aux donnees deja selectionner parmis ceux qui sont plus prioritaire                    
                    $not = implode(", ", $restruct);
                    $sql2 .= " AND (id NOT IN ({$not}))";
                }
                
                $sql2 .= " ORDER BY name";
                $statement2 = UtilitaireSQL::prepareStatement($pdo, $sql2, $sql2Params);
                while ($row = $statement2->fetch()) {
                    $data[] = new Member($row);
                }
                $statement2->closeCursor();
            } catch (\PDOException $e) {
                throw  new DAOException($e->getMessage()." => {$sql1}", DAOException::ERROR_CODE, $e);
            }
            
        }
        
        
        if (empty($data)) {
            throw new DAOException("Sorry. No results for the search index. Please rephrase your search index(es)");
        }
        
        return $data;
    }
    
    /**
     * Est-ce que cette utilisateur existe dans cette collection des utilisateurs??
     * @param Member[] $members
     * @param Member $member
     * @return bool
     */
    private function inArray (array $members, Member $member) : bool {
        foreach ($members as $m) {
            if ($m->getId() == $member->getId()) {
                return true;
            }
        }
        
        return false;
    }
    

    
    

}

