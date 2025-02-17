<?php
namespace PHPBackend\File;

use PHPBackend\Config\GlobalConfig;
use PHPBackend\PHPBackendException;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class FileManager
{

    /**
     */
    public function __construct()
    {}
    
    /**
     * ecrit un fichier uploder dans le docier publique du serveur
     * @param UploadedFile $file
     * @param string $fileName
     * @return string chemain absolut vers le fichier sur le serveur
     */
    public static function writeUploadedFile (UploadedFile $file, ?string $fileName = null) : string {
        $dir = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.GlobalConfig::getInstance()->getPublicDirectory();
        
        $matches = array();
        $simpleFileName = null;
        
        if($fileName == null) {
            $now = time();
            $fileName = "file-{$now}-".strtolower($file->getName());
        }
        
        //(.+)(/|\\)([a-zA-Z0-9_]+)\.([a-zA-Z0-9]{1,6})
        if (preg_match("#^(.+)(/|\\\\)(.+)(\.[a-zA-Z]{2,10})$#", $fileName, $matches)) { //recuprerationd de la hierarchie des dossiers
            $dir = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.GlobalConfig::getInstance()->getPublicDirectory().DIRECTORY_SEPARATOR."{$matches[1]}";
            $simpleFileName = "{$matches[3]}{$matches[4]}";
        }
        
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        
        $reelFileName = $dir.DIRECTORY_SEPARATOR.($simpleFileName == null? $fileName : $simpleFileName);
        
        if(!(@move_uploaded_file($file->getTmpName(), $reelFileName))){
            throw new PHPBackendException('Erreur de configuration du serveur. tmpfile_name = '.$file->getTmpName().'; filename='.$reelFileName.'. Echec de recuperation du fichier dans le tmp du serveur');
        }
        
        return $reelFileName;
    }
}

