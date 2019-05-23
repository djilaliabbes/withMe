"use strict"
// inviter User
function envoiInvitation(id) {
    let url = `/allUsers/recherche/${id}/inviter`;
    fetch(url).then(response => {
            if (response.ok)
                console.log(response)
        })
        .catch(error => console.error(error));
}
// ///////////////// cacher des éléments
// function cacher() {
//     this.parentNode.className = 'none';
// }
// // cacher chaque user click 
// let divUsers = document.querySelectorAll(".cacher");
// for (let i = 0; i < divUsers.length; i++) {
//     divUsers[i].addEventListener("click", cacher);
// }