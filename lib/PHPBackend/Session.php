<?php
namespace PHPBackend;

/**
 *
 * @author Esaie MUHASA
 *        
 */
interface Session
{
    /**
     * revoie l'identifiant de la session
     * @return string
     */
    public function getId () : string;
    
    /**
     * renvoie le nom de la session
     * @return string
     */
    public function getName () : string;
    
    /**
     * modification du nom  dela session
     * @param string $name
     */
    public function setName (string $name) : void;
    
    /**
     * renvoie le s statut de la session
     * @return int
     */
    public function getStatus () : int;
    
    /**
     * Revoie un tableau des donnees que garde la session
     * @return mixed[]
     */
    public function getAttributes() : array;
    
    /**
     * Renvoie l'attribut correspondant au name en parametre,
     * dans la session encours
     * @param string $name
     * @return mixed
     */
    public function getAttribute (string $name);
    
    /**
     * Suprime un attribut dans la session en cours
     * @param string $name
     */
    public function removeAttribute (string $name) : void;
    
    /**
     * cette attribut existe dans la session???
     * @param string $name
     */
    public function hasAttribute (string $name) : bool;
    
    /**
     * ajoute un attribut dans la session
     * @param string $name
     * @param mixed $value
     */
    public function addAttribute(string $name, $value) : void;
    
    /**
     * merge les attribut du tablau en parametre dans la session
     * @param array $attribute
     */
    public function addAttributes (array $attribute) : void;

    /**
     * descruction de la session
     * apres appel a cette methode, tout les attribut dans la session sont suprimer, 
     * et l'id de la session n'est plus reconue
     */
    public function destroy () : void;
}

