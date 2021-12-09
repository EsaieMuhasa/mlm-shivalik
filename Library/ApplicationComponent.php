<?php
namespace Library;

/**
 *Classe de base pour gerer l'injection de dependance
 * @author Esaie MHS
 * @tutorial cette classe ...    
 */
abstract class ApplicationComponent
{
    /**
     * @var Application
     */
    private $application;
    
    use StringCrypteur;
    
    /**
     * Constructeur d''initialisation
     * @param Application $application l'a reference  ....
     */
    public function __construct(Application $application)
    {
        if ($application==null) {
            throw new LibException('Le composant de l\'application ne peut pas fonctionner avec une refference null de l\'application');
        }
        $this->application= $application;
    }
    
    /**
     * @return \Library\Application
     */
    public function getApplication()
    {
        return $this->application;
    }
    
    /**
     * Debugale des variables
     * @param mixed ...$variable
     */
    public function debug(...$variable) : void{
        echo '<pre>';
        foreach ($variable as $var) {
            var_dump($var);
        }
        echo '</pre>';
        exit();
    }

}

