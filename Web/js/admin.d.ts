/**
 * @author Esaie Muhasa
 * @description le 16/02/2021
 */
declare class SearchMember {
    private form;
    private container;
    private field;
    /**
     * @param {HTMLFormElement} form le formultaire concerner
     * @param {HTMLElement} container le conteneur de resultat de recherche
     */
    constructor(form: HTMLFormElement, container: HTMLElement);
    /**
     * Envoie d'une requette de recherche au serveur
     * @param {string} action l'url a la quel la requette doit etre acheminer
     * @param {string} type le type de requette HTTP a transmetre
     */
    sendRequest(action: string, type?: string): void;
    /**
     * reinitalisation du formulaire
     * -rechergement de donnees par defaut du conteneur
     */
    private raz;
}
