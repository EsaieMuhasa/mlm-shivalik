<?php
namespace PHPBackend\Http;

use Dompdf\Dompdf;
use PHPBackend\Response;
use PHPBackend\Controller;
use PHPBackend\PHPBackendException;
use PHPBackend\Application;

/**
 *
 * @author Esaie MHS
 *        
 */
final class HTTPResponse implements Response
{
    /**
     * @var HTTPPage
     */
    private $page;
    
    /**
     * @var Application
     */
    private $application;
    
    /**
     * constucuteur d'initialisation
     * @param HTTPApplication $application
     */
    public function __construct(HTTPApplication $application) {
        $this->application = $application;
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\ApplicationComponent::getApplication()
     */
    public function getApplication(): Application
    {
        return $this->application;
    }

    /**
     * Ajout d'un nouveau cookie
     * @param HTTPCookie $cookie
     */
    public function addCookie(HTTPCookie $cookie)
    {
        setcookie($cookie->getName(), $cookie->getValue(), $cookie->getExpiration(), $cookie->getPath(), $cookie->getDomain(),  $cookie->isSecure(), $cookie->isHttpOnly());
    }


    /***
     * Envoie de la demade de supression d'un cookie
     * @param string $name
     */
    public function deleteCookie(string $name)
    {
        
    }
    
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Response::setPage()
     */
    public function setPage(\PHPBackend\Page $page): void
    {
        $this->page = $page;
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Response::sendRedirect()
     */
    public function sendRedirect(string $url) : void
    {
        header('Location: '.$url);
        exit();
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Response::sendError()
     */
    public function sendError(?string $message=null, int $code=self::ERROR_NOT_FOUND) : void
    {
        if($this->getApplication()->getRequest()->getExtensionURI()!='pdf'){
            header('HTTP/1.0 404 Not Found');
        }
            
        $this->page = new HTTPPage($this->getApplication());
        $filename = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Errors'.DIRECTORY_SEPARATOR.'404.php';
        if (file_exists($filename)) {
            $this->page->setViewFile(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Errors'.DIRECTORY_SEPARATOR.'404');
        }else {
            $this->page->setViewFile(dirname(__DIR__).DIRECTORY_SEPARATOR."DefaultLayouts".DIRECTORY_SEPARATOR."404");
        }
        $this->getApplication()->getRequest()->addAttribute('message', ($message==null? "Désolez! Aucune réssource ne correspond à l\'URL '{$this->getApplication()->getRequest()->getURI()}' dans l'application '{$this->getApplication()->getName()}'": $message));
        $this->getApplication()->getRequest()->addAttribute('code', $code);
        $this->send();
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Response::sendException()
     */
    public function sendException(PHPBackendException $exception) : void
    {
//         var_dump($exception);exit();
        //$this->getApplication()->logger($exception);
        
        if($this->getApplication()->getRequest()->getExtensionURI()!='pdf'){
            header('HTTP/1.0 404 Not Found');
        }
        
        $this->page = new HTTPPage($this->getApplication());
        $filename = dirname(__DIR__, 3).DIRECTORY_SEPARATOR.'Errors'.DIRECTORY_SEPARATOR.'exception.php';
        
        if (file_exists($filename)) {
            $this->page->setViewFile(dirname(__DIR__, 2).DIRECTORY_SEPARATOR.'Errors'.DIRECTORY_SEPARATOR.'exception');
        }else {
            $this->page->setViewFile(dirname(__DIR__).DIRECTORY_SEPARATOR."DefaultLayouts".DIRECTORY_SEPARATOR."exception");
        }
        
        $this->getApplication()->getRequest()->addAttribute('exception', $exception);
        $this->send();
    }
    

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Response::addHeader()
     */
    public function addHeader($header) : void
    {
        header($header);
    }
    

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Response::send()
     * pur les url ayant les extension .xml, .json, .htm, les temptate particulier sont carement utiliser pour la 
     */
    public function send() : void
    {
        if($this->getApplication()->getRequest()->getExtensionURI()=='pdf'){//pour les fichier PDF
            $this->sendPDF();
        }else{//par defaut (pour tout les reste des page page)
            exit($this->page->getGeneratedPage());
        }
    }
    
    /**
     * Envoei d'un vue au format PDF
     * @param string $fileName
     * @param string $orientation l'orientation des pages (landscape ou port)
     * @param string $size
     */
    public function sendPDF(?string $fileName = null, string $orientation='portrait', string $size = 'A4'){
        
        //def("DOMPDF_ENABLE_REMOTE", false);
        $pdf = new Dompdf();
        $html = $this->page->getGeneratedPDF();
        //exit($html);
        $pdf->loadHtml($html);
        $pdf->setPaper($size, $orientation);
        $pdf->render();
        
        exit($pdf->stream(($fileName == null? $this->getApplication()->getConfig()->get('website').'-document-'.time() : $fileName).'.pdf', array('Attachment' => 0)));
    }
    

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Response::sendMail()
     */
    public function sendMail(array $destinataires, string $subject, string $viewFile, Controller $controller=null) : bool{
    	$header = array();
        $header[]="MIME-Version: 1.0";
        $header[]='From:"OnlineDIAB"<webmaster@onlinediab.com>';
        $header[]='Content-Type: text/html; charset="uft-8"';
        $header[]='Content-Transfer-Encoding: 8bit';
        
        $page = new HTTPPage($controller->getApplication());
        $viewFile = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Applications'.DIRECTORY_SEPARATOR.$this->getApplication()->getName().DIRECTORY_SEPARATOR.'Modules'.DIRECTORY_SEPARATOR.$controller->getModule().DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'Mails'.DIRECTORY_SEPARATOR.$viewFile;
        $page->setViewFile($viewFile);
        $message = $page->getGeneratedMail();
        $send = @mail(implode(",", $destinataires), $subject, $message, implode("\r\n", $header));

        //$this->addHeader('Content-Type: text/html; charset=UTF-8');
        
        return $send;
    }
}

