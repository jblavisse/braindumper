import { async } from 'regenerator-runtime';
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
  
  async function transformToEditable(container) {
    const title = container.querySelector('.memory-title').textContent;
    const description = container.querySelector('.memory-description').textContent;
    
    const classTitle = "edit-title block text-sm py-3 px-4 rounded-lg w-full border border-pink-400 outline-none";
    const classDescription = "edit-description block text-sm py-3 px-4 rounded-lg w-full border border-pink-400 outline-none";
    const classButton = "save-button text-white absolute w-1/7 right-6 bg-pink-400 hover:bg-pink-800 focus:ring-4 focus:outline-none focus:ring-pink-300 font-medium rounded-lg text-sm px-4 py-2";
  
      
    container.innerHTML = `
        <input type="text" class="${classTitle}" value="${title}">
        <textarea class="${classDescription}">${description}</textarea>
        <button class="${classButton}">Enregistrer</button>`;
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
        if (!response.ok) {
            if (response.status === 422) {
                const errorData = await response.json();
                console.error('Erreur de validation:', errorData);
            } else {
                throw new Error('Failed to update');
            }
        }
        const classTitle = "memory-title text-base font-bold text-navy-700 ";
        const classDescription = "memory-description text-base text-navy-700 ";
        const classButton = "delete-button text-white bg-pink-400 hover:bg-pink-500 focus:ring-4 focus:outline-none focus:ring-pink-300 font-medium rounded-lg text-sm px-1"
        const dataPath = "/memories/" + dataId;   
        
        container.innerHTML = `
            <h3 class="${classTitle}">${updatedTitle}</h3>
            <div class="${classDescription}">${updatedDescription}</div>
            <div class="flex items-center justify-between absolute right-4 gap-5">
            <div class="delete-button-container">
			    <button class="${classButton}" type="button" data-id=${dataId} data-path="${dataPath}">X</button>
		    </div>
            <button data-id=${dataId} class="buttonmodal text-white bg-pink-400 hover:bg-pink-500 focus:ring-4 focus:outline-none focus:ring-pink-300 font-medium rounded-lg text-sm px-1" type="button">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewbox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
            </svg>
        </button>
        </div>`;
        const deleteButton = container.querySelector('.delete-button');
        deleteButton.addEventListener('click', () => {
            deleteMemory(dataPath, dataId);
        });
        
        const button = container.querySelector('.buttonmodal')
        const modal = document.querySelector('.modal')
        button.addEventListener('click', () => {
            modal.classList.add('scale-100');
        }
        )

        const json = await response.json();
        console.log(json);
    } catch (error) {
        console.error('Erreur:', error);
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    const closeButton = document.querySelector('.close-button');
  
    if (closeButton) {
      closeButton.addEventListener('click', () => {
        console.log('Bouton de fermeture cliqué!');
  
        const alertContainer = closeButton.closest('.alert-container');
  
        if (alertContainer) {
          console.log('Élément d\'alerte trouvé:', alertContainer);
          alertContainer.style.display = 'none';
        }
      });
    }
  });

document.addEventListener('DOMContentLoaded', () => {
    const buttons = document.querySelectorAll('.buttonmodal');
    const closeButtons = document.querySelectorAll('.closebutton');
    const closeButtons2 = document.querySelectorAll('.closebutton2');

    buttons.forEach((button) => {
        button.addEventListener('click', () => {
            const modalId = button.dataset.id;
            const modal = document.querySelector(`.modal-${modalId}`);
            modal.classList.add('scale-100');
        });
    });

    closeButtons2.forEach((closeButton2) => {
        closeButton2.addEventListener('click', () => {
            const modalId = closeButton2.dataset.id;
            const modal = document.querySelector(`.modal-${modalId}`);
            modal.classList.remove('scale-100');
        });
    });

    closeButtons.forEach((closeButton) => {
        closeButton.addEventListener('click', () => {
            const modalId = closeButton.dataset.id;
            const modal = document.querySelector(`.modal-${modalId}`);
            modal.classList.remove('scale-100');
        });
    });
})

let isDropdownOpenType = false; 

