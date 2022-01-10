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
     */
    public static function writeUploadedFile (UploadedFile $file, ?string $fileName = null) : void {
        $dir = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.($fileName == null? GlobalConfig::getInstance()->getPublicDirectory() : "Web");
        
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        $fileName = $dir.DIRECTORY_SEPARATOR.($fileName == null? ($file->getName()) : $fileName);
        
        if(!(@move_uploaded_file($file->getTmpName(), $fileName))){
            throw new PHPBackendException('Erreur de configuration du serveur. tmpfile_name = '.$file->getTmpName().'; filename='.$fileName.'. Echec de recuperation du fichier dans le tmp du serveur');
        }
    }
}

