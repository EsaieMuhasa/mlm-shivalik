<?php
namespace Core\Shivalik\Entities;

/**
 * Tout Element notifiable doit implementer cette interface
 * @author Esaie MUHASA
 * <hr/>
 * <p>
 *  la mis en place de cette interface est venue pour assayer de compler le vide.
 *  Dans la genese de ce systeme, les notifications n'y etaient pas prevue. 
 *  Ainsi, les admins et les membres du syndicat peuvent recevoir des notifcations.
 *  cela ne poserait pas probleme si on suposait que tout le User sont suceptible de recevoir de notification. 
 *  Mains depuis la genese de cette applications, les histoires d'heritages n'ont pas ete implementer dans le BDD
 *  c'est ainsi que cette interface essaie de compler ce vide, en suposentat que tout elements qui suceptible de recevoie une 
 *  notification doit l'implementer
 * </p>
 */
interface Notifiable
{
    /**
     * revoie les donnees
     * doit etre un reference vers l'objet de la classe qui implemente cette interface
     * pour faciliter le mappage, lors du chargement des donnees depuis la BDD
     * @return Object
     */
    public function getData ();
    
    /**
     * Revoie la cle de l'object notifiable (l'ID de l'occurence dans la BDD)
     */
    public function getKey () ;
    
    /**
     * renvoie le surnom de l'object notifiable
     * @return string
     */
    public function getNickname () : string;
}

