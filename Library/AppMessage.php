<?php
namespace Library;

/**
 *
 * @author Esaie MHS
 * Cette classe existe dans la logique de conservation des message critique dans la session.
 * cela a un rolle extement important pour les actions qui n'ont pas de vue (la plus part des supressions). 
 * dans ce genre de situation on coserve le message dans la session de l'utilisateur, et la prochaine fois qu'il accede au 
 * serveur on inclue le message dans le layout de la l'application (en meme temps on retire le message dans sa session)
 */
class AppMessage
{
    const MESSAGE_ERROR = 1;//Pour les messages d\'erreurs
    const MESSAGE_WARGING = 2;//Pour les avertissement
    const MESSAGE_INFORMATION = 3;//Pour les message d'information simple
    const MESSAGE_SUCCESS = 4;//Pour les messages de succees
    
    /**
     * le type de message d'erreur
     * @var int
     */
    private $type;
    
    /**
     * le contenue du message
     * @var string
     */
    private $description;
    
    /**
     * le titre du message
     * @var string
     */
    private $title;
    
    /**
     * constructeur d'initialisation des parametre d'un message
     * @param string $messageContent
     * @param string $messageTitle
     * @param string $messageType
     */
    public function __construct(?string $title, ?string $description, int $type = self::MESSAGE_WARGING)
    {
        $this->title = $title;
        $this->description = $description;
        $this->type = $type;
    }
    
    
    /**
     * pour rendre tout les attribut de cette classe accessible en lecture seul
     * @param string $attributeName
     * @throws LibException si l'attribut n'existe pas dans la classe, ou si son accesseur n'existe pas
     * @return mixed
     */
    public function __get($attributeName)
    {
        $method = 'get'.ucfirst($attributeName);
        if (is_callable(array($this, $method))) {
            return $this->$method();
        }else throw new LibException('L\'attribut '.$attributeName.' n\'est pas definie dans la classe AppMessage');
    }
    
    /**
     * @return number
     */
    public function getType() : int
    {
        return $this->type;
    }
    
    /**
     * Renvoie la classe du type de message d'erreur
     * @return string
     */
    public function getClassType() : string{
        switch ($this->getType()) {
            case self::MESSAGE_ERROR:
                return 'danger';
                break;
                
            case self::MESSAGE_WARGING:
                return 'warning';
                break;
                
            case self::MESSAGE_INFORMATION:
                return 'info';
                break;
                
            case self::MESSAGE_SUCCESS:
                return 'success';
                break;
        }
        return 'default';
    }
    
    /**
     * Type du message d'erreur en toute letre
     * @return string
     */
    public function getTypeName() : string{
        switch ($this->getType()) {
            case self::MESSAGE_ERROR:
                return 'Erreur';
                break;
            
            case self::MESSAGE_WARGING:
                return 'Avertissement';
                break;
                
            case self::MESSAGE_INFORMATION:
                return 'Information';
                break;
                
            case self::MESSAGE_SUCCESS:
                return 'Succès de l\'opération';
                break;
        }
        return 'Message d\'alert';
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

}

