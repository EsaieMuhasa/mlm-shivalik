
/**
 * configuration des slides a scroller pour chaque type d'ecran
 */
interface SliderToScrollConfig {
    xs : number,
    sm : number,
    xsLand : number,
    smLand : number,
    md : number,
    lg : number,
    [key : string] : number
}

/**
 * option de configutration d'un carousel
 */
interface CarouselOption {
    slidesToScroll? : number,
    configSlidesToScroll? : SliderToScrollConfig,
    autoscroll? : boolean,
    loop?: boolean,
    delay? : number,
    showMiniatures? : boolean,
    createContainerMiniature? : boolean,
    showNavigations? : boolean
}

/**
 * configuration du style d'un carousel
 */
interface CarouselStyle {
    container : string,//class du conteneur principale
    containerItems : string,//classe du conteneur des items
    containerNavigations : string,//classe du conteneur des boutons next et prev
    containerMiniatures : string//classe du conteneur des boutons miniature
    item : string, //class pour chaque item
    nextBtn : string,//class pour le bouton next
    prevBtn : string,//class pour le bouton prev
    hiddenBtn : string,//pour casher le bouton
    iconNextBtn : string,//class de l'icone pour le bouton next
    iconPrevBtn : string,//class de l'icone pour le bouton prev
    miniature : string,
    miniatureActive : string
}

// interface ObjectConstructor {
//     assign (ob1: Object, ob2 : Object, ob3 : Object) : Object;
// }

/**
 * utilitaire de creation d'un element et initialisation directe de son attribut class
 * @param nodeName nom du noeud HTML
 * @param className valeurs a attribuer a l'attribut class de l'element HTML
 * @returns l'element HTML fraichement cree
 */
function cerateElementByClass <E extends HTMLElement> (nodeName : string , className : string ) : E {
    const element = document.createElement(nodeName) as E;
    element.setAttribute('class', className);
    return element;
}

/**
 * generateur d'evenement en fonction de variation de la largeur context Web du navigateur
 */
class WindowConfig {
    private listeners : Function[] =  [];
    private configNames : string[] = ['lg', 'md', 'sm', 'xs', 'smLand', 'xsLand'];
    private currentConfig : number = 0;

    constructor() {
        window.addEventListener('resize', (event) => {
            this.updateConfig();
            this.listeners.forEach(listener => {
                listener(this.configNames[this.currentConfig]);
            });
        });
        this.updateConfig();
    }
    
    private updateConfig () : void {
        const width = window.innerWidth;
        console.log(width);
        
        // const orientation = window.ScreenOrientation;
    
        if (width <= 768) {
            this.currentConfig = (width > 500)? 5 : 3;
        } else if (width > 768 && width <= 992) {
            this.currentConfig = 2;
        } else if (width > 992 && width <= 1170) {
            this.currentConfig = 1;
        } else {
            this.currentConfig = 0;
        }
    }

    /**
     * ajout d'un ecouteur de chagement de configuration du fenetre du navigateur
     * @param listener ecouteur
     * @returns 
     */
    public addListener (listener : Function ) : void {
        for (let i = 0; i < this.listeners.length; i++) {
            if(listener == this.listeners[i]){
                return;
            }  
        }

        this.listeners.push(listener);
    }

    public getCurrentConfig () : string {
        return this.configNames[this.currentConfig];
    }
}


/**
 * definition classe Carousel
 */
class Carousel <T extends HTMLElement> {
    
    private root : T;
    private options : CarouselOption = {};
    private styles : CarouselStyle;
    
    private container : HTMLDivElement;//contenuer principale du carousel
    private containerItems : HTMLDivElement;//contenuer des items du carousel
    private containerNavigations : HTMLDivElement;//contenuer des boutons next et prev du carousel
    private containerMiniatures : HTMLElement;//contenuer des boutons miniature du carousel
    private currentSlider : number = 0;
    private navigrationCallbacks : Function [] = [];
    private lastAnnimationId = 0;

    private items : HTMLElement[] = [];//collections  ref des items
    private mins : HTMLSpanElement[] = [];//collection ref de mins
    private btnNext : HTMLButtonElement;
    private btnPrev : HTMLButtonElement;
    private windowConfig : WindowConfig;

