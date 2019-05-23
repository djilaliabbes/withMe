"use strict"
// envoyer un message
function envoiMessage(id) {
    let url = `/${id}`;
    fetch(url).then(response => {
            if (response.ok)
                console.log(response)
            return;
        })
        .catch(error => console.error(error));
}
//accepter une invitation
function accepterInvitation(id) {
    let url = `/home/invitation/${id}/accepter`;
    fetch(url).then(response => {
            if (response.ok)
                return;
        })
        .catch(error => console.error(error));
}
// supprimer une invitation
function removeInvitation(id) {
    let url = `/home/invitation/${id}/remove`;
    fetch(url).then(response => {
            if (response.ok)
                return;
        })
        .catch(error => console.error(error));
}
// supprimer un ami
function removeAmi(id) {
    let url = `/home/ami/${id}/remove`;
    fetch(url).then(response => {
            if (response.ok)
                return;
        })
        .catch(error => console.error(error));
}

////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////  afficher les invitations envoyées et Reçues //////////////////////////////////////////
// function display() {
//     let div = document.querySelector('#invitationEnvoyer');
//     div.className === 'none' ? div.className = 'block' : div.className = 'none';
// }
function display() {
    if (this.classList.contains("invitationEnvoyer")) {
        let div = document.querySelector('#invitationEnvoyer');
        div.className === 'none' ? div.className = 'block' : div.className = 'none';
    } else {
        let div = document.querySelector('#invitationRecu');
        div.className === 'none' ? div.className = 'block' : div.className = 'none';
    }
}
let btnsInvitation = document.querySelectorAll(".invitation");
for (let i = 0; i < btnsInvitation.length; i++) {
    btnsInvitation[i].addEventListener("click", display);
}

////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////   cacher les divs au click //////////////////////////////////////////
function cacher() {
    this.parentNode.className = 'none';
}
// cacher chaque invitationau click 
let divs = document.querySelectorAll(".cacher");
for (let i = 0; i < divs.length; i++) {
    divs[i].addEventListener("click", cacher);
}