<?php
namespace Library;

/**
 *
 * @author Esaie MHS
 *        
 */
trait StringCrypteur
{
    
    /**
     * Recuperation de la clee de cryptage
     * @return string
     */
    protected static function getCryptingKey(){
        return sha1('MUHA$A-2020-2021-ES@IE-01056-MEMOIRE');
    }
    
    
    /**
     * Encriptage d'une chaine de caractere sur 64 bits
     * @param string $data
     * @return void
     */
    protected function encryption($data){
        if ($data==null || strlen(trim($data))==0) {
            return null;
        }
        return openssl_encrypt($data, "AES-128-ECB" , self::getCryptingKey());
    }
    
    /**
     * Decriptage des donnees textuel encripter au prealable.
     * @param string $data
     * @return string
     */
    protected function decryption($data){
        if ($data==null || strlen(trim($data))==0) {
            return null;
        }
        return openssl_decrypt($data, "AES-128-ECB" , self::getCryptingKey());
    }
    
}

