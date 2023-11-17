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


document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('click', event => {
      const memoryContainer = event.target.closest('.memory-container');
      if (!memoryContainer) return;
  
        const url = memoryContainer.dataset.url;
        const dataId = memoryContainer.dataset.id;
      
      if (event.target.matches('.memory-title, .memory-description')) {
        transformToEditable(memoryContainer);
      } else if (event.target.matches('.save-button')) {
        saveChanges(memoryContainer, url, dataId);
      }
    });
  });
  
  function transformToEditable(container) {
    const title = container.querySelector('.memory-title').textContent;
    const description = container.querySelector('.memory-description').textContent;
    
    const classTitle = "edit-title text-base font-bold text-navy-700 border border-pink-400 py-1 px-1";
    const classDescription = "edit-description text-base text-navy-700 border border-pink-400 p-3";
    const classButton = "save-button text-white absolute w-1/7 right-6 bg-pink-400 hover:bg-pink-800 focus:ring-4 focus:outline-none focus:ring-pink-300 font-medium rounded-lg text-sm px-4 py-2";
      
    container.innerHTML = `
        <input type="text" class="${classTitle}" value="${title}">
        <textarea class="${classDescription}">${description}</textarea>
        <button class="${classButton}">Save</button>`;
  }
  
  async function saveChanges(container, url, dataId) {
    const updatedTitle = container.querySelector('.edit-title').value;
    const updatedDescription = container.querySelector('.edit-description').value;
  

    try {
        const response = await fetch(url, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ title: updatedTitle, description: updatedDescription })
        });
        if (!response.ok) throw new Error('Failed to update');
        const classTitle = "memory-title text-base font-bold text-navy-700 ";
        const classDescription = "memory-description text-base text-navy-700 ";
        const classButton = "delete-button text-white absolute right-2.5 bg-pink-400 hover:bg-pink-500 focus:ring-4 focus:outline-none focus:ring-pink-300 font-medium rounded-lg text-sm px-1"
        const dataPath = "/memories/" + dataId;   
        
        container.innerHTML = `
            <h3 class="${classTitle}">${updatedTitle}</h3>
            <div class="${classDescription}">${updatedDescription}</div>
            <div class="delete-button-container">
			    <button class="${classButton}" type="button" data-id=${dataId} data-path="${dataPath}">X</button>
		    </div>`;
        const deleteButton = container.querySelector('.delete-button');
        deleteButton.addEventListener('click', () => {
            deleteMemory(dataPath, dataId);
            console.log('Bouton cliqué !');
        });
    
        const json = await response.json();
        console.log(json);
    } catch (error) {
        console.error('Erreur:', error);
    }
  }
