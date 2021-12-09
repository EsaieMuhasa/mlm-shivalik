<?php
namespace Library;

/**
 *
 * @author Esaie MHS
 *        
 */
final class PDOFactory
{
    private function __construct()
    {}
    
    /**
     * Recuperation d'une instance de PDO
     * @return \PDO
     * 
     */
    public static function getPDOInstance(string $dsn, string $user, ?string $password = null)
    {
        try {
            $pdo = new \PDO($dsn, $user, $password);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(\PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES 'UTF8'");
            $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            return $pdo;
        } catch (\PDOException $e) {
            throw new DAOException('DAO: '.$e->getMessage(), LibException::APP_LIB_ERROR_CODE, $e);
        }
    }
}

