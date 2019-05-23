"use strict"

function onLike(event) {
    event.preventDefault();
    //recuperer l'url de lien
    const URL = this.href;
    const ERROR = document.querySelector('.error');
    const NB_LIKE = this.querySelector('span.js-likes');
    const ICONE = this.querySelector('i');
    fetch(URL)
        .then(response => {
            //console.log(response)
            if (response.ok)
                return response.json();
        }).then(data => {
            NB_LIKE.textContent = data.likes;
            if (ICONE.classList.contains('fas')) {
                ICONE.classList.replace('fas', 'far')
            } else {
                ICONE.classList.replace('far', 'fas')
            }
            //console.log(data)
        })
        .catch(function(error) {
            ERROR.classList.replace('none', 'block');
        })
    return;
};
//cibler les liens avec la class js - like
document.querySelectorAll('a.js-like').forEach(function(link) {
    link.addEventListener('click', onLike);
});
/////////////////////////////////////////////////////////////////////////////////////////
///////////supprimer un article///////////////////////////////////////////////////////
function supprimerArticle(id) {
    if (confirm("Voulez vous vraiment supprimer cet article ?")) {
        let url = `/blog/${id}/supprimer`;
        fetch(url, { method: 'delete' })
            .then(response => {
                if (response.ok)
                    location.reload();
            })
            .catch(error => console.error(error));
    }
}

function supprimerComment(event) {
    event.preventDefault();
    const URL = this.href;
    fetch(URL)
        .then(response => {
            if (response.ok) return response.json()
        })
        .then(data => {
            this.parentNode.classList.add('none')
        })
        .catch(error => console.error(error));
    return;
}
document.querySelectorAll('a.js-comment').forEach(function(link) {
        link.addEventListener('click', supprimerComment)
    })
    // document.forms['commentForm'].reset()