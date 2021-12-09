<?php
namespace Library;

use Dompdf\Dompdf;

/**
 *
 * @author Esaie MHS
 *        
 */
final class HTTPResponse extends ApplicationComponent
{
    /**
     * @var Page
     */
    private $page;
    
    /**
     * Ajout d'un nouveau cookie
     * @param Cookie $cookie
     */
    public function addCookie(Cookie $cookie)
    {
        setcookie($cookie->getName(), $cookie->getValue(), $cookie->getExpiration(), $cookie->getPath(), $cookie->getDomain(),  $cookie->isSecure(), $cookie->isHttpOnly());
    }
    
    /**
     * @param \Library\Page $page
     */
    public function setPage(Page $page)
    {
        $this->page = $page;
    }

    /***
     * Envoie de la demade de supression d'un cookie
     * @param string $name
     */
    public function deleteCookie(string $name)
    {
        setcookie(new Cookie($this->getApplication(), $name));
    }
    
    
    /**
     * Enveoie de la demande de redirection
     * cette methode coupe l'executio de la requette
     * @param string $url
     */
    public function sendRedirect(string $url)
    {
        header('Location: '.$url);
        exit();
    }
    
    /**
     * Envoie d'une erreur HTTP
     * @param string $errorMessage le message d'erreur 
     * @param number $errorCode le code d'erreur
     * @return void
     */
    public function sendError(?string $errorMessage=null, int $errorCode=404)
    {
        if($this->getApplication()->getHttpRequest()->getExtensionURI()!='pdf'){
            header('HTTP/1.0 404 Not Found');
        }
            
        $this->page = new Page($this->getApplication());
        $this->page->setViewFile(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Errors'.DIRECTORY_SEPARATOR.'404');
        $this->page->addAttribute('errorMessage', ($errorMessage==null? "Désolez! Aucune réssource ne correspond à l\'URL '{$this->getApplication()->getHttpRequest()->getURI()}'": $errorMessage));
        $this->page->addAttribute('errorCode', $errorCode);
        $this->send();
    }
    
    /**
     * Revoie de la descriptiion d'une exception
     * @param LibException $exception
     */
    public function sendException(LibException $exception)
    {
        
        $this->getApplication()->logger($exception);
        if($this->getApplication()->getHttpRequest()->getExtensionURI()!='pdf'){
            header('HTTP/1.0 404 Not Found');
        }
        
        $this->page = new Page($this->getApplication());
        $this->page->setViewFile(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Errors'.DIRECTORY_SEPARATOR.'exception');
        $this->page->addAttribute('exception', $exception);
        $this->send();
    }
    
    /**
     * Pour ajouter une en-tete http
     * @param string $header l'en-tete a ajouter
     * @return void
     */
    public function addHeader($header) : void
    {
        header($header);
    }
    
    /**
     * Pour envoyer la page generer a l'utilisateur
     * pur les url ayant les extension .xml, .json, .htm, les temptate particulier sont carement utiliser pour la 
     * generation de la page
     * @return void
     */
    public function send() : void
    {
        if($this->getApplication()->getHttpRequest()->getExtensionURI()=='pdf'){//pour les fichier PDF
            $this->sendPDF();
        }else{//par defaut (pour tout les reste des page page)
            exit($this->page->getGeneratedPage());
        }
    }
    
    /**
     * Envoei d'un vue au format PDF
     * @param string $orientation l'orientation des pages (landscape ou port)
     * @param string $size
     */
    public function sendPDF(string $orientation='portrait', string $size = 'A4'){
        
        require_once (__DIR__).DIRECTORY_SEPARATOR.'ExternalDependencies'.DIRECTORY_SEPARATOR.'dompdf'.DIRECTORY_SEPARATOR.'autoload.inc.php';
        
        //def("DOMPDF_ENABLE_REMOTE", false);
        $pdf = new Dompdf();
        $html = $this->page->getGeneratedPDF();
        //exit($html);
        $pdf->loadHtml($html);
        $pdf->setPaper($size, $orientation);
        $pdf->render();
        
        exit($pdf->stream('onlinediab-com-document.pdf', array('Attachment' => 0)));
    }
    
    /**
     * Demande d'envoie d'un email a un utilisateur
     * @param string $destinateur
     * @param string $subject
     * @param string $viewFile
     * @param Controller $controller
     * @return boolean
     */
    public function sendMail($destinateur, $subject, Controller $controller, $viewFile) : bool{
    	$header = array();
        $header[]="MIME-Version: 1.0";
        $header[]='From:"OnlineDIAB"<webmaster@onlinediab.com>';
        $header[]='Content-Type: text/html; charset="uft-8"';
        $header[]='Content-Transfer-Encoding: 8bit';
        
        $page = new Page($controller->getApplication());
        $viewFile = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Applications'.DIRECTORY_SEPARATOR.$this->getApplication()->getName().DIRECTORY_SEPARATOR.'Modules'.DIRECTORY_SEPARATOR.$controller->getModule().DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'Mails'.DIRECTORY_SEPARATOR.$viewFile;
        $page->setViewFile($viewFile);
        $message = $page->getGeneratedMail();
        $send = @mail($destinateur, $subject, $message, implode("\r\n", $header));

        //$this->addHeader('Content-Type: text/html; charset=UTF-8');
        
        return $send;
    }
}