    /**
     * constructeur Carousel
     * @param {HTMLElement} root element qui contiens la structure de base de sliders
     * @param {CarouselStyle} styles options de configuration du style CSS
     * @param {CarouselOption} options options pour modifier les comportements du carousel
     * @param {WindowConfig}  windowConfig
     * @param {HTMLElement} containerMiniatures le conteneur des minuatures
     */
    constructor (root : T,  styles : CarouselStyle, options : CarouselOption = {}, windowConfig : WindowConfig, containerMiniatures : HTMLElement | null = null) {
        this.root = root;
        this.styles = styles;    
        this.setOptions(options);
        this.options.createContainerMiniature = containerMiniatures === null;
        // console.log(this.options);

        this.windowConfig = windowConfig;     

        this.container = cerateElementByClass('div', styles.container);
        this.containerItems = cerateElementByClass('div', styles.containerItems);//le conteneur des items
        this.containerNavigations = cerateElementByClass('div', styles.containerNavigations);
        this.containerMiniatures = containerMiniatures? containerMiniatures : cerateElementByClass('div', styles.containerMiniatures);
        
        this.btnNext = cerateElementByClass('button', styles.nextBtn);
        this.btnPrev = cerateElementByClass('button', styles.prevBtn);
        this.updateDom();
        this.resizeItems();
        // this.gotoSlide(0);

        this.windowConfig.addListener((value : string) => { this.resizeItems()});
        
        //Evements du clavier
        this.container.setAttribute('tabindex', '1');
        this.container.addEventListener('keyup', event => {
            if(event.key == 'ArrowRight' || event.key == 'Right'){
                this.next();
            }else if(event.key == 'ArrowLeft' || event.key == 'Left'){
                this.prev();
            }
        });

        if (this.options.autoscroll) {
            this.lastAnnimationId = setInterval( () => {
                this.next();
            }, options.delay);
        }
    }
    
    /**
     * modification des options de configurations d'un carousel
     * @param options options de configuration d'un carousel
     */
    private setOptions (options : CarouselOption ) : void{
        this.options = Object.assign({}, {
            slidesToScroll : 1,
            delay : 2000,
            loop : true,
            autoscroll : false,
            showMiniatures : true,
            showNavigations : true,
            createContainerMiniature : true
        }, options);
    }

    /**
     * modification du DOM, pour definir la structutre du carousel
     */
    private updateDom () : void {
        const childrens = [].slice.call(this.root.children);
        
        //items
        this.items = childrens.map(item => {//Integration de chacun des enfants dans un root
            let contentItem = cerateElementByClass('div', this.styles.item);
            contentItem.appendChild(item);
            this.containerItems.appendChild(contentItem);
            return contentItem;
        });
        //\\items

        //next-prev
        if (this.options.showNavigations) {
            this.btnNext.appendChild(cerateElementByClass('span', this.styles.iconNextBtn));//ajout icone next
            this.btnPrev.appendChild(cerateElementByClass('span', this.styles.iconPrevBtn));//ajout icone prev
    
            this.containerNavigations.appendChild(this.btnNext);
            this.containerNavigations.appendChild(this.btnPrev);
    
            this.btnNext.addEventListener('click', this.next.bind(this));
            this.btnPrev.addEventListener('click', this.prev.bind(this));
        }
        //\\next-prev
        
        
        this.addListener( (index : number) => {

            if (this.options.showNavigations) {
                if(index == 0){
                    this.btnPrev.classList.add(this.styles.hiddenBtn);
                }else{
                    this.btnPrev.classList.remove(this.styles.hiddenBtn);
                }
    
                if(index >= (this.mins.length-1)){
                    this.btnNext.classList.add(this.styles.hiddenBtn);
                }else{
                    this.btnNext.classList.remove(this.styles.hiddenBtn);
                }
            }

            if(this.options.showMiniatures){
                this.mins.forEach(m => {
                    m.classList.remove(this.styles.miniatureActive);
                });
                this.mins[index].classList.add(this.styles.miniatureActive);
            }
        })
        
        this.container.appendChild(this.containerItems);
        this.container.appendChild(this.containerNavigations);
        if (this.options.createContainerMiniature) {
            this.container.appendChild(this.containerMiniatures);
        }
        this.root.appendChild(this.container);
    }

    /**
     * mis en jour des minuatures
     */
    private updateMiniatures () : void {
        this.mins.forEach(m => {
            m.remove();
        });
        // this.containerMiniatures.innerHTML = '';
        this.mins = [];

        for (let i = 0; i < (this.items.length / this.getSlidesToScroll()); i++) {
            const min = cerateElementByClass('span', this.styles.miniature);
            this.containerMiniatures.appendChild(min);
            this.mins.push(min);
            
            min.addEventListener('click', ev => {
                this.gotoSlide(i);
            });
        }
    }

    /**
     * renvoie le nombre de slider a scroller
     * @returns {number}
     */
    public getSlidesToScroll () : number {
        if (this.options.slidesToScroll && !this.options.configSlidesToScroll) {
            return this.options.slidesToScroll;
        }
        return (this.options.configSlidesToScroll)? this.options.configSlidesToScroll[this.windowConfig.getCurrentConfig()] : 1;
    }

