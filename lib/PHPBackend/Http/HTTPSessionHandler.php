<?php
namespace PHPBackend\Http;


/**
 *
 * @author Esaie MUHASA
 *        
 */
class HTTPSessionHandler implements \SessionHandlerInterface
{
    /**
     * chemain vers fichier de serialisation des session
     * @var string
     */
    private $savePath;
    
    /**
     * renvoie la collection des sessions ouvert sur le serveur
     * @return \SplObjectStorage
     */
    public static function getSessions () : \SplObjectStorage{
        $path = session_save_path();
        $dir = new \DirectoryIterator($path);
        
        $spl = new \SplObjectStorage();
        foreach ($dir as $file) {
            $match = array();
            if (preg_match("#^(.*)sess_(.+)$#", $file->getFilename(), $match)) {
                $id = $match[2];
                
                $filename = $file->getPathname();
                $content = "";
                
                if (file_exists($filename)) {
                    $content = @file_get_contents($filename);
                }
                
                $session = new HTTPSession($content, $id);

                $spl->attach($session, $id);
            }
        }
        
        return $spl;
    }
    
    /**
     * @param string $id
     * @return HTTPSession|NULL
     */
    public static function getSession (string $id) : ?HTTPSession {
        $path = session_save_path();
        $dir = new \DirectoryIterator($path);
        
        foreach ($dir as $file) {
            $match = array();
            if (preg_match("#^(.*)sess_(.+)$#", $file->getFilename(), $match)) {
                
                if ($id != $match[2]) {
                    continue;
                }
                
                $filename = $file->getPathname();
                $content = "";
                
                if (file_exists($filename)) {
                    $content = @file_get_contents($filename);
                }
                
                $session = new HTTPSession($content, $id);
                
                return $session;
            }
        }
        
        return null;
    }
    
    /**
     * {@inheritDoc}
     * @see \SessionHandlerInterface::close()
     */
    public function close()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     * @see \SessionHandlerInterface::destroy()
     */
    public function destroy($sessionId)
    {
        $file = "{$this->savePath}/sess_{$sessionId}";//le fichier qui contiens les informations de la session
        
        if (file_exists($file)) {
            unlink($file);
        }
        
        return true;
    }

    /**
     * {@inheritDoc}
     * @see \SessionHandlerInterface::gc()
     */
    public function gc($maxlifetime)
    {
        foreach (glob("{$this->savePath}/sess_*") as $file) {
            if (filemtime($file) + $maxlifetime < time() && file_exists($file)) {
                unlink($file);
            }
        }
        
        return true;
    }

    /**
     * {@inheritDoc}
     * @see \SessionHandlerInterface::open()
     */
    public function open($savePath, $sessionName) : bool
    {
        $this->savePath = $savePath;
        if (!is_dir($this->savePath)) {
            mkdir($this->savePath, 0777);
        }
        return true;
    }

    /**
     * {@inheritDoc}
     * @see \SessionHandlerInterface::read()
     */
    public function read($sessionId)
    {
        $file = "{$this->savePath}/sess_{$sessionId}";
        if (file_exists($file)) {
            return @file_get_contents($file);
        }
        return "";
    }

    /**
     * {@inheritDoc}
     * @see \SessionHandlerInterface::write()
     */
    public function write($sessionId, $sessionData)
    {
        return @file_put_contents("{$this->savePath}/sess_{$sessionId}", $sessionData) === false ? false : true;
    }

    
}

