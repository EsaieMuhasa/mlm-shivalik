/**
 * configuration des slides a scroller pour chaque type d'ecran
 */
interface SliderToScrollConfig {
    xs: number;
    sm: number;
    xsLand: number;
    smLand: number;
    md: number;
    lg: number;
    [key: string]: number;
}
/**
 * option de configutration d'un carousel
 */
interface CarouselOption {
    slidesToScroll?: number;
    configSlidesToScroll?: SliderToScrollConfig;
    autoscroll?: boolean;
    loop?: boolean;
    delay?: number;
    showMiniatures?: boolean;
    createContainerMiniature?: boolean;
    showNavigations?: boolean;
}
/**
 * configuration du style d'un carousel
 */
interface CarouselStyle {
    container: string;
    containerItems: string;
    containerNavigations: string;
    containerMiniatures: string;
    item: string;
    nextBtn: string;
    prevBtn: string;
    hiddenBtn: string;
    iconNextBtn: string;
    iconPrevBtn: string;
    miniature: string;
    miniatureActive: string;
}
/**
 * utilitaire de creation d'un element et initialisation directe de son attribut class
 * @param nodeName nom du noeud HTML
 * @param className valeurs a attribuer a l'attribut class de l'element HTML
 * @returns l'element HTML fraichement cree
 */
declare function cerateElementByClass<E extends HTMLElement>(nodeName: string, className: string): E;
/**
 * generateur d'evenement en fonction de variation de la largeur context Web du navigateur
 */
declare class WindowConfig {
    private listeners;
    private configNames;
    private currentConfig;
    constructor();
    private updateConfig;
    /**
     * ajout d'un ecouteur de chagement de configuration du fenetre du navigateur
     * @param listener ecouteur
     * @returns
     */
    addListener(listener: Function): void;
    getCurrentConfig(): string;
}
/**
 * definition classe Carousel
 */
declare class Carousel<T extends HTMLElement> {
    private root;
    private options;
    private styles;
    private container;
    private containerItems;
    private containerNavigations;
    private containerMiniatures;
    private currentSlider;
    private navigrationCallbacks;
    private lastAnnimationId;
    private items;
    private mins;
    private btnNext;
    private btnPrev;
    private windowConfig;
    /**
     * constructeur Carousel
     * @param {HTMLElement} root element qui contiens la structure de base de sliders
     * @param {CarouselStyle} styles options de configuration du style CSS
     * @param {CarouselOption} options options pour modifier les comportements du carousel
     * @param {WindowConfig}  windowConfig
     * @param {HTMLElement} containerMiniatures le conteneur des minuatures
     */
    constructor(root: T, styles: CarouselStyle, options: CarouselOption | undefined, windowConfig: WindowConfig, containerMiniatures?: HTMLElement | null);
    /**
     * modification des options de configurations d'un carousel
     * @param options options de configuration d'un carousel
     */
    private setOptions;
    /**
     * modification du DOM, pour definir la structutre du carousel
     */
    private updateDom;
    /**
     * mis en jour des minuatures
     */
    private updateMiniatures;
    /**
     * renvoie le nombre de slider a scroller
     * @returns {number}
     */
    getSlidesToScroll(): number;
    /**
     * deplacement du carousel
     * @param index le numero du slider a afficher
     * @returns
     */
    gotoSlide(index: number): void;
    /**
     * ajout d'un ecouteur des chagemens d'etat du carousel
     * @param listener une fonction qui sera appeler en collback
     * @returns
     */
    addListener(listener: Function): void;
    /**
     * transmission d'un evenement aux ecouteurs
     * @param eventData
     */
    private dispatch;
    /**
     * Scroll le carousel au slide suivant
     */
    next(): void;
    /**
     * Scroll le carousel au slide prev
     */
    prev(): void;
    /**
     * Applique le bonnes dimensions des elements du carousel
     * @returns {void}
     */
    private resizeItems;
}
