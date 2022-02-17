"use strict";
/**
 * @author Esaie Muhasa
 * @description le 16/02/2021
 */
class SearchMember {
    /**
     * @param {HTMLFormElement} form le formultaire concerner
     * @param {HTMLElement} container le conteneur de resultat de recherche
     */
    constructor(form, container) {
        this.form = form;
        this.container = container;
        this.field = form.querySelector('input');
        this.field.addEventListener('keyup', event => {
            $(this.form).trigger('submit');
        });
        $(this.form).on('submit', (event) => {
            event.preventDefault();
            const indice = this.field.value;
            const action = this.form.action + "search.json";
            if (indice.trim().length >= 3) {
                this.sendRequest(action, this.form.method);
            }
            else {
                this.container.innerHTML = "";
            }
        });
    }
    /**
     * Envoie d'une requette de recherche au serveur
     * @param {string} action l'url a la quel la requette doit etre acheminer
     * @param {string} type le type de requette HTTP a transmetre
     */
    sendRequest(action, type = 'POST') {
        $.ajax({
            url: action,
            type: type,
            data: $(this.form).serialize(),
            success: (data) => {
                this.container.innerHTML = "<h5 class='text-info'>" + data.feedback.result + "</h5>";
                const members = data.members;
                if (members.length != 0) {
                    const list = document.createElement('div');
                    list.setAttribute('class', 'list-group');
                    for (let i = 0; i < members.length; i++) {
                        const member = members[i];
                        const lien = document.createElement('a');
                        const names = document.createElement('h4');
                        const description = document.createElement('p');
                        const img = document.createElement('img');
                        img.src = '../../' + member.photo;
                        img.width = 40;
                        img.style.borderRadius = "50%";
                        img.style.float = 'left';
                        names.appendChild(document.createTextNode(member.name + " " + member.postName + " " + member.lastName));
                        names.classList.add('list-group-item-heading');
                        description.classList.add('list-group-item-text');
                        let desc = " -> Subcribe date " + member.dateAjout;
                        desc += "<br/> -> <span class='text-danger'>Packet: " + member.packet.grade.name + "</span>";
                        desc += "<br/> -> <strong class ='text-info'>Username: " + member.pseudo + "</strong>";
                        desc += "<br/> -> <strong class ='text-primary'>ID: " + member.matricule + "</strong>";
                        description.innerHTML = desc;
                        description.style.paddingLeft = '50px';
                        names.style.paddingLeft = '25px';
                        lien.classList.add('list-group-item', 'col-lg-4', 'col-md-6', 'col-sm-6', 'col-xs-12');
                        lien.href = member.id + '/';
                        lien.appendChild(img);
                        lien.appendChild(names);
                        lien.appendChild(description);
                        list.appendChild(lien);
                    }
                    this.container.appendChild(list);
                }
                else {
                    const info = document.createElement('div');
                    info.classList.add('alert', 'alert-danger');
                    info.innerHTML = "<strong>" + data.feedback.result + "</strong><p>" + data.feedback.message + "</p>";
                    this.container.appendChild(info);
                    this.container.classList.remove('hidden');
                }
            },
            error: (data) => {
                this.container.innerHTML = "<p class='text-danger' >" + data.responseText + "</p>";
            }
        });
    }
    /**
     * reinitalisation du formulaire
     * -rechergement de donnees par defaut du conteneur
     */
    raz() {
        this.container.innerHTML = "";
        this.container.classList.add('hidden');
    }
}
//bar de recherche de la section members
$(() => {
    const form = document.querySelector("#form-search-members");
    const container = document.querySelector('#container-search-members');
    if (form && container) {
        new SearchMember(form, container);
    }
});
