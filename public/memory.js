function deleteMemory(url, dataId) {
    if (confirm("Voulez-vous vraiment supprimer ce Memory ?")) {

        fetch(url, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({})
        }).then(response => {
            if (response.ok) {
                const memory = document.querySelector('[data-id = "' + dataId + '"]');
                memory.remove();
            } else {
                console.error('Erreur de suppression côté serveur:', response.statusText);
            }
        }).catch(error => {
            console.error('Erreur:', error);
        });
    }
}



function changeTag(url, NameTag, classTag, bodyTag) {

    console.log('Début de la fonction changeTag');
    const tag = document.querySelector(`.${classTag}`);

    if (tag) {

        const initialContent = tag.innerHTML;

        const newTag = `<input type="text" class="${classTag}" value="${bodyTag}">`;

        tag.outerHTML = newTag;

        const updatedTag = document.querySelector(`.${classTag}`);


        updatedTag.focus();


        updatedTag.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {

                const newValue = updatedTag.value
                const response = updateAPI(url, newValue);

                console.log('Nouvelle valeur reçue de updateAPI :', newValue);

                if (response !== 'error') {
                    const tagChanged = `<${NameTag} class="${classTag}">${newValue}</${NameTag}>`;
                    tag.outerHTML = tagChanged;
                } else {
                    const tagUnchanged = `<${NameTag} class="${classTag}">${initialContent}</${NameTag}>`;
                    tag.outerHTML = tagUnchanged;
                    console.error(response);
                }
            }
        });
    } else {
        console.error('Aucun élément avec la classe spécifiée trouvé.');
    }
}



''
function updateAPI(url, newValue) {
    const options = {
        method: 'PUT', // Ou 'PATCH' 
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(newValue),
    };

    fetch(url, options)
        .then(response => {
            if (!response.ok) {
                throw new Error('La requête a échoué avec le statut : ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            data = newValue;
            console.log('Réponse de l\'API :', data);
            return data;
        })
        .catch(error => {
            console.error('Erreur lors de la requête fetch :', error);
            return 'error'
        });
}