document.addEventListener('DOMContentLoaded', () => {
    const dropdownButtons = document.querySelectorAll('.dropdown-button-type');
    const dropdownMenus = document.querySelectorAll('.dropdown-menu-type');

    function toggleDropdownType(menu) {
        const isOpen = !menu.classList.contains('hidden');
    
    dropdownMenus.forEach((dropdownMenu) => {
        if (dropdownMenu !== menu) {
            dropdownMenu.classList.add('hidden');
        }
    });

    if (!isOpen) {
        menu.classList.remove('hidden');
        isDropdownOpenType = true;
    } else {
        isDropdownOpenType = false;
    }
}
    dropdownButtons.forEach((dropdownButton) => {
        const menu = dropdownButton.nextElementSibling; 
        dropdownButton.addEventListener('click', () => {
            console.log('Bouton cliqué')
            toggleDropdownType(menu);
        });

        document.addEventListener('click', (event) => {
            if (!dropdownButton.contains(event.target) && !menu.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });
    });

    const dropdownItemsType = document.querySelectorAll('[name="selected_type"]');

    dropdownItemsType.forEach(item => {
        item.addEventListener('click', handleDropdownItemClickType);
    });
});


async function handleDropdownItemClickType(event) {

    console.log('On rentre dans la fonction')
    const selectedType = event.target.innerText;

    const selectedTypeId = event.target.getAttribute('data-id');
    const url = event.target.getAttribute('data-url')
    console.log(url);


    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: selectedTypeId, name: selectedType })
        });
        if (!response.ok) {
            if (response.status === 422) {
                const errorData = await response.json();
                console.error('Erreur de validation:', errorData);
            } else {
                throw new Error('Failed to update');
            }
        }

        
        const id = event.target.getAttribute('data-idM');
        console.log(id)

        const dropdownButtonType = document.querySelector(`.dropdown-button-type-${id}`);
        const dropdownMenusType = document.querySelectorAll('.dropdown-menu-type');
        
        dropdownButtonType.innerText = `Type: ${selectedType}`;
        
        dropdownMenusType.forEach((dropdownMenuType) => {
            dropdownMenuType.classList.add('hidden');
        })
                
        isDropdownOpenType = false;

        const json = await response.json();
        console.log(json);
    } catch (error) {
        console.error('Erreur:', error);
    }
  }


let isDropdownOpenCategory = false; 
document.addEventListener('DOMContentLoaded', () => {
    const dropdownButtonsCategory = document.querySelectorAll('.dropdown-button-category');
    const dropdownMenusCategory = document.querySelectorAll('.dropdown-menu-category');

    function toggleDropdownCategory(menu) {
        const isOpen = !menu.classList.contains('hidden');
    
    dropdownMenusCategory.forEach((dropdownMenuCategory) => {
        if (dropdownMenuCategory !== menu) {
            dropdownMenuCategory.classList.add('hidden');
        }
    });

    if (!isOpen) {
        menu.classList.remove('hidden');
        isDropdownOpenCategory = true;
    } else {
        isDropdownOpenCategory = false;
    }
}
    dropdownButtonsCategory.forEach((dropdownButtonCategory) => {
        const menu = dropdownButtonCategory.nextElementSibling; 
        dropdownButtonCategory.addEventListener('click', () => {
            console.log('Bouton cliqué')
            toggleDropdownCategory(menu);
        });

        document.addEventListener('click', (event) => {
            if (!dropdownButtonCategory.contains(event.target) && !menu.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });
    });

    const dropdownItemsCategory = document.querySelectorAll('[name="selected_category"]');

    dropdownItemsCategory.forEach(item => {
        item.addEventListener('click', handleDropdownItemClickCategory);
    });
});

async function handleDropdownItemClickCategory(event) {

    console.log('On rentre dans la fonction')
    const selectedCategory = event.target.innerText;

    const selectedCategoryId = event.target.getAttribute('data-id');
    const url = event.target.getAttribute('data-url')
    console.log(url);


    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: selectedCategoryId, name: selectedCategory })
        });
        if (!response.ok) {
            if (response.status === 422) {
                const errorData = await response.json();
                console.error('Erreur de validation:', errorData);
            } else {
                throw new Error('Failed to update');
            }
        }
        const id = event.target.getAttribute('data-idM');
        console.log(id)

        const dropdownButtonCategory = document.querySelector(`.dropdown-button-category-${id}`);
        const dropdownMenusCategory = document.querySelectorAll('.dropdown-menu-category');
        
        dropdownButtonCategory.innerText = `Catégorie: ${selectedCategory}`;
        
        dropdownMenusCategory.forEach((dropdownMenuCategory) => {
            dropdownMenuCategory.classList.add('hidden');
        })
                isDropdownOpenCategory = false;

        const json = await response.json();
        console.log(json);
    } catch (error) {
        console.error('Erreur:', error);
    }
  }
