<?php
namespace Library;


/**
 * Utilitaire de base de generation et execution des requette SQL.
 * l'API prise en compte est PDO
 * --------------------------------------------------------------------------------
 * @tutorial cette classe facilite l'execution des requette SQL elementaire comme
 * +INSERT INTO TableName
 * +UPDATE TableName SET column=value WHERE id = x
 * +SELECT * FROM TableName LIMIT y OFFSET h
 * +DELETE FROM TableName WHERE id = x
 * +DELETE FROM TableName WHERE id IN (x1, x2, ..., xn)
 * +SELECT * FROM TableName WHERE id IN (x1, x2, ..., xn)
 * +SELECT * FROM TableName WHERE id NOT IN (x1, x2, ..., xn) LIMIT y OFFSET h
 * +Etc.
 * @author Esaie Muhasa
 *
 */
trait SQLManagerPDO
{
    /**
     * @var \PDO
     */
    protected $pdo;

    /**
     * demande d'initialisation
     * @param DAOManager $manager
     */
    protected  function init(DAOManager $manager) : void{
        $this->pdo = $manager->getFirstConnection();
    }

    /**
     * Pour enregistrer une occurence dans une table de la base dde donnee
     * @param string $tableName le nom de la table dans la catalogue de l'intance de PDO
     * @param array $data les donnes a inserer dans la table
     * @param boolean $returnGeneratedKey s'il faut retourner la cle autogenerer
     * @param boolean $dateAjout s'il faut initialiser la date d'ajout
     * @throws DAOException
     * @return int|void
     */
    protected function pdo_insertInTable($tableName, array $data, $returnGeneratedKey=true, $dateAjout=true)
    {
        $id = $this->pdo_insertInTableTansactionnel($this->pdo, $tableName, $data, $returnGeneratedKey, $dateAjout);
        if ($returnGeneratedKey) {
            return $id;
        }
    }
    