    /**
     * deplacement du carousel
     * @param index le numero du slider a afficher
     * @returns 
     */
    public gotoSlide  (index : number) : void {
        
        const max = (this.items.length / this.getSlidesToScroll());

        if (index >= max || index < 0 ) {
            if(!this.options.loop){
                return;        
            }

            index = index >= max ? 0 : max-1;
        }
        
        const translateX= (index * -100) / max;
        this.containerItems.style.transform = 'translate3d('+translateX+'%, 0, 0)';
        this.currentSlider = index;
        this.dispatch(index);
    }

    /**
     * ajout d'un ecouteur des chagemens d'etat du carousel
     * @param listener une fonction qui sera appeler en collback
     * @returns 
     */
    public addListener (listener : Function ) : void {
        for (let i = 0; i < this.navigrationCallbacks.length; i++) {
            if (listener == this.navigrationCallbacks[i]) {
                return;
            }
        }
        this.navigrationCallbacks.push(listener);
    }

    /**
     * transmission d'un evenement aux ecouteurs
     * @param eventData
     */
    private dispatch (...eventData : any) : void{
        for (let i = 0; i < this.navigrationCallbacks.length; i++) {
            const listener = this.navigrationCallbacks[i];
            listener(eventData);
        }
    }

    /**
     * Scroll le carousel au slide suivant
     */
    public next () : void {
        this.gotoSlide(this.currentSlider + 1);
        
    }
    
    /**
     * Scroll le carousel au slide prev
     */
    public prev () : void {
        this.gotoSlide(this.currentSlider - 1);
    }

    /**
     * Applique le bonnes dimensions des elements du carousel
     * @returns {void}
     */
    private resizeItems () : void {
        this.updateMiniatures();
        let ration = this.items.length;
        this.containerItems.style.width = ((ration * 100) / this.getSlidesToScroll())+'%';
        this.items.forEach(item => item.style.width = (100/ration)+'%');
        this.gotoSlide(0);
    }
}

$(function () {
    const btnMenu = this.querySelector('.toggle-sm-screen');
    const menu = this.querySelector('.default-nav');
    if (menu && btnMenu) {
        btnMenu.addEventListener('click', event => {
            if (event.target == btnMenu) {
                event.stopPropagation();
            }
            menu.classList.toggle('show-menu');
        });

        document.addEventListener('click', e => {
            if (menu.classList.contains('show-menu')) {
                //$(btnMenu).trigger('click');
            }
        })
    }
});

$(function () {
    const element = <HTMLElement> document.querySelector('.init-default-carousel');
    const windowConfig = new WindowConfig();
    
    const options : CarouselOption= {
        slidesToScroll : 3,
        configSlidesToScroll : {
            lg : 3,
            md : 3,
            sm : 2,
            xs : 1,
            xsLand : 2,
            smLand : 3
        },
        showNavigations : true
    }
    
    const optionsHome : CarouselOption= {
        showNavigations : false,
        autoscroll : true,
        delay : 5000
    }

    const styles :  CarouselStyle = {
        container : "default-carousel",//class du conteneur principale
        containerItems : "default-carousel-items",//classe du conteneur des items
        containerNavigations : "default-carousel-navigation",//classe du conteneur des boutons next et prev
        containerMiniatures : "default-carousel-miniatures",//classe du conteneur des boutons miniature
        item : "default-carousel-item", //class pour chaque item
        nextBtn : "default-carousel-next",//class pour le bouton next
        prevBtn : "default-carousel-prev",//class pour le bouton prev
        hiddenBtn : "hidden",//pour casher le bouton
        iconNextBtn : "fa fa-right-open",//class de l'icone pour le bouton next
        iconPrevBtn : "fa fa-left-open",//class de l'icone pour le bouton prev
        miniature : "default-carousel-miniature-item",
        miniatureActive : "active"
    }

    const stylesHome :  CarouselStyle = {
        container : "home-carousel",//class du conteneur principale
        containerItems : "home-carousel-items",//classe du conteneur des items
        containerNavigations : "home-carousel-navigation",//classe du conteneur des boutons next et prev
        containerMiniatures : "home-carousel-miniatures",//classe du conteneur des boutons miniature
        item : "home-carousel-item", //class pour chaque item
        nextBtn : "home-carousel-next",//class pour le bouton next
        prevBtn : "home-carousel-prev",//class pour le bouton prev
        hiddenBtn : "hidden",//pour casher le bouton
        iconNextBtn : "fa fa-right-open",//class de l'icone pour le bouton next
        iconPrevBtn : "fa fa-left-open",//class de l'icone pour le bouton prev
        miniature : "home-carousel-mins-item",
        miniatureActive : "active"
    }
    
    if(element) {
        new Carousel(element, styles, options, windowConfig);
    }
    
    const home = <HTMLElement> document.querySelector('.banner-carousel');
    const homeMins = <HTMLDivElement> document.querySelector('.home-carousel-mins');
    if(home) {
        new Carousel(home, stylesHome, optionsHome, windowConfig, homeMins);
    }
});