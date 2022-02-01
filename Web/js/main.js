"use strict";
// interface ObjectConstructor {
//     assign (ob1: Object, ob2 : Object, ob3 : Object) : Object;
// }
/**
 * utilitaire de creation d'un element et initialisation directe de son attribut class
 * @param nodeName nom du noeud HTML
 * @param className valeurs a attribuer a l'attribut class de l'element HTML
 * @returns l'element HTML fraichement cree
 */
function cerateElementByClass(nodeName, className) {
    const element = document.createElement(nodeName);
    element.setAttribute('class', className);
    return element;
}
class WindowConfig {
    constructor() {
        this.listeners = [];
        this.configNames = ['lg', 'md', 'sm', 'xs', 'smLand', 'xsLand'];
        this.currentConfig = 0;
        window.addEventListener('resize', (event) => {
            this.updateConfig();
            this.listeners.forEach(listener => {
                listener(this.configNames[this.currentConfig]);
            });
        });
        this.updateConfig();
    }
    updateConfig() {
        const width = window.innerWidth;
        console.log(width);
        // const orientation = window.ScreenOrientation;
        if (width <= 768) {
            this.currentConfig = (width > 500) ? 5 : 3;
        }
        else if (width > 768 && width <= 992) {
            this.currentConfig = 2;
        }
        else if (width > 992 && width <= 1170) {
            this.currentConfig = 1;
        }
        else {
            this.currentConfig = 0;
        }
    }
    /**
     * ajout d'un ecouteur de chagement de configuration du fenetre du navigateur
     * @param listener ecouteur
     * @returns
     */
    addListener(listener) {
        for (let i = 0; i < this.listeners.length; i++) {
            if (listener == this.listeners[i]) {
                return;
            }
        }
        this.listeners.push(listener);
    }
    getCurrentConfig() {
        return this.configNames[this.currentConfig];
    }
}
/**
 * definition classe Carousel
 */