    /**
     * Pour enregistrer une occurence dans une table de la base dde donnee
     * @param \PDO $pdo
     * @param string $tableName le nom de la table dans la catalogue de l'intance de PDO
     * @param array $data les donnes a inserer dans la table
     * @param boolean $returnGeneratedKey s'il faut retourner la cle autogenerer
     * @param boolean $dateAjout s'il faut initialiser la date d'ajout
     * @throws DAOException
     * @return int|void
     */
    protected function pdo_insertInTableTansactionnel(\PDO $pdo, $tableName, array $data, $returnGeneratedKey=true, $dateAjout=true)
    {
        $SQL = 'INSERT INTO '.$tableName.' ( ';
        $SQL_SUITE = ' VALUES (';
        
        $nombreColones = count($data);
        $colone = 1;
        
        $initData = array();
        
        foreach (array_keys($data) as $columnName) {
            $paramName = $columnName;
            $matches = array();
            if (preg_match('#^`(.+)`$#', $columnName, $matches)) {
                $paramName = $matches[1];
            }
            
            $initData[$paramName] =  $data[$columnName];
            
            $SQL .= $columnName.($colone==$nombreColones? ($dateAjout? ', dateAjout)' : ' )') :', ');
            $SQL_SUITE .= ':'.$paramName.($colone==$nombreColones? ($dateAjout? ', NOW() )' : ' )') : ', ');
            if ($colone!=$nombreColones) {
                $colone++;
            }
        }
        
        $SQL .= $SQL_SUITE;
        
        try {
            $result = $pdo->prepare($SQL);
            if ($result == false){
                throw new DAOException("Ehec de préparation de la requête: {$SQL}");
            }
            $status = $result->execute($initData);
            $result->closeCursor();
            if(!$status){
                throw new DAOException('Echec d\'exécution de la requête d\'enregistrement. ré-essayez ultérieurement svp!...');
            }elseif($returnGeneratedKey) {
                return $pdo->lastInsertId();
            }
        } catch (\Exception $e) {

            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
    }


    /***
     * Pour la modification d'une occurence d'une table
     * @param string $tableName le nom de la table
     * @param array $data les donnees des colonne a modifier
     * @param int $id l'identifiant de l'occurence a modifier
     * @param boolean $dateModif si la date de modification doit etre mise a jours
     * @throws DAOException s'il y a erreur lors de la modification ou echec de modification
     * @return void
     */
    protected function pdo_updateInTable($tableName, array $data, $id=null, $dateModif=true)
    {
        $this->pdo_updateInTableTransactionnel($this->pdo, $tableName, $data, $id, $dateModif);
    }
    
    
    /***
     * Pour la modification d'une occurence d'une table,
     * la modification s'effectue dans une transaction deja demarer d'avence
     * @param \PDO $pdo
     * @param string $tableName le nom de la table
     * @param array $data les donnees des colonne a modifier
     * @param int $id l'identifiant de l'occurence a modifier
     * @param boolean $dateModif si la date de modification doit etre mise a jours
     * @throws DAOException s'il y a erreur lors de la modification ou echec de modification
     * @return void
     */
    protected function pdo_updateInTableTransactionnel(\PDO $pdo, string $tableName, array $data, $id=null, $dateModif=true)
    {
        $SQL = 'UPDATE '.$tableName.' SET '.($dateModif? 'dateModif = NOW(), ':'').'';
        $SQL_SUITE = ' WHERE id ='.$id;
        
        $nombreColones = count($data);
        $colone = 1;
        
        $initData = array();
        
        foreach (array_keys($data) as $columnName) {
            $paramName = $columnName;
            $matches = array();
            if (preg_match('#^`(.+)`$#', $columnName, $matches)) {
                $paramName = $matches[1];
            }
            
            $initData[$paramName] =  $data[$columnName];
            
            $SQL .= $columnName.'=:'.$paramName.($colone==$nombreColones? '' : ', ');
            if ($colone!=$nombreColones) {
                $colone++;
            }
        }
        if ($id!=null) {
            $SQL .= $SQL_SUITE;
        }
        
        try {
            $result = $pdo->prepare($SQL);
            if ($result == false){
                throw new DAOException('Une erreur est survenue lors de la préparation de la requête de mise à jour.');
            }
            $status = $result->execute($initData);
            $result->closeCursor();
            
            if(!$status){
                throw new DAOException('Aucune aucurence n\'a été mise à jour!');
            }
        } catch (\Exception $e) {
            throw new DAOException($e->getMessage().'['.$e->getCode().']', DAOException::ERROR_CODE, $e);
        }
    }


    /***
     * Supression d'une occurence d'une table
     * Cette operation est ireversible
     * @param string $tableName le nom de la table
     * @param int $id l'identifiant de l'occurence a suprimer
     * @throws DAOException s'il y arreur lors de la supression
     * @return void
     */
    protected function pdo_deleteInTable(string $tableName, int $id)
    {
        try {
            $statut=$this->pdo->exec('DELETE FROM '.$tableName.' WHERE id='.$id);
            if ($statut==0) {
                throw new DAOException('Aucune occurence n\'a été suprimée définitivement dans la base des données...');
            }
        } catch (\Exception $e) {
            throw new DAOException($e->getMessage().'['.$e->getCode().']', DAOException::ERROR_CODE, $e);
        }
    }
    
    /**
     * Supression d'un occurence dans une transaction deja demarrer d'avence
     * @param \PDO $pdo
     * @param string $tableName
     * @param int $id
     * @throws DAOException
     */
    protected function pdo_deleteInTableTransactionnel(\PDO $pdo, string $tableName, int $id)
    {
        $statut=$pdo->exec('DELETE FROM '.$tableName.' WHERE id='.$id);
        if ($statut==0) {
            throw new DAOException('Aucune occurence n\'a été suprimée définitivement dans la base des données...');
        }
    }

    /**
     * Supression d'une collection des donnes dans une table
     * @param string $tableName
     * @param array $ids
     * @throws DAOException
     * @return number le nombre d'occurence suprimer dans la base de donnees
     */
    protected function pdo_deleteAllInTable(string $tableName, array $ids = array()){
        $SQL_REQUE = 'DELETE FROM '.$tableName;
        if ($ids!=null && count($ids) != 0) {
            $SQL_REQUE .= ' WHERE id IN (';

            for ($i =0; $i<count($ids); $i++) {
                $SQL_REQUE .= $ids[$i].($i != (count($ids)-1)? ',':'');
            }
            $SQL_REQUE .= ')';
        }elseif ($ids==null || count($ids)==0){
            //Reinitialisation de l'incrementation

        }else{
            throw new DAOException('Valeur invalide en deuxième paramètre de la méthode de suppression multiple');
        }

        try {
            $statut = $this->pdo->exec($SQL_REQUE);
            if ($statut==0) {
                throw new DAOException('Aucune occurence n\'a été suprimée définitivement');
            }else return $statut;

        } catch (\Exception $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
    }

    /**
     * Evoie d'une occurence dans la corbeil de la table
     * @param string $tableName le nom de la table
     * @param int $id l'indetifiant de l'ocurence a metre en corbeil
     * @throws DAOException s'il ya erreur lors de la mise en corbeil
     */
    protected function pdo_removeInTable(string $tableName, $id)
    {
        try {
            $statut = $this->pdo->exec('UPDATE '.$tableName.' SET deleted = 1 WHERE id='.$id);
            if ($statut==0) {
                throw new DAOException('Acune occurence n\'a été mise en corbeille');
            }
        } catch (\Exception $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
    }
    
    /**
     * Supression transactionnel dans une table
     * @param \PDO $pdo
     * @param string $tableName
     * @param int $id
     * @throws DAOException
     */
    protected function pdo_removeInTableTransactionnel(\PDO $pdo, string $tableName, $id)
    {
        try {
            $statut = $pdo->exec('UPDATE '.$tableName.' SET deleted = 1 WHERE id='.$id);
            if ($statut==0) {
                throw new DAOException('Acune occurence n\'a été mise en corbeille');
            }
        } catch (\Exception $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
    }

    /**
     * Mise en corbeille d'une collection d'occurence d'une table
     * @param string $tableName
     * @param array $ids
     * @return int le nombre d'occurence mise en cobeille
     * @throws DAOException s'il erreur lors de la mise en corbeille
     */
    protected function pdo_removeAllInTable($tableName, array $ids= array()){
        $SQL_REQUE = 'UPDATE '.$tableName.' SET deleted = 1';
        if (count($ids) != 0) {
            $SQL_REQUE .= ' WHERE id IN (';

            for ($i =0; $i<count($ids); $i++) {
                $SQL_REQUE .= $ids[$i].($i != (count($ids)-1)? ',':'');
            }
            $SQL_REQUE .= ')';
        }

        try {
            $statut = $this->pdo->exec($SQL_REQUE);
            if ($statut==0) {
                throw new DAOException('Aucune occurence d\'a été mise en corbeille ');
            }else return $statut;

        } catch (\Exception $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
    }

    /**
     * REcyclage d'une occurence biens precise
     * @param string $tableName
     * @param int $id
     * @throws DAOException
     */
    protected function pdo_recycleInTable($tableName, $id){
        try {
            $statut = $this->pdo->exec('UPDATE '.$tableName.' SET deleted = 0 WHERE id='.$id);
            if ($statut==0) {
                throw new DAOException('Acune occurence n\'a été recyclée de la corbeille');
            }
        } catch (\Exception $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
    }

    /**
     * Verification si une occurence est dans la poubele d'une table
     * @param string $tableName
     * @param int $id
     * @throws DAOException s'il y a erreur lors de la communication avec la BDD
     * @return boolean
     */
    protected function pdo_isInTableTrash($tableName, $id){

        try {
            $statement = $this->pdo->prepare('SELECT id, deleted FROM '.$tableName.' WHERE id=:id AND deleted=1');
            $statut = $statement->execute(array('id' => $id));
            if(!$statut){
                $statement->closeCursor();
                throw new DAOException('Echec d\'exécution de la requête préparer');
            }

            if ($statement->fetch()) {
                $statement->closeCursor();
                return true;
            }else{
                $statement->closeCursor();
            }
        } catch (\Exception $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }

        return false;
    }

    /**
     * Recyclage des donnees d'une table.
     * @param string $tableName
     * @param array $ids
     * @throws DAOException
     * @return number le nombre d'occurence affecter par l'action de recyclage
     */
    protected function pdo_recycleAllInTable($tableName, array $ids = array()){
        $SQL_REQUE = 'UPDATE '.$tableName.' SET deleted = 0';
        if (count($ids) != 0) {
            $SQL_REQUE .= ' WHERE id IN (';

            for ($i =0; $i<count($ids); $i++) {
                $SQL_REQUE .= $ids[$i].($i != (count($ids)-1)? ',':'');
            }
            $SQL_REQUE .= ')';
        }

        try {
            $statut = $this->pdo->exec($SQL_REQUE);
            if ($statut==0) {
                throw new DAOException('Aucune occurence n\'a été recyclée de la corbeille ');
            }else return $statut;

        } catch (\Exception $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
    }

    /**
     * Verification d'une valeur biens precis dans une colone d'une table
     * @param string $tableName le nom de la table dans la catalogue encours
     * @param string $columnName le nom de la colone dans la table
     * @param mixed $columnValue la valeur a verifier
     * @throws DAOException s'il y erreur lors de la communication avec le SGBD
     * @return boolean true si la veur existe, sinon false
     */
    protected function pdo_columnValueExistInTable($tableName, $columnName, $columnValue, $id=-1)
    {
        try {
            $result = $this->pdo->prepare('SELECT '.$columnName.' FROM '.$tableName.' WHERE '.$columnName.'=:'.$columnName.($id !=-1 ? ' AND id!='.$id : '').' LIMIT 1 OFFSET 0');
            if ($result == false){
                throw new DAOException('Echec de préparation de la requête.');
            }
            $status = $result->execute(array($columnName => $columnValue));

            if(!$status){
                $result->closeCursor();
                throw new DAOException('Echec d\'exécution de la requête. re-essayez svp!...');
            }

            if($result->fetch()){
                $result->closeCursor();
                return true;
            }else {
                $result->closeCursor();
                return false;
            }
        } catch (\Exception $e) {
            throw new DAOException($e->getMessage().'['.$e->getCode().']', DAOException::ERROR_CODE, $e);
        }
    }

    /**
     * Selection de donnee d'une table de la bdd
     * @param string $tableName
     * @param int $limit
     * @param int $offset
     * @param string $entityClasName
     * @return DBEntity[]
     */
    protected function pdo_getAllInTable($tableName, $entityClassName, $limit=-1, $offset=-1)
    {
        $data = array();
        $entityClass = $entityClassName;
        $SQL = 'SELECT * FROM '.$tableName.' WHERE deleted=0 '.' ORDER BY dateAjout DESC '.(($limit!=-1 && $offset!=-1) ? ('LIMIT '.$limit.' OFFSET '.($offset==0? '0': $offset)) : (''));
        try {
            $statement = $this->pdo->prepare($SQL);
            $statut = $statement->execute();
            if ($statut) {
                if ($row = $statement->fetch()) {
                    $data [] = new $entityClass($row, true);
                    while ($row = $statement->fetch()) {
                        $data [] = new $entityClass($row, true);
                    }

                }else throw new DAOException('Aucune donnée retournée pour la requête de sélection');
            }else throw new DAOException('Echec d\'exéctionn de la requête. Ré-essayez svp! ...');
            $statement->closeCursor();
        } catch (\Exception $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        return $data;
    }

    /**
     * Selection de donnee d'une table de la bdd
     * @param string $tableName
     * @param int $limit
     * @param int $offset
     * @param string $entityClasName
     * @return DBEntity[]
     */
    protected function pdo_getTrashInTable($tableName, $entityClassName,  $limit=-1, $offset=-1)
    {
        $data = array();
        $entityClass = $entityClassName;
        $SQL = 'SELECT * FROM '.$tableName.' ORDER BY dateAjout DESC '.(($limit!=-1 && $offset!=-1) ? (' LIMIT '.$limit.' OFFSET '.($offset==0? '0': $offset)) : ('')).' WHERE deleted=1';
        try {
            $statement = $this->pdo->prepare($SQL);
            $statut = $statement->execute();
            if ($statut) {
                if ($row = $statement->fetch()) {
                    $data [] = new $entityClass($row, true);
                    while ($row = $statement->fetch()) {
                        $data [] = new $entityClass($row, true);
                    }
                }else throw new DAOException('Aucune donnée retournée pour la requête de sélection');
            }else throw new DAOException('Echec d\'exéctionn de la requête. Ré-essayez svp! ...');
            $statement->closeCursor();
        } catch (\Exception $e) {
            throw new DAOException($e->getMessage().'['.$e->getCode().']', DAOException::ERROR_CODE, $e);
        }
        return $data;
    }

    /**
     * Pour compter les occurence d'une table
     * @param string $tableName
     * @throws DAOException s'il y a erreur lors de la communication avec la base de donnnes
     * @return int le nombre d'ocurence qui ce trouve dans la table de la bdd
     */
    protected function pdo_countAllInTable($tableName)
    {
        $nombre = 0;
        try {
            $statement = $this->pdo->prepare('SELECT COUNT(*) AS nombre FROM '.$tableName.' WHERE deleted=0');
            $statut = $statement->execute();
            if($statut){
                if($count = $statement->fetch()){
                    $nombre = $count['nombre'];
                }
            }else throw new DAOException('Echec d\'exécution de la requête. Ré-essayez svp! ...');
            $statement->closeCursor();
        } catch (\Exception $e) {
            throw new DAOException($e->getMessage().'['.$e->getCode().']', DAOException::ERROR_CODE, $e);
        }
        return $nombre;
    }

    /**
     * Pour compter les occurence d'une table
     * @param string $tableName
     * @throws DAOException s'il y a erreur lors de la communication avec la base de donnnes
     * @return int le nombre d'ocurence qui ce trouve dans la table de la bdd
     */
    protected function pdo_countInTrashInTable($tableName)
    {
        $nombre = 0;
        try {
            $statement = $this->pdo->prepare('SELECT COUNT(*) AS nombre FROM '.$tableName.' WHERE deleted=1');
            $statut = $statement->execute();
            if($statut){
                if($count = $statement->fetch()){
                    $nombre = $count['nombre'];
                }
            }else throw new DAOException('Echec d\'exécution de la requête. Ré-essayez svp! ...');
            $statement->closeCursor();
        } catch (\Exception $e) {
            throw new DAOException($e->getMessage().'['.$e->getCode().']', DAOException::ERROR_CODE, $e);
        }
        return $nombre;
    }


    /***
     * Recuperation d'une occurance (ou une collection d'occurence) dont l'indice de restruction est en parametre
     * @param string $tableName
     * @param string $entityClassName
     * @param string $columnName
     * @param mixed $columnValue
     * @throws DAOException
     * @return DBEntity
     */
    protected function pdo_uniqueFromTableColumnValue($tableName, $entityClassName, $columnName, $columnValue)
    {
        $data = null;
        try {
            $result = $this->pdo->prepare('SELECT * FROM '.$tableName.' WHERE '.$columnName.'=:'.$columnName.' LIMIT 1 ');
            if ($result == false){
                throw new DAOException('Echec de préparation de la requête. ré-essayez svp!');
            }
            $status = $result->execute(array($columnName => $columnValue));

            if(!$status){
                $result->closeCursor();
                throw new DAOException('Echec d\'exécution de la requête. Ré-essayez svp! ...');
            }
            $className = $entityClassName;
            if($row=$result->fetch()){
                $data = new $className($row, true);
                $result->closeCursor();
            }else {
                $result->closeCursor();
                throw new DAOException('Aucun résultat pour l\'indice du critère de sélection ['.$tableName.'.'.$columnName.' => '.$columnValue.']');
            }
        } catch (\Exception $e) {
            throw new DAOException($e->getMessage().' ['.$e->getCode().']', DAOException::ERROR_CODE, $e);
        }

        return $data;
    }

    /**
     * Recuperation de occurence d'une table en fonction d'un critere
     * @param string $tableName le nom de la table
     * @param string $entityClassName le nom simple de la classe pour le maping du DAO
     * @param string $columnName le nom de la colone pour close WHERE de la resctruction
     * @param mixed $columnValue la valeur de filtrage
     * @param int $limit limitation des resulta
     * @param int $offset pas de selection
     * @throws DAOException s'il y erreur lors de la selection, soit aucun resultat
     * @return DBEntity[]
     */
    protected function pdo_fromTableColumnValue(string $tableName, string $entityClassName, string$columnName, $columnValue, int $limit=-1, int $offset=-1)
    {
        $data = array();
        try {
            $result = $this->pdo->prepare('SELECT * FROM '.$tableName.' WHERE '.$columnName.'=:'.$columnName.' AND deleted=0 '.(($limit!=-1 && $offset!=-1)? (' LIMIT '.$limit.' OFFSET '.($offset!=0? $offset:'0')):''));
            if ($result == false){
                throw new DAOException('Echec de préparation de la requête. ré-essayez svp!');
            }
            $status = $result->execute(array($columnName => $columnValue));

            if(!$status){
                $result->closeCursor();
                throw new DAOException('Echec d\'exécution de la requête. Ré-essayez svp! ...');
            }
            $className = $entityClassName;
            if($row=$result->fetch()){
                $data[] = new $className($row, true);
                while ($row= $result->fetch()) {
                    $data[] = new $className($row, true);
                }
                $result->closeCursor();
            }else {
                $result->closeCursor();
                throw new DAOException('Aucun résultat pour l\'indice du critère de sélection ['.$tableName.'.'.$columnName.' => '.$columnValue.']');
            }
        } catch (\Exception $e) {
            throw new DAOException($e->getMessage().' ['.$e->getCode().']', DAOException::ERROR_CODE, $e);
        }

        return $data;
    }


    /***
     * selection des tout les ocurence d'une table dont leurs identifiant ne sont pas dans la table des identifiant en parametre
     * @param string $tableName
     * @param string $entityClassName
     * @param array $ids taleau des identifiant auquel on doit faire abstraction
     * @param int $limit
     * @param int $offset
     * @throws DAOException
     * @return array
     */
    protected function pdo_selectOtherInTable(string $tableName, string $entityClassName, array $ids, $limit=-1, $offset=-1) : array{
        $data = array();
        try {
            $SQL = 'SELECT * FROM '.$tableName.' WHERE id NOT IN (';

            for ($i = 0; $i < count($ids); $i++) {
                $SQL .= '?'.($i < (count($ids)-1)? ',':'');
            }

            $SQL .= ') AND deleted=0 '.(($limit>-1 && $offset>-1)? (' LIMIT '.$limit.' OFFSET '.($offset!=0? $offset:'0')):'');

            $result = $this->pdo->prepare($SQL);
            if ($result == false){
                throw new DAOException('Echec de préparation de la requête. ré-essayez svp!');
            }
            $status = $result->execute($ids);

            if(!$status){
                $result->closeCursor();
                throw new DAOException('Echec d\'exécution de la requête. Ré-essayez svp! ...');
            }
            $className = $entityClassName;
            if($row=$result->fetch()){
                $data [] = new $className($row, true);
                while ($row= $result->fetch()) {
                    $data[] = new $className($row, true);
                }
                $result->closeCursor();
            }else {
                $result->closeCursor();
                throw new DAOException('Aucun résultat en déhors des critères de restriction "'.$tableName.'"');
            }
        } catch (\Exception $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }

        return $data;
    }


    /***
     * selection d'une collection d'occurence dont leurs identifiant sont dans le tableau en parametre
     * @param string $tableName
     * @param array $ids collection des identifiant
     * @param string $entityClassName
     * @throws DAOException
     * @return array
     */
    protected function pdo_selectFromTableIn(string $tableName, string $entityClassName, array $ids) : array{
        $data = array();
        try {
            $SQL = 'SELECT * FROM '.$tableName.' WHERE id IN (';

            for ($i = 0; $i < count($ids); $i++) {
                $SQL .= '?'.($i < (count($ids)-1)? ',':'');
            }

            $SQL .= ') AND deleted=0 ';

            $result = $this->pdo->prepare($SQL);
            if ($result == false){
                throw new DAOException('Echec de préparation de la requête. ré-essayez svp!');
            }
            $status = $result->execute($ids);

            if(!$status){
                $result->closeCursor();
                throw new DAOException('Echec d\'exécution de la requête. Ré-essayez svp! ...');
            }
            $className = $entityClassName;
            if($row=$result->fetch()){
                $data [] = new $className($row, true);
                while ($row= $result->fetch()) {
                    $data[] = new $className($row, true);
                }
                $result->closeCursor();
            }else {
                $result->closeCursor();
                throw new DAOException('Aucun résultat pour la liste des indices du critère de restriction dans la table "'.$tableName.'"');
            }
        } catch (\Exception $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }

        return $data;
    }
    
    /**
     * recuperation de l'historique des creation des entite
     * @param string $tableName
     * @param string $entityClassName
     * @param \DateTime $dateMin
     * @param \DateTime $dateMax
     * @param array $filters
     * @param int $limit
     * @param int $offset
     * @throws DAOException
     * @return DBEntity[]
     */
    protected function pdo_getCreationHistory (string $tableName, string $entityClassName, \DateTime $dateMin, ?\DateTime $dateMax = null, array $filters = array(), $limit = -1, $offset=-1) {
        $SQL = 'SELECT * FROM '.$tableName.' WHERE dateAjout >= :dateMin  AND dateAjout <=:dateMax ';
        
        if ($dateMax == null) {
            $dateMax = clone $dateMin;
        }
        
        
        
        $params = array();
        
        $params['dateMin'] = $dateMin->format('Y-m-d\T00:00:00');
        $params['dateMax'] = $dateMax->format('Y-m-d\T23:59:59');
        
        if (!empty($filters)) {
            $i = 0;
            $count = count($filters);
            $SQL .= " AND ";
            
            foreach (array_keys($filters) as $name) {
                $SQL .= "{$name}=:{$name}".($i < ($count-1)? ' AND ':'');
                $params[$name] = $filters[$name];
                $i++;
            }
        }
        
        $SQL .= ' AND deleted=0 '.(($limit>-1 && $offset>-1)? (' LIMIT '.$limit.' OFFSET '.($offset!=0? $offset:'0')):'');
        $data = array();
        try {
            $result = $this->pdo->prepare($SQL);
            if ($result == false){
                throw new DAOException('Echec de préparation de la requête. ré-essayez svp!');
            }
            $status = $result->execute($params);
            
            if(!$status){
                $result->closeCursor();
                throw new DAOException('Echec d\'exécution de la requête. Ré-essayez svp! ...');
            }
            $className = $entityClassName;
            if($row=$result->fetch()){
                $data [] = new $className($row, true);
                while ($row= $result->fetch()) {
                    $data[] = new $className($row, true);
                }
                $result->closeCursor();
            }else {
                $result->closeCursor();
                throw new DAOException('Aucun résultat en déhors des critères de restriction "'.$tableName.'"');
            }
        } catch (\Exception $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
    
        return $data;
    }
    
    
    /**
     * @param string $tableName
     * @param string $entityClassName
     * @param \DateTime $dateMin
     * @param \DateTime $dateMax
     * @param array $filters
     * @param int $limit
     * @param int $offset
     * @throws DAOException
     * @return bool
     */
    protected function pdo_hasCreationHistory (string $tableName, \DateTime $dateMin, ?\DateTime $dateMax = null, array $filters = array(), $limit = -1, $offset=-1) : bool {
        $SQL = 'SELECT * FROM '.$tableName.' WHERE (dateAjout >= :dateMin  AND dateAjout <=:dateMax) ';
        
        if ($dateMax == null) {
            $dateMax = clone $dateMin;
        }
        
        $params = array();
        
        $params['dateMin'] = $dateMin->format('Y-m-d\T00:00:00');
        $params['dateMax'] = $dateMax->format('Y-m-d\T23:59:59');
        
        if (!empty($filters)) {
            $i = 0;
            $count = count($filters);
            $SQL .= " AND ";
            foreach (array_keys($filters) as $name) {
                $SQL .= "{$name}=:{$name}".($i < ($count-1)? ' AND ':'');
                $params[$name] = $filters[$name];
                $i++;
            }
        }
        
        $SQL .= ' AND deleted=0 '.(($limit>-1 && $offset>-1)? (' LIMIT '.$limit.' OFFSET '.($offset!=0? $offset:'0')):'');
        $return = false;
        try {
            $result = $this->pdo->prepare($SQL);
            if ($result == false){
                throw new DAOException('Echec de préparation de la requête. ré-essayez svp!');
            }
            $status = $result->execute($params);
            
            if(!$status){
                $result->closeCursor();
                throw new DAOException('Echec d\'exécution de la requête. Ré-essayez svp! ...');
            }
            if($result->fetch()){
                $return = true;
            }
            $result->closeCursor();
        } catch (\Exception $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        
        return $return;
    }
}
