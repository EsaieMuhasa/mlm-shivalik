
//tree orgchart
class MLMTree {
    /**
     * The actual data we goonna try to work with
     */
    data = { Id: "", icon: "", childs: [], name: "" };
    /**
     * The tree we gonna render
     */
    tree = [];
    imgPath = "/";
    constructor(data) {
        if (data)
            this.data = data;
    }
    /**
     * Checks if any data tree given has children or not
     * @param data
     * @returns {boolean} Boolean
     */
    hasChildren(data) {
        return Array.isArray(data.childs) && data.childs.length > 0 ? true : false;
    }
    /**
     * Sets the root of the current treee
     */
    getAndSetRoot() {
        this.tree.push({
            id: this.data.Id,
            name: this.data.name,
            img: this.imgPath+this.data.icon,
        });
    }
    /**
     * Makes a needle tree data type for the library
     * @param data Tree object data
     */
    getAllChildrenFrom(data) {
        if (data.childs) {
            let length = data.childs.length, i = 0;
            for (i; i < length; i++) {
                if (this.hasChildren(data.childs[i])) {
                    this.getAllChildrenFrom(data.childs[i]);
                }
                this.tree.push({
                    pid: data.Id,
                    id: data.childs[i].Id,
                    name: data.childs[i].name,
                    img: this.imgPath+data.childs[i].icon
                });

                console.log(this.imgPath+data.childs[i].icon);
            }
        }
    }
    /**
     * Actually this the method that execute in which order our array will be fill in
     */
    drawTree() {
        this.getAndSetRoot();
        console.log("tree");
        this.getAllChildrenFrom(this.data);
        console.log("tree");
        console.log(this.tree);
    }
}

class MLMTreeContainer {

    /**
     * 
     * @param {Object} data donnees descriptive de l'arbre
     * @param {HTMLElement} container conteneur du dessin de l'arbre
     */
    constructor (data, container) {
        this.container = container;
        this.scale = 0.8;
        const tree = new MLMTree(data);
        this.tree = tree;

        const divZoom = document.createElement('div');
    
        const btnZoomIn = document.createElement('button');
        btnZoomIn.classList.add('btn', 'btn-primary');
        btnZoomIn.innerHTML = '<span class="fa fa-plus"></span>';
    
        const btnZoomOut = document.createElement('button');
        btnZoomOut.classList.add('btn', 'btn-danger');
        btnZoomOut.innerHTML = '<span class="fa fa-minus"></span>';
    
        divZoom.appendChild(btnZoomIn);
        divZoom.appendChild(btnZoomOut);
        divZoom.classList.add('btn-group');
        container.parentNode.insertBefore(divZoom, container);
    
        $(btnZoomIn).on("click", () => {
            this.scale *= 2;
            this.update();
        });
        $(btnZoomOut).on("click", () => {
            this.scale *= 0.5;
            this.update();
        });

        container.style.backgroundColor = '#FFFFFF';

        tree.drawTree();
        this.update();
    }
    
    update () {
        const chart = new OrgChart(this.container, {
            enableSearch: false,
            enableDragDrop: false,
            nodeTreeMenu: false,
            mouseScrool: OrgChart.none,
            scaleInitial : this.scale,
            nodeBinding: {
                field_0: "name",
                img_0: "img",
            },
            nodes: this.tree.tree,
        });
    }

}


var treesContainers = document.querySelectorAll('.tree-render');
if(treesContainers){
    treesContainers.forEach(container => {
        $.ajax({
            method: "GET",
            url: container.getAttribute('data-treeRender'),
            success: (data) => {
                new MLMTreeContainer(data.tree, container);
            }
        });
    });
}

