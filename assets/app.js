


import './styles/app.scss';


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
                const memories = document.getElementsByClassName('memory-' + dataId);
                if (memories.length > 0) {
                    const memory = memories[0];
                    console.log(memory);
                    memory.remove();
                } else {
                    console.error('Aucun élément avec la classe memory-' + dataId + ' trouvé.');
                }
            } else {
                console.error('Erreur de suppression côté serveur:', response.statusText);
            }
        }).catch(error => {
            console.error('Erreur:', error);
        });
    }
}

document.addEventListener('DOMContentLoaded', function () {

    const deleteButtons = document.getElementsByClassName('delete-button');

    
    Array.from(deleteButtons).forEach(function (deleteButton) {
        
        const dataPath = deleteButton.getAttribute('data-path');
        const dataId = deleteButton.getAttribute('data-id');

        console.log('Data Path:', dataPath);
        console.log('Data ID :', dataId);

        deleteButton.addEventListener('click', function () {
            deleteMemory(dataPath, dataId);
            console.log('Bouton cliqué !');
        });
    });
});




function changeTag(url, nameTag, classTag, classTag2, bodyTag) {

    console.log('Début de la fonction changeTag');
    const tag = document.querySelector(`${nameTag}.${classTag}`);

    if (tag) {
        const newTag = `<input type="text" class="${classTag}" value="${bodyTag}">`;


        tag.outerHTML = newTag;

        const updatedTag = document.querySelector(`.${classTag}`);


        updatedTag.focus();


        updatedTag.addEventListener('keydown', async function (event) {
            if (event.key === 'Enter') {

                const newValue = updatedTag.value
                const response = await updateAPI(url, newValue);

                console.log('Nouvelle valeur reçue de updateAPI :', newValue);

                if (response !== 'error') {
                    const tagChanged = `<${nameTag} class="${classTag2}">${newValue}</${nameTag}>`;

                    updatedTag.outerHTML = tagChanged;

                    console.log(tagChanged);

                    const backTag = document.querySelector(`.${classTag}`);

                    console.log(backTag);

                    backTag.focus();

                }
                else {
                    console.error(response);
                }
            }
        });
    } else {
        console.error('Aucun élément avec la classe spécifiée trouvé.');
    }
}





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

document.addEventListener('DOMContentLoaded', function () {

    const updateDatas = document.getElementsByClassName('update-data');

    
    Array.from(updateDatas).forEach(function (updateData) {
        
        const dataTag = updateData.getAttribute('data-tag');
        const dataClass = updateData.getAttribute('data-class');
        const dataClass2 = updateData.getAttribute('data-classe');
        const dataBody = updateData.getAttribute('data-body');
        const dataPath2 = updateData.getAttribute('data-path');


        updateData.addEventListener('click', function () {
            changeTag(dataPath2, dataTag, dataClass, dataClass2, dataBody);
            console.log('Bouton cliqué !');
        });
    });
});


