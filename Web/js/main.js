
const customViews = {

    /**
     * constructeur d'une caroussel
     * @param {HTMLElement} element conteneur des sliders
     * @param {Object} options options de configuration du carousel
     * @param {int} options.slides nombre sliders a faire defiler
     * @param {int} delay la pause aveant l'auto-scroll
     * @param {boolean} options.autoscroll pour activer l'auto-scroll
     * @param {boolean} options.miniature pour activer l'affichage des minuatures
     */
    Carousel : function  (element, options = {}) {}
};


/**
 * definition constructeur Carousel
 * @param {HTMLElement} element conteneur des sliders
 * @param {Object} options options pour modifier les comportements du carousel
 * @param {int} options.slides nombre sliders a faire defiler
 * @param {int} delay la pause aveant l'auto-scroll
 * @param {boolean} options.autoscroll pour activer l'auto-scroll
 * @param {boolean} options.miniature pour activer l'affichage des minuatures
 */
customViews.Carousel =  function (element, options = {}) {
    this.element=element;

    this.options = Object.assign({}, {
        slides : 1,
        delay : 2000,
        autoscroll : false,
        miniature : true
    }, options);

    this.navigationCallBacks = [];

    /**
     * creation d'un div de la clase en parametre
     * @param {string} className un suite des classe
     */
    this.createDivWithClass = (className) => {
        const div = document.createElement('div');
        div.setAttribute('class', className);
        return div;
    }

    /**
     * Construction des elements du carousel || Modification du DOM
     * =====================================================================
     */
    let childrens = [].slice.call(element.children);
    this.currentSlide = 0;//Le numero du slider actuel

    this.root = this.createDivWithClass('default-carousel');
    this.root.setAttribute('tabindex', '0');

    this.container = this.createDivWithClass('default-carousel-items');//le conteneur des items
    this.root.appendChild(this.container);
    this.element.appendChild(this.root);
    
    //Integration de chacun des enfants dans un .default-carousel
    this.items = childrens.map(item => {
        let contentItem = this.createDivWithClass('default-carousel-item');
        contentItem.appendChild(item);
        this.container.appendChild(contentItem);
        return contentItem;
    });
    
    /**
     * Applique le bonnes dimensions des elements du carousel
     * @returns {void}
     */
    this.setStyle = () => {
        let ration = this.items.length;
        this.container.style.width = ((ration * 100) / this.options.slides)+'%';
        this.items.forEach(item => item.style.width = (100/ration)+'%');
    }

    /**
     * Construction des boutons de navigation
     * @returns {void}
     * @returns {HTMLDivElement} l'element qui conties les boutons de navigation
     */
    this.createNavigation = () => {
        const nav = this.createDivWithClass('default-carousel-navigation');
        const nextNavigation= this.createDivWithClass('default-carousel-next');
        const prevNavigation= this.createDivWithClass('default-carousel-prev');

        //Les icones des boutons de navigation
        const contentNext = document.createElement('span');
        const contentPrev = document.createElement('span');
        contentNext.setAttribute('class', 'fa fa-right-open');
        contentPrev.setAttribute('class', 'fa fa-left-open');
        nextNavigation.appendChild(contentNext);
        prevNavigation.appendChild(contentPrev);

        nav.appendChild(nextNavigation);
        nav.appendChild(prevNavigation);

        nextNavigation.addEventListener('click', this.next.bind(this));
        prevNavigation.addEventListener('click', this.prev.bind(this));

        this.onNavigation( index => {
            if(index === 0){
                prevNavigation.classList.add('default-carousel-btn-hidden');
            }else{
                prevNavigation.classList.remove('default-carousel-btn-hidden');
            }

            if(index >= (this.items.length-1) || (this.items[this.currentSlide] == 'undefinid')){
                nextNavigation.classList.add('default-carousel-btn-hidden');
            }else{
                nextNavigation.classList.remove('default-carousel-btn-hidden');
            }
        });

        return nav;
    }

    /**
     * Reation des boutons minuature de pagination
     * renvoie le conteneur des minuature
     * @returns {HTMLDivElement}
     */
    this.createMiniature = () => {
        const miniatures = this.createDivWithClass('default-carousel-miniatures');
        const btns = [];
        for (let i = 0; i < (this.items.length/this.options.slides); i++) {
            let btn = document.createElement('span');
            btn.setAttribute('class', 'default-carousel-miniature-item');
            //btn.appendChild(document.createTextNode(i+1));
            miniatures.appendChild(btn);
            btns.push(btn);
            btn.addEventListener('click', ev => {
                this.gotoItem(i);
            });
        }
        this.miniatures=btns;
        return miniatures;
    }

    /**
     * methode de cla
     * @param {Function} callBack 
     */
    this.onNavigation = (callBack) => {
        this.navigationCallBacks.push(callBack);
    }

    /**
     * Action du bouton next
     */
    this.next = () => {
        this.gotoItem(this.currentSlide + 1);
    }

    /**
     * Action du bouton prev
     */
    this.prev = () => {
        this.gotoItem(this.currentSlide - 1);
    }

    /**
     * Deplacement du carousel a l'item
     * @param {number} index 
     */
    this.gotoItem = (index) => {
        const max = (this.items.length/this.options.slides);
        if(index >= max || (this.items[this.currentSlide] == 'undefinid' && index > this.currentSlide)){
            return;            
        }

        console.log("Index ",index);

        if(this.options.miniature){
            this.miniatures.forEach(m => {
                m.classList.remove('active');
            });
            if(this.miniatures[index]!='undefinid'){
                this.miniatures[index].classList.add('active');
            }
        }

        let translateX= index * -100/ max;
        this.container.style.transform = 'translate3d('+translateX+'%, 0, 0)';
        this.currentSlide = index;

        this.navigationCallBacks.forEach(callBack => callBack(index));
    }
    //appel des methodes utilitaire des sliders
    this.setStyle();

    if (this.items.length>1) {
        if (this.options.miniature) {
            const navContainer=this.createNavigation();
            const minsContainer= this.createMiniature();
            this.root.appendChild(navContainer);
            this.root.appendChild(minsContainer);
        }
        this.gotoItem(0);
    }

    //Evements du clavier
    this.root.addEventListener('keyup', event => {
        if(event.type === 'ArrowRight' || event.type === 'Right'){
            this.next();
        }else if(event.type === 'ArrowLeft' || event.type === 'Left'){
            this.prev();
        }
    });
}


$(function () {
    const element = document.querySelector('.init-default-carousel');
    if(element) {
        new customViews.Carousel(element, {slides : 3});
        console.log(element);
    }
});