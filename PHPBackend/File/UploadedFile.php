<?php
namespace PHPBackend\File;

/**
 * 
 * @author Esaie MUHASA
 *
 */
class UploadedFile
{
    private $metadata;

    /**
     * cosntucteur d'initialisation
     * @param array $metadata
     */
    public function __construct(array $metadata)
    {
        $this->metadata=$metadata;
    }
    
    /**
     *
     * @return string|null
     */
    public function getName() {
        if (in_array('name', $this->metadata)) {
            return $this->metadata['name'];
        }
        return null;
    }
    
    /**
     * est-ce un fichier
     * @return boolean
     */
    public function isFile()
    {
        $params = array(
            'name',
            'size'
        );
        
        $name = false;
        $size = false;
        foreach ($this->metadata as $cle => $value) {
            if (in_array($cle, $params)) {
                if (($cle=='name') && (($value != null && $value != ''))) {
                    $name = true;
                } elseif (($cle=='size') && (((int)$value) != 0)) {
                    $size = true;
                }
            }
        }
        return $name && $size;
    }
    
    /**
     *
     * @return int
     */
    public function getSize(){
        if (in_array('size', $this->metadata)) {
            return $this->metadata['size'];
        }
        return 0;
    }
    
    /**
     * Pour verifier s'il n'y a pas eu d'erreur
     * @return boolean
     */
    public function hasError(){
        if (in_array('error', $this->metadata)) {
            return $this->metadata['error']==UPLOAD_ERR_OK? false : true;
        }
        return true;
    }
    
    public function isImage(){
        return in_array($this->getExtension(), ['png', 'jpg', 'jpeg', 'PNG','JPG', 'JPEG']);
    }
    
    /**
     * 
     * @return bool
     */
    public function isPhpFile () : bool {
        return ($this->getExtension() == 'php' || $this->getExtension() == 'php5');
    }
    
    /**
     * @return bool
     */
    public function isWebFile () : bool {
        return in_array($this->getExtension(), ['php', 'html', 'xhtml', 'js', 'htm', 'php5', 'css']);
    }
    
    /**
     * Recuperation de l'extension
     * @return string
     */
    public function getExtension() : ?string{
        $infoFile=pathinfo($this->getName(), PATHINFO_EXTENSION);
        return $infoFile;//(isset($infoFile[PATHINFO_EXTENSION])? $infoFile[PATHINFO_EXTENSION] :'');
    }
    
    /**
     * Revoie le nom du fichier dans le dossier tmp du serveur
     * @return string
     */
    public function getTmpName() : ?string{
        if (in_array('tmp_name', $this->metadata)) {
            return $this->metadata['tmp_name'];
        }
        
        return null;
    }
}

