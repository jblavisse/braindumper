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

        deleteButton.addEventListener('click', function () {
            deleteMemory(dataPath, dataId);
            console.log('Bouton cliqué !');
        });
    });
});




function changeTag(url, id, bodyTitle, bodyDescription) {
    console.log('Début de la fonction changeTag');
    const title = document.querySelector(".title-memory-" + id);
    console.log('title:', title);
    const description = document.querySelector(".description-memory-" + id);
    console.log('description:', description);

    const classTitle = "text-base font-bold text-navy-700 border border-blue-500 border-2 title-memory-" + id;
    const classDescription = "text-base text-navy-700 border border-blue-500 border-2 description-memory-" + id;

    if (title && description) {
        const newInput = `<input type="text" class="${classTitle}" value="${bodyTitle}" placeholder="Titre">`;
        const newTextArea = `<textarea class="${classDescription}" placeholder="Description">${bodyDescription}</textarea>`;


        title.outerHTML = newInput;
        description.outerHTML = newTextArea;


        const updatedTitle = document.querySelector(`.title-memory-` + id);
        const updatedDescription = document.querySelector(`.description-memory-` + id);

        updatedTitle.focus();
        updatedDescription.select();



        // updatedTitle.focus();
        // updatedDescription.setSelectionRange(0, updatedDescription.value.length);

        
        const container = document.querySelector(".memory-container-" + id);

        container.addEventListener('keydown', async function (event) {
            if (event.key === 'Enter') {
                console.log("Entrée cliqué !")

                const newTitle = updatedTitle.value
                const newDescription = updatedDescription.value;

                const datas = [newTitle, newDescription];

                // console.log("nouvelle description : " + { newDescription })
                // updatedDescription.setAttribute("value", newTitle);
                // updatedTitle.setAttribute("value", newDescription);

                const response = await updateAPI(url, datas);


                const allClassTitle = "text-base font-bold text-navy-700 title-memory-" + id + " update-data";
                const allClassDescription = "text-base text-navy-700 description-memory-" + id + " update-data";

                if (response !== 'error') {

                    const titleChanged = `<h3 class="${allClassTitle}">${newTitle}</h3>`;
                    const descriptionChanged = `<div class="${allClassDescription}">${newDescription}</div>`;

                    updatedTitle.outerHTML = titleChanged;
                    updatedDescription.outerHTML = descriptionChanged;

                }
                else {
                    console.error(response);
                }
            }
        })
         
    } else {
        console.error('Aucun élément avec la classe spécifiée trouvé.');
    }
}

function updateAPI(url, datas) {
    const options = {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Cache-Control': 'no-cache',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            title: datas[0],
            description: datas[1],
        }),
    };

    fetch(url, options)
        .then(response => {
            if (!response.ok) {
                throw new Error('La requête a échoué avec le statut : ' + response.status);
            }
            return response.json();
        })
        .then(updatedData => {
            console.table(updatedData);

            // Mise à jour spécifique des éléments du tableau
            updatedData.title= datas[0]
            updatedData.description =datas[1]

            console.table(updatedData);
            return updatedData;
        })
        
        .catch(error => {
            console.error('Erreur lors de la requête fetch :', error);
            return 'error'
        });
}

document.addEventListener('DOMContentLoaded', function () {

    const containersClicks = document.getElementsByClassName('memory-container');

    
    Array.from(containersClicks).forEach(function (containersClick) {
        
        const dataBodyTitle = containersClick.getAttribute('data-body-title');
        const dataBodyDescription = containersClick.getAttribute('data-body-description');
        const dataPath2 = containersClick.getAttribute('data-path');
        const dataId =containersClick.getAttribute('data-id')

        const containerClick = document.getElementsByClassName('memory-container-' + dataId);
        
        Array.from(containerClick).forEach(function (containerIdClick) {
            containerIdClick.addEventListener('click', function () {
                changeTag(dataPath2, dataId, dataBodyTitle, dataBodyDescription);
                
                console.log('Bouton cliqué !');
            });
        })
    });
});