class Carousel {
    /**
     * constructeur Carousel
     * @param {HTMLElement} root element qui contiens la structure de base de sliders
     * @param {CarouselStyle} styles options de configuration du style CSS
     * @param {CarouselOption} options options pour modifier les comportements du carousel
     * @param {WindowConfig}  windowConfig
     * @param {HTMLElement} containerMiniatures le conteneur des minuatures
     */
    constructor(root, styles, options = {}, windowConfig, containerMiniatures = null) {
        this.options = {};
        this.currentSlider = 0;
        this.navigrationCallbacks = [];
        this.lastAnnimationId = 0;
        this.items = []; //collections  ref des items
        this.mins = []; //collection ref de mins
        this.root = root;
        this.styles = styles;
        this.setOptions(options);
        this.options.createContainerMiniature = containerMiniatures === null;
        // console.log(this.options);
        this.windowConfig = windowConfig;
        this.container = cerateElementByClass('div', styles.container);
        this.containerItems = cerateElementByClass('div', styles.containerItems); //le conteneur des items
        this.containerNavigations = cerateElementByClass('div', styles.containerNavigations);
        this.containerMiniatures = containerMiniatures ? containerMiniatures : cerateElementByClass('div', styles.containerMiniatures);
        this.btnNext = cerateElementByClass('button', this.styles.nextBtn);
        this.btnPrev = cerateElementByClass('button', this.styles.prevBtn);
        this.updateDom();
        this.resizeItems();
        this.gotoSlide(0);
        this.windowConfig.addListener((value) => { this.resizeItems(); });
        //Evements du clavier
        this.container.setAttribute('tabindex', '1');
        this.container.addEventListener('keyup', event => {
            if (event.key == 'ArrowRight' || event.key == 'Right') {
                this.next();
            }
            else if (event.key == 'ArrowLeft' || event.key == 'Left') {
                this.prev();
            }
        });
        if (this.options.autoscroll) {
            this.lastAnnimationId = setInterval(() => {
                this.next();
            }, options.delay);
        }
    }
    /**
     * modification des options de configurations d'un carousel
     * @param options options de configuration d'un carousel
     */
    setOptions(options) {
        this.options = Object.assign({}, {
            slidesToScroll: 1,
            delay: 2000,
            loop: true,
            autoscroll: false,
            showMiniatures: true,
            showNavigations: true,
            createContainerMiniature: true
        }, options);
    }
    /**
     * modification du DOM, pour definir la structutre du carousel
     */
    updateDom() {
        const childrens = [].slice.call(this.root.children);
        //items
        this.items = childrens.map(item => {
            let contentItem = cerateElementByClass('div', this.styles.item);
            contentItem.appendChild(item);
            this.containerItems.appendChild(contentItem);
            return contentItem;
        });
        //\\items
        //next-prev
        if (this.options.showNavigations) {
            this.btnNext.appendChild(cerateElementByClass('span', this.styles.iconNextBtn)); //ajout icone next
            this.btnPrev.appendChild(cerateElementByClass('span', this.styles.iconPrevBtn)); //ajout icone prev
            this.containerNavigations.appendChild(this.btnNext);
            this.containerNavigations.appendChild(this.btnPrev);
            this.btnNext.addEventListener('click', this.next.bind(this));
            this.btnPrev.addEventListener('click', this.prev.bind(this));
        }
        //\\next-prev
        this.addListener((index) => {
            if (this.options.showNavigations) {
                if (index == 0) {
                    this.btnPrev.classList.add(this.styles.hiddenBtn);
                }
                else {
                    this.btnPrev.classList.remove(this.styles.hiddenBtn);
                }
                if (index >= (this.mins.length - 1)) {
                    this.btnNext.classList.add(this.styles.hiddenBtn);
                }
                else {
                    this.btnNext.classList.remove(this.styles.hiddenBtn);
                }
            }
            if (this.options.showMiniatures) {
                this.mins.forEach(m => {
                    m.classList.remove(this.styles.miniatureActive);
                });
                this.mins[index].classList.add(this.styles.miniatureActive);
            }
        });
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
    updateMiniatures() {
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
    getSlidesToScroll() {
        if (this.options.slidesToScroll && !this.options.configSlidesToScroll) {
            return this.options.slidesToScroll;
        }
        if (this.options.configSlidesToScroll) {
            const key = this.windowConfig.getCurrentConfig();
            return this.options.configSlidesToScroll[key];
        }
        return 1;
    }
    /**
     * deplacement du carousel
     * @param index le numero du slider a afficher
     * @returns
     */
    gotoSlide(index) {
        const max = (this.items.length / this.getSlidesToScroll());
        if (index >= max || index < 0) {
            if (!this.options.loop) {
                return;
            }
            index = index >= max ? 0 : max - 1;
        }
        const translateX = (index * -100) / max;
        this.containerItems.style.transform = 'translate3d(' + translateX + '%, 0, 0)';
        this.currentSlider = index;
        this.dispatch(index);
    }
    /**
     * ajout d'un ecouteur des chagemens d'etat du carousel
     * @param listener une fonction qui sera appeler en collback
     * @returns
     */
    addListener(listener) {
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
    dispatch(...eventData) {
        for (let i = 0; i < this.navigrationCallbacks.length; i++) {
            const listener = this.navigrationCallbacks[i];
            listener(eventData);
        }
    }
    /**
     * Scroll le carousel au slide suivant
     */
    next() {
        this.gotoSlide(this.currentSlider + 1);
    }
    /**
     * Scroll le carousel au slide prev
     */
    prev() {
        this.gotoSlide(this.currentSlider - 1);
    }
    /**
     * Applique le bonnes dimensions des elements du carousel
     * @returns {void}
     */
    resizeItems() {
        this.updateMiniatures();
        let ration = this.items.length;
        this.containerItems.style.width = ((ration * 100) / this.getSlidesToScroll()) + '%';
        this.items.forEach(item => item.style.width = (100 / ration) + '%');
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
        });
    }
});
$(function () {
    const element = document.querySelector('.init-default-carousel');
    const windowConfig = new WindowConfig();
    const options = {
        slidesToScroll: 3,
        configSlidesToScroll: {
            lg: 3,
            md: 3,
            sm: 2,
            xs: 1,
            xsLand: 2,
            smLand: 3
        },
        showNavigations: true
    };
    const optionsHome = {
        showNavigations: false,
        autoscroll: true,
        delay: 5000
    };
    const styles = {
        container: "default-carousel",
        containerItems: "default-carousel-items",
        containerNavigations: "default-carousel-navigation",
        containerMiniatures: "default-carousel-miniatures",
        item: "default-carousel-item",
        nextBtn: "default-carousel-next",
        prevBtn: "default-carousel-prev",
        hiddenBtn: "hidden",
        iconNextBtn: "fa fa-right-open",
        iconPrevBtn: "fa fa-left-open",
        miniature: "default-carousel-miniature-item",
        miniatureActive: "active"
    };
    const stylesHome = {
        container: "home-carousel",
        containerItems: "home-carousel-items",
        containerNavigations: "home-carousel-navigation",
        containerMiniatures: "home-carousel-miniatures",
        item: "home-carousel-item",
        nextBtn: "home-carousel-next",
        prevBtn: "home-carousel-prev",
        hiddenBtn: "hidden",
        iconNextBtn: "fa fa-right-open",
        iconPrevBtn: "fa fa-left-open",
        miniature: "home-carousel-mins-item",
        miniatureActive: "active"
    };
    if (element) {
        new Carousel(element, styles, options, windowConfig);
        console.log(element);
    }
    const home = document.querySelector('.banner-carousel');
    const homeMins = document.querySelector('.home-carousel-mins');
    if (home) {
        new Carousel(home, stylesHome, optionsHome, windowConfig, homeMins);
        console.log(home);
    }
});
