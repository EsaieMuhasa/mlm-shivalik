<?php
namespace PHPBackend\Validator;

use PHPBackend\Request;
use PHPBackend\DBEntity;
use PHPBackend\PHPBackendException;

/**
 *
 * @author Esaie MUHASA
 *        
 */
interface FormValidator
{
    /**
     * revoie la collection des message d'erreurs
     * @return array
     */
    public function getErrors() : array;
    
    /**
     * revoie le feednack error dont la clee est en parametre
     * @param string $key
     * @return ValidationFeedback|NULL
     */
    public function getFeedback(string $key) : ?ValidationFeedback;
    
    /**
     * il y a il erreur??
     * @param string $key
     * @return bool
     */
    public function hasMessage(?string $key = null) : bool;
    
    /**
     * ce feedback exxite-il??
     * @param string $key
     * @return bool
     */
    public function hasFeedback(?string $key=null) : bool;
    
    /**
     * supresseion de d'un feedback dans la collection des feedbacks
     * @param string $key
     */
    public function removeFeedback(string $key) : void;
    
    /**
     * ajoute un feedback dans la collection des feedbacks
     * @param string $key
     * @param ValidationFeedback $feedback
     */
    public function addFeedback(string $key, ValidationFeedback $feedback) : void;
    
    /**
     * ajout d'un message d'erreur speceifique dans la collection des message
     * @param string $name
     * @param string $message
     */
    public function addMessage(string $name, string $message) : void;
    
    /**
     * retoyage des erreurs
     * @return void
     */
    public function clear() : void;
    
    /**
     * collection particulier des messages
     * @return array
     */
    public function getMessages() : array;
    
    /**
     * renvoie le message generale apreves valisation
     * @return string|NULL
     */
    public function getResult() : ?string;
    
    /**
     * revoie sous titre du resultat apres valisation
     * @return string|NULL
     */
    public function getMessage() : ?string;
    
    /**
     * collection des messages specifiques
     * @param array $messages
     */
    public function setMessages(array $messages) : void;
    
    /**
     * inclut les resultat feedback des messages d'erreur dans la requette
     * @param Request $request
     */
    public function includeFeedback(Request $request) : void;
    
    /**
     * sub result
     * @param string $message
     * @param bool $inErrors
     */
    public function setMessage(string $message, bool $inErrors=true) : void;
    
    /**
     * Validation d'un entite et ajout dans la bdd si les donnees sont valide
     * @param Request $request
     * @return DBEntity
     */
    public function createAfterValidation(Request $request);
    
    /**
     * Validation de la mise ajour d'une entite
     * @param Request $request
     * @return DBEntity l'objet deja mise ajour
     */
    public function updateAfterValidation(Request $request);
    
    /**
     * Demande de supresion d'une occurence dans la base de donnee
     * @param Request $request
     * @return DBEntity l'occurence suprimer dansla base de donnees
     *
     * @tutorial Il est probat que la supression soit impossible. Dans ce cas, si
     * les autres occurence font reference a l'occurence encours de supression, alors ceux-ci
     * vont etre integrer dans le $_REQUEST...
     */
    public function deleteAfterValidation(Request $request);
    
    /**
     * Demande de mise en corbeille d'une occurence
     * @param Request $request
     * @return DBEntity
     */
    public function moveToTrashAfterValidation(Request $request);
    
    /**
     * Demande de recuperation d'une occurence deja mis en corbeil
     * @param Request $request
     * @return DBEntity
     */
    public function recycleAfterValidation(Request $request);
    
    /**
     * Demande de supression definitive d'une collection d'occurence
     * @param Request $request
     * @return DBEntity[]
     */
    public function deleteAllAfterValidation(Request $request);
    
    
    /**
     * Demande de mise en corbeil d'une collection d'occurences
     * @param Request $request
     * @throws PHPBackendException
     * @return DBEntity[]
     */
    public function moveAllToTrashAfterValidation(Request $request);
    
    /**
     * Recyclage d'une collection d'occurence
     * @param Request $request
     * @throws PHPBackendException
     * @return DBEntity
     */
    public function recycleAllAfterValidation(Request $request);
    
}

