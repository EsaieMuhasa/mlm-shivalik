<?php
namespace PHPBackend\Dao;

use PHPBackend\DBEntity;

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
final class UtilitaireSQL
{
    private function __construct(){}
    
    /**
     * preparation d'un requette
     * @param \PDO $pdo
     * @param string $sql
     * @param array $params
     * @throws DAOException
     * @throws \PDOException
     * @return \PDOStatement
     */
    public static  function prepareStatement (\PDO $pdo, string $sql, array $params = []) : \PDOStatement {
        /**
         * @var \PDOStatement $result
         */
        $statement = $pdo->prepare($sql);
        if ($statement === false){
            throw new DAOException('Une erreur est survenue lors de la préparation de la requête.');
        }
        $status = $statement->execute($params);
        if(!$status){
            throw new DAOException('Une erreur est survenue lars de l\'execution de la requête');
        }
        
        return $statement;
    }
    
    /**
     * Pour enregistrer une occurence dans une table de la base dde donnee
     * @param \PDO $pdo
     * @param string $tableName le nom de la table dans la catalogue de l'intance de PDO
     * @param array $data les donnes a inserer dans la table
     * @param boolean $returnGeneratedKey s'il faut retourner la cle autogenerer
     * @throws DAOException
     * @return int|void
     */
    public static function insert (\PDO $pdo, string $tableName, array $data, $returnGeneratedKey=true) {
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
            
            $SQL .= $columnName.($colone==$nombreColones?  ' ) ' :', ');
            $SQL_SUITE .= ':'.$paramName.($colone==$nombreColones? ' )' : ', ');
            if ($colone != $nombreColones) {
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
    public static function update (\PDO $pdo, string $tableName, array $data, $id=null) : void {
        $SQL = 'UPDATE '.$tableName.' SET ';
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
    

    /**
     * Supression d'un occurence dans une transaction deja demarrer d'avence
     * @param \PDO $pdo
     * @param string $tableName
     * @param int $id
     * @throws DAOException
     */
    public static  function delete (\PDO $pdo, string $tableName, string $columnName, $whereValue) : void {
        try {
            $result = $pdo->prepare("DELETE FROM {$tableName}  WHERE {$columnName} = :{$columnName}");
            if ($result == false){
                throw new DAOException('Une erreur est survenue lors de la préparation de la requête de mise à jour.');
            }
            $status = $result->execute(array($columnName => $whereValue));
            $result->closeCursor();
            if(!$status){
                throw new DAOException('Aucune occurence n\'a été suprimée définitivement dans la base des données...');
            }
        } catch (\Exception $e) {
            throw new DAOException($e->getMessage().'['.$e->getCode().']', DAOException::ERROR_CODE, $e);
        }
    }
    
    /**
     * Supression d'une collection des donnes dans une table
     * @param \PDO $pdo
     * @param string $tableName
     * @param array $ids
     * @throws DAOException
     * @return number le nombre d'occurence suprimer dans la base de donnees
     */
    public static function deleteAll(\PDO $pdo, string $tableName, ?array $ids = array()){
        $SQL_REQUE = '';
        if ($ids!=null && count($ids) != 0) {
            $SQL_REQUE .= "DELETE FROM {$tableName} WHERE id IN (";
            
            for ($i =0; $i<count($ids); $i++) {
                $SQL_REQUE .= $ids[$i].($i != (count($ids)-1)? ',':'');
            }
            $SQL_REQUE .= ')';
        }elseif ($ids==null || count($ids)==0){
            //Reinitialisation de l'incrementation
            $SQL_REQUE = "TRUNCATE {$tableName}";
        } else {
            throw new DAOException('Valeur invalide en deuxième paramètre de la méthode de suppression multiple');
        }
        
        try {
            $statut = $pdo->exec($SQL_REQUE);
            if ($statut==0) {
                throw new DAOException('Aucune occurence n\'a été suprimée définitivement');
            }else return $statut;
        } catch (\Exception $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
    }
    
    
    /**
     * Verification d'une valeur biens precis dans une colone d'une table
     * @param \PDO $pdo
     * @param string $tableName le nom de la table dans la catalogue encours
     * @param string $columnName le nom de la colone dans la table
     * @param mixed $columnValue la valeur a verifier
     * @throws DAOException s'il y erreur lors de la communication avec le SGBD
     * @return boolean true si la veur existe, sinon false
     */
    public static function columnValueExist(\PDO $pdo, string $tableName, string $columnName, $columnValue, $id = null) : bool {
        $return = false;
        try {
            $result = $pdo->prepare('SELECT '.$columnName.' FROM '.$tableName.' WHERE '.$columnName.'=:'.$columnName.($id !== null ? ' AND id!='.$id : '').' LIMIT 1 OFFSET 0');
            if ($result == false){
                throw new DAOException('Echec de préparation de la requête.');
            }
            $status = $result->execute(array($columnName => $columnValue));
            
            if(!$status){
                $result->closeCursor();
                throw new DAOException('Echec d\'exécution de la requête. re-essayez svp!...');
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
    
    /**
     * Selection de donnee d'une table de la bdd
     * @param \PDO $pdo
     * @param string $tableName
     * @param string $entityClasName
     * @param string $columnOrder
     * @param array $filters
     * @param int $limit
     * @param int $offset
     * @return Object[]
     */
    public static function findAll(\PDO $pdo, string $tableName, string $entityClassName, string $columnOrder, bool $deskOrder = true, array $filters = array(), ?int $limit=null, int $offset=0) {
        $data = array();
        $entityClass = $entityClassName;
        
        $where = '';
        if (!empty($filters)) {
            $where = ' WHERE ';
            foreach (array_keys($filters) as $key => $columnName) {
                $where .= $columnName .'=:'.$columnName. (count($filters) == ($key+1) ? '' : ' AND ');
            }
        }
        $SQL = "SELECT * FROM {$tableName} {$where} ORDER BY {$columnOrder} ".($deskOrder? "DESC" : "ASCK").($limit!==null ? (' LIMIT '.$limit.' OFFSET '.($offset==0? '0': $offset)) : (''));
        try {
            //die ($SQL);
            $statement = $pdo->prepare($SQL);
            $statut = $statement->execute($filters);
            if ($statut) {
                while ($row = $statement->fetch()) {
                    $data [] = new $entityClass($row);
                }
                $statement->closeCursor();
                if (empty($data)){
                    throw new DAOException('Aucune donnée retournée pour la requête de sélection');
                }
            }else {
                $statement->closeCursor();
                throw new DAOException('Echec d\'exéctionn de la requête. Ré-essayez svp! ...');
            }
        } catch (\Exception $e) {
            throw new DAOException("Error in selection query: {$SQL}", DAOException::ERROR_CODE, $e);
        }
        return $data;
    }
    
    /**
     * Pour compter les occurence d'une table
     * @param string $tableName
     * @throws DAOException s'il y a erreur lors de la communication avec la base de donnnes
     * @return int le nombre d'ocurence qui ce trouve dans la table de la bdd
     */
    public static function count(\PDO $pdo, string $tableName, array $filters = array()) : int
    {
        $nombre = 0;
        $where = '';
        if (!empty($filters)) {
            $where = ' WHERE ';
            foreach (array_keys($filters) as $key => $columnName) {
                $where .= $columnName .'=:'.$columnName. (((count($filters)-1) === $key) ? '' : ' AND ');
            }
        }
        try {
            $statement = $pdo->prepare("SELECT COUNT(*) AS nombre FROM {$tableName} {$where}");
            $statut = $statement->execute($filters);
            
            if($statut){
                if($count = $statement->fetch()){
                    $nombre = $count['nombre'];
                }
            }else {
                $statement->closeCursor();
                throw new DAOException('Echec d\'exécution de la requête. Ré-essayez svp! ...');
            }
            $statement->closeCursor();
        } catch (\Exception $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        return $nombre;
    }
    
    /**
     * verifie s'il a des donnes pour l'itervale en parametre
     * @param \PDO $pdo
     * @param string $tableName
     * @param array $filters
     * @param int $limit
     * @param int $offset
     * @throws DAOException
     * @return bool
     */
    public static function checkAll(\PDO $pdo, string $tableName, array $filters = array(), ?int $limit = null, int $offset = 0) : bool {
        $check = false;
        $where = '';
        if (!empty($filters)) {
            $where = ' WHERE ';
            
            $keys = array_keys($filters);
            $count = count($keys);
            foreach ($keys as $key => $columnName) {
                $where .= $columnName .'=:'.$columnName. ((($count-1) === $key) ? '' : ' AND ');
            }
        }
        try {
            $SQL  = "SELECT * FROM {$tableName} {$where} LIMIT ".($limit!==null? $limit : "1" )." OFFSET {$offset}";
            //die($SQL);
            $statement = $pdo->prepare($SQL);
            $statut = $statement->execute($filters);
            
            if($statut){
                if($statement->fetch()){
                    $check = true;
                }
            }else {
                $statement->closeCursor();
                throw new DAOException('Echec d\'exécution de la requête. Ré-essayez svp! ...');
            }
            $statement->closeCursor();
        } catch (\Exception $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        return $check;
    }
    
    /**
     * verifie s'il aumoin une occurence dans la table
     * @param \PDO $pdo
     * @param string $tableName
     * @return bool
     */
    public static function hasData (\PDO $pdo, string $tableName) : bool {
        return self::checkAll($pdo, $tableName);
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
    public static function findUnique(\PDO $pdo, string $tableName, string $entityClassName, string $columnName, $columnValue)
    {
        $data = null;
        try {
            $result = $pdo->prepare('SELECT * FROM '.$tableName.' WHERE '.$columnName.'=:'.$columnName.' LIMIT 1 ');
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
    
    
    /***
     * selection des tout les ocurence d'une table dont les valeurs  ne sont pas ou sont dans la colonne de la table en parametre
     * <p>N.B: le parametre de filtrage utilise la compinaison AND</p>
     * @param string $tableName
     * @param string $entityClassName
     * @param array $values taleau des identifiant auquel on doit faire abstraction
     * @param array $filters
     * @param int $limit
     * @param int $offset
     * @throws DAOException
     * @return array
     */
    public static function findIn(\PDO $pdo, string $tableName, string $entityClassName, string $columnName,  array $values, bool $in= true, array $filters = array(), ?int $limit=null, $offset=0) : array{
        $data = array();
        
        try {
            $SQL = 'SELECT * FROM '.$tableName.' WHERE id '.($in? '':'NOT').' IN (';
            $args = $values;
            
            for ($i = 0; $i < count($values); $i++) {
                $SQL .= '?'.($i < (count($values)-1)? ',':'');
            }
            
            $andFilter = "";
            if (!empty($filters)) {
                $andFilter = " AND (";
                foreach ($filters as $key => $value) {
                    $andFilter .= "{$key} = ? ".($i < (count($filters)-1)? ' AND ':'');
                    $args[] = $value;
                }
                $andFilter .= ")";
            }
            
            $SQL .= ") {$andFilter} ".(($limit>-1 && $offset>-1)? (' LIMIT '.$limit.' OFFSET '.($offset!=0? $offset:'0')):'');
            
            $result = $pdo->prepare($SQL);
            
            if ($result === false){
                throw new DAOException('Echec de préparation de la requête. ré-essayez svp!');
            }
            $status = $result->execute($args);
            
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
     * recuperation de l'historique des creation des entite
     * <p>columName mast be date or datetime type</p>
     * @param string $tableName
     * @param string $entityClassName
     * @param string $columnName
     * @param bool $dateTime
     * @param \DateTime $dateMin
     * @param \DateTime $dateMax
     * @param array $filters
     * @param int $limit
     * @param int $offset
     * @throws DAOException
     * @return DBEntity[]
     */
    public static function findCreationHistory (\PDO $pdo, string $tableName, string $entityClassName, string $columnName, bool $dateTime, \DateTime $dateMin, ?\DateTime $dateMax = null, array $filters = array(), ?int $limit = null, int $offset=0) {
        $SQL = "SELECT * FROM {$tableName} WHERE {$columnName} >= :dateMin AND {$columnName} <=:dateMax ";
        
        if ($dateMax == null) {
            $dateMax = $dateMin;
        }

        $params = array();
        
        $params['dateMin'] = $dateMin->format('Y-m-d\T00:00:00');
        $params['dateMax'] = $dateMax->format('Y-m-d\T23:59:59');
        
        if (!empty($filters)) {
            $i = 0;
            $count = count($filters);
            
            $SQL .= ' AND ';
            
            foreach (array_keys($filters) as $name) {
                $SQL .= "{$name}=:{$name}".((($i + 1) == $count)? '':' AND ');
                $params[$name] = $filters[$name];
                $i++;
            }
        }
        
        $SQL .= ($limit !== null? (" LIMIT {$limit} OFFSET ".($offset!=0? $offset:'0')):'');
        $data = array();
        try {
            $result = $pdo->prepare($SQL);
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
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        
        return $data;
    }
    
    /**
     * comptage des operarions selons leurs historiques
     * @param \PDO $pdo
     * @param string $tableName
     * @param string $columnName
     * @param bool $dateTime
     * @param \DateTime $dateMin
     * @param \DateTime $dateMax
     * @param array $filters
     * @throws DAOException
     * @return int
     */
    public static function countByCreationHistory (\PDO $pdo, string $tableName, string $columnName, bool $dateTime, \DateTime $dateMin, ?\DateTime $dateMax = null, array $filters = array()) : int{
        $SQL = "SELECT COUNT(*) AS nombre FROM {$tableName} WHERE {$columnName} >= :dateMin AND {$columnName} <=:dateMax ";
        
        if ($dateMax == null) {
            $dateMax = $dateMin;
        }

        $params = array();
        
        $params['dateMin'] = $dateMin->format('Y-m-d\T00:00:00');
        $params['dateMax'] = $dateMax->format('Y-m-d\T23:59:59');
        
        $andFilter = "";
        
        if (!empty($filters)) {
            $i = 0;
            $count = count($filters);
            $andFilter = " AND ";
            
            foreach (array_keys($filters) as $name) {
                $SQL .= "{$name}=:{$name}".($i < ($count-1)? ' AND ':'');
                $params[$name] = $filters[$name];
                $i++;
            }
        }
        
        $SQL .= " {$andFilter}";
        $count = 0;
        try {
            $result = $pdo->prepare ($SQL);
            if ($result == false){
                throw new DAOException('Echec de préparation de la requête. ré-essayez svp!');
            }
            $status = $result->execute ($params);
            
            if (!$status) {
                $result->closeCursor();
                throw new DAOException('Echec d\'exécution de la requête. Ré-essayez svp! ...');
            }
            
            if($row=$result->fetch()){
                $count = $row['nombre'];
            }
            
            $result->closeCursor();
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        
        return $count;
    }
    
    /**
     * verifie s'il y a une historique
     * @param \PDO $pdo
     * @param string $tableName
     * @param string $columnName
     * @param bool $dateTime
     * @param \DateTime $dateMin
     * @param \DateTime $dateMax
     * @param array $filters
     * @param int $limit
     * @param int $offset
     * @throws DAOException
     * @return boolean
     */
    public static function hasCreationHistory (\PDO $pdo, string $tableName, string $columnName, bool $dateTime, \DateTime $dateMin, ?\DateTime $dateMax = null, array $filters = array(), ?int $limit = null, int $offset=0) {
        $SQL = "SELECT * FROM {$tableName} WHERE {$columnName} >= :dateMin AND {$columnName} <=:dateMax ";
        
        if ($dateMax == null) {
            $dateMax = $dateMin;
        }

        $params = array();
        
        $params['dateMin'] = $dateMin->format('Y-m-d\T00:00:00');
        $params['dateMax'] = $dateMax->format('Y-m-d\T23:59:59');
        
        if (!empty($filters)) {
            $i = 0;
            $count = count($filters);
            $SQL .= " AND ";
            
            foreach (array_keys($filters) as $name) {
                $SQL .= "{$name}=:{$name}".(($i + 1) != ($count)? ' AND ':'');
                $params[$name] = $filters[$name];
                $i++;
            }
        }
        
        $SQL .= ($limit !== null? (' LIMIT '.$limit.' OFFSET '.($offset!=0? $offset:'0')):'');

        $return = false;
        
        try {
            $result = $pdo->prepare($SQL);
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

