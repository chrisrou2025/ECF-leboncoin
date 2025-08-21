// === VARIABLES GLOBALES ===
let allAnnonces = null;
let currentFilters = {
    category: '',
    sort: 'random'
};

// Variables pour l'édition d'images
let imagesToDelete = [];
let selectedFilesForEdit = {};

// === CONFIGURATION ET CONSTANTES ===
const CONFIG = {
    maxFiles: 4,
    allowedTypes: ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
    maxSize: 5 * 1024 * 1024, // 5MB
    vehiculeCategoryId: '4'
};

// === FONCTIONS UTILITAIRES ===
function displaySessionMessage(message, type) {
    const container = document.getElementById('toast-container') || createToastContainer();
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <div class="toast-content">
            <span class="toast-icon">${type === 'success' ? '✓' : '✗'}</span>
            <span class="toast-message">${message}</span>
            <button class="toast-close" onclick="this.parentElement.parentElement.remove()">×</button>
        </div>
    `;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 5000);
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    document.body.appendChild(container);
    return container;
}

// === FONCTIONS DE BASE ===
function confirmLogout() {
    if (confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) {
        window.location.href = '/ECF-leboncoin/logout';
    }
}

function confirmDelete(annonceId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette annonce ? Cette action est irréversible.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/ECF-leboncoin/annonce/${annonceId}/delete`;
        document.body.appendChild(form);
        form.submit();
    }
}

// === GESTION DE LA RECHERCHE ===
function initializeSearch() {
    const searchInput = document.querySelector('.search-input');
    const searchButton = document.querySelector('.search-button');
    if (!searchInput || !searchButton) return;

    searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            performSearch();
        }
    });
    searchButton.addEventListener('click', performSearch);
}

function performSearch() {
    const searchInput = document.querySelector('.search-input');
    if (!searchInput) return;
    const searchTerm = searchInput.value.trim();
    if (searchTerm) {
        window.location.href = `/ECF-leboncoin/recherche?q=${encodeURIComponent(searchTerm)}`;
    }
}

// === GESTION DES MOTS DE PASSE ===
function initializePasswordToggle() {
    document.querySelectorAll('.toggle-password').forEach(button => {
        const passwordInput = button.parentElement.querySelector('input[type="password"], input[type="text"]');
        if (!passwordInput) return;

        button.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';

            const icon = button.querySelector('i');
            if (icon) {
                icon.className = `fas ${isPassword ? 'fa-eye-slash' : 'fa-eye'}`;
            }
        });
    });
}

// === GESTION DU FILTRAGE ===
function collectAllAnnonces() {
    if (allAnnonces) return;

    allAnnonces = Array.from(document.querySelectorAll('.annonce-card')).map(card => ({
        element: card,
        categoryId: card.dataset.categoryId,
        categoryName: card.dataset.categoryName,
        price: parseFloat(card.dataset.price) || 0,
        date: new Date(card.dataset.date),
        title: (card.dataset.title || '').toLowerCase()
    }));
}

function setupFilterEvents() {
    const categoryFilter = document.getElementById('category-filter');
    const sortFilter = document.getElementById('sort-filter');

    if (categoryFilter) {
        categoryFilter.addEventListener('change', () => {
            currentFilters.category = categoryFilter.value;
            applyFilters();
        });
    }

    if (sortFilter) {
        sortFilter.addEventListener('change', () => {
            currentFilters.sort = sortFilter.value;
            applyFilters();
        });
    }
}

function applyFilters() {
    let filteredAnnonces = allAnnonces.filter(annonce =>
        currentFilters.category === '' || annonce.categoryId === currentFilters.category
    );

    filteredAnnonces = sortAnnonces(filteredAnnonces, currentFilters.sort);
    updateDisplay(filteredAnnonces);
}

function sortAnnonces(annonces, sortType) {
    const sorted = [...annonces];

    const sortFunctions = {
        'random': () => {
            for (let i = sorted.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [sorted[i], sorted[j]] = [sorted[j], sorted[i]];
            }
        },
        'date-desc': () => sorted.sort((a, b) => b.date - a.date),
        'date-asc': () => sorted.sort((a, b) => a.date - b.date),
        'price-asc': () => sorted.sort((a, b) => a.price - b.price),
        'price-desc': () => sorted.sort((a, b) => b.price - a.price),
        'title-asc': () => sorted.sort((a, b) => a.title.localeCompare(b.title)),
        'title-desc': () => sorted.sort((a, b) => b.title.localeCompare(a.title))
    };

    const sortFunction = sortFunctions[sortType];
    if (sortFunction) sortFunction();

    return sorted;
}

function updateDisplay(filteredAnnonces) {
    const container = document.getElementById('annonces-container');
    const noResultsMessage = document.getElementById('no-results-message');
    if (!container || !noResultsMessage) return;

    container.innerHTML = '';

    if (filteredAnnonces.length > 0) {
        filteredAnnonces.forEach(annonce => {
            container.appendChild(annonce.element.cloneNode(true));
        });
        noResultsMessage.style.display = 'none';
        container.style.display = 'flex';
    } else {
        container.style.display = 'none';
        noResultsMessage.style.display = 'block';
    }
}

// Gestion du menu burger
function initializeBurgerMenu() {
    const burgerBtn = document.getElementById('burger-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuOverlay = document.getElementById('menu-overlay');

    if (!burgerBtn || !mobileMenu || !menuOverlay) return;

    burgerBtn.addEventListener('click', toggleMobileMenu);
    menuOverlay.addEventListener('click', closeMobileMenu);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && mobileMenu.classList.contains('active')) {
            closeMobileMenu();
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 768 && mobileMenu.classList.contains('active')) {
            closeMobileMenu();
        }
    });
}

function toggleMobileMenu() {
    const burgerBtn = document.getElementById('burger-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuOverlay = document.getElementById('menu-overlay');

    const isActive = mobileMenu.classList.contains('active');

    if (isActive) {
        closeMobileMenu();
    } else {
        openMobileMenu();
    }
}

function openMobileMenu() {
    const burgerBtn = document.getElementById('burger-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuOverlay = document.getElementById('menu-overlay');

    burgerBtn.classList.add('active');
    mobileMenu.classList.add('active');
    menuOverlay.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeMobileMenu() {
    const burgerBtn = document.getElementById('burger-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuOverlay = document.getElementById('menu-overlay');

    if (!burgerBtn || !mobileMenu || !menuOverlay) return;

    burgerBtn.classList.remove('active');
    mobileMenu.classList.remove('active');
    menuOverlay.classList.remove('active');
    document.body.style.overflow = '';
}

// === NAVIGATION DES CATÉGORIES ===
function filterByCategory(categoryId) {
    const isHomePage = ['/', '/index.php', ''].some(path =>
        window.location.pathname.endsWith('/ECF-leboncoin' + path)
    );

    if (isHomePage) {
        // Filtrage local sur la page d'accueil
        const categoryFilter = document.getElementById('category-filter');
        if (categoryFilter) {
            categoryFilter.value = categoryId;
            currentFilters.category = categoryId;
            applyFilters();

            // Défilement vers la section des annonces
            const latestAnnoncesSection = document.querySelector('.latest-annonces');
            if (latestAnnoncesSection) {
                latestAnnoncesSection.scrollIntoView({ behavior: 'smooth' });
            }
        }
    } else {
        // Redirection vers l'accueil avec le filtre
        const params = new URLSearchParams();
        params.set('category', categoryId);
        window.location.href = `/ECF-leboncoin?${params.toString()}`;
    }
}

function initializeCategoryNavigation() {
    const urlParams = new URLSearchParams(window.location.search);
    const categoryParam = urlParams.get('category');

    if (categoryParam) {
        setTimeout(() => {
            const categoryFilter = document.getElementById('category-filter');
            if (categoryFilter) {
                categoryFilter.value = categoryParam;
                currentFilters.category = categoryParam;
                applyFilters();

                const latestAnnoncesSection = document.querySelector('.latest-annonces');
                if (latestAnnoncesSection) {
                    latestAnnoncesSection.scrollIntoView({ behavior: 'smooth' });
                }

                // Nettoyer l'URL
                const newUrl = window.location.pathname;
                window.history.replaceState({}, '', newUrl);
            }
        }, 100);
    }
}

function initializeFiltering() {
    if (!document.querySelector('.annonces-random-container')) return;
    collectAllAnnonces();
    setupFilterEvents();
    applyFilters();
}

// === GESTION DES CHAMPS CONDITIONNELS ===

function initializeConditionalFields() {
    const categorySelect = document.getElementById('category_id');
    const kilometrageRow = document.getElementById('kilometrage-row') ||
        document.getElementById('vehicule-fields');
    const kilometrageInput = document.getElementById('kilometrage');

    // Gestion du champ marque
    const marqueRow = document.getElementById('marque-row');
    const marqueInput = document.getElementById('marque');

    if (!categorySelect) return;

    const toggleConditionalFields = () => {
        const selectedCategoryId = categorySelect.value;

        // Gestion du kilométrage (pour les véhicules - ID 4)
        if (kilometrageRow && kilometrageInput) {
            const isVehicule = selectedCategoryId === CONFIG.vehiculeCategoryId;
            kilometrageRow.style.display = isVehicule ? 'flex' : 'none';
            kilometrageInput.required = isVehicule;

            // Vider le kilométrage si ce n'est pas un véhicule
            if (!isVehicule) {
                kilometrageInput.value = '';
            }
        }

        // Gestion de la marque (cachée pour "Maison et jardin" - ID 2)
        if (marqueRow && marqueInput) {
            const isMaisonJardin = selectedCategoryId === '2';
            marqueRow.style.display = isMaisonJardin ? 'none' : 'flex';

            // Vider le champ marque si "Maison et jardin" est sélectionné
            if (isMaisonJardin) {
                marqueInput.value = '';
            }
        }

        // Debug pour vérifier les valeurs
        console.log('Catégorie sélectionnée:', selectedCategoryId);
        console.log('Est véhicule (ID 4):', selectedCategoryId === CONFIG.vehiculeCategoryId);
        console.log('Est Maison et jardin (ID 2):', selectedCategoryId === '2');
    };

    // Écouter les changements de catégorie
    categorySelect.addEventListener('change', toggleConditionalFields);

    // Appel initial pour configurer l'état au chargement
    toggleConditionalFields();
}

// === GESTION DES PHOTOS - CRÉATION ===
function initializePhotoUpload() {
    const photoUploadTrigger = document.getElementById('photo-upload-trigger');
    const photoInput = document.getElementById('photo-input');
    const thumbnailsContainer = document.querySelector('.photo-thumbnails');

    if (!photoUploadTrigger || !photoInput || !thumbnailsContainer) return;

    let selectedFiles = [];

    photoUploadTrigger.addEventListener('click', () => photoInput.click());

    photoInput.addEventListener('change', (e) => {
        Array.from(e.target.files).forEach(file => {
            if (selectedFiles.length >= CONFIG.maxFiles) {
                displaySessionMessage(`Vous ne pouvez sélectionner que ${CONFIG.maxFiles} images maximum.`, 'error');
                return;
            }

            if (!CONFIG.allowedTypes.includes(file.type)) {
                displaySessionMessage(`Format non autorisé pour "${file.name}".`, 'error');
                return;
            }

            if (file.size > CONFIG.maxSize) {
                displaySessionMessage(`"${file.name}" dépasse la taille de 5MB.`, 'error');
                return;
            }

            selectedFiles.push(file);
        });

        updateFileInput(photoInput, selectedFiles);
        updateThumbnails(thumbnailsContainer, selectedFiles);
    });

    // Fonction globale pour supprimer une image
    window.removeImageFromThumbnail = (event, index) => {
        event.stopPropagation();
        selectedFiles.splice(index, 1);
        updateFileInput(photoInput, selectedFiles);
        updateThumbnails(thumbnailsContainer, selectedFiles);
    };

    // Initialiser les placeholders
    updateThumbnails(thumbnailsContainer, selectedFiles);
}

function updateThumbnails(container, files) {
    container.innerHTML = '';

    if (files.length === 0) {
        // Placeholders vides
        for (let i = 0; i < CONFIG.maxFiles; i++) {
            const placeholder = document.createElement('div');
            placeholder.className = 'thumbnail';
            placeholder.innerHTML = `<span class="thumbnail-placeholder">Photo ${i + 1}</span>`;
            container.appendChild(placeholder);
        }
    } else {
        // Images sélectionnées
        files.forEach((file, index) => {
            const thumbnailWrapper = document.createElement('div');
            thumbnailWrapper.className = 'thumbnail has-image';

            const reader = new FileReader();
            reader.onload = (e) => {
                thumbnailWrapper.innerHTML = `
                    <div class="thumbnail-content">
                        <img src="${e.target.result}" alt="Prévisualisation" class="thumbnail-preview">
                        <button type="button" class="remove-image-btn" onclick="removeImageFromThumbnail(event, ${index})" title="Supprimer">x</button>
                    </div>
                `;
            };
            reader.readAsDataURL(file);
            container.appendChild(thumbnailWrapper);
        });

        // Compléter avec des placeholders
        const remainingPlaceholders = CONFIG.maxFiles - files.length;
        for (let i = 0; i < remainingPlaceholders; i++) {
            const placeholder = document.createElement('div');
            placeholder.className = 'thumbnail';
            placeholder.innerHTML = `<span class="thumbnail-placeholder">Photo ${files.length + i + 1}</span>`;
            container.appendChild(placeholder);
        }
    }
}

function updateFileInput(input, files) {
    const dataTransfer = new DataTransfer();
    files.forEach(file => dataTransfer.items.add(file));
    input.files = dataTransfer.files;
}

// === GESTION DES PHOTOS - ÉDITION ===
function triggerImageUpload(position) {
    const input = document.getElementById(position + '-input');
    if (input) {
        input.click();
    }
}

function removeExistingImage(imageId, position) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cette image ?')) return;

    if (!imageId || isNaN(imageId)) {
        displaySessionMessage('Erreur : ID d\'image invalide', 'error');
        return;
    }

    if (!imagesToDelete.includes(imageId)) {
        imagesToDelete.push(imageId);
    }

    const deletionField = document.getElementById('images-to-delete');
    if (deletionField) {
        deletionField.value = imagesToDelete.join(',');
    }

    showImagePlaceholder(position);
    displaySessionMessage('Image marquée pour suppression', 'success');
}

function handleImageUpload(input, position) {
    if (!input.files || !input.files[0]) return;

    const file = input.files[0];

    // Validation
    if (file.size > CONFIG.maxSize) {
        displaySessionMessage('Le fichier est trop volumineux. Taille maximum : 5MB', 'error');
        input.value = '';
        return;
    }

    if (!CONFIG.allowedTypes.includes(file.type)) {
        displaySessionMessage('Format non supporté. Utilisez JPG, PNG, WebP ou GIF.', 'error');
        input.value = '';
        return;
    }

    // Stocker le fichier
    selectedFilesForEdit[position] = file;

    // Créer la prévisualisation
    const reader = new FileReader();
    reader.onload = (e) => displayNewImage(position, e.target.result, file.name);
    reader.readAsDataURL(file);
}

function displayNewImage(position, imageSrc, fileName) {
    const container = document.getElementById(position + '-container');
    if (!container) return;

    container.innerHTML = `
        <div class="thumbnail-content">
            <img src="${imageSrc}" alt="${fileName}" id="${position}-display" style="width: 100%; height: 100%; object-fit: cover;">
            <button type="button" class="remove-btn" onclick="removeNewImage('${position}')" title="Supprimer cette image">✕</button>
        </div>
    `;
    container.classList.remove('no-image-placeholder');
}

function removeNewImage(position) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cette image ?')) return;

    const input = document.getElementById(position + '-input');
    if (input) input.value = '';

    delete selectedFilesForEdit[position];
    showImagePlaceholder(position);
    displaySessionMessage('Image supprimée', 'success');
}

function showImagePlaceholder(position) {
    const container = document.getElementById(position + '-container');
    if (!container) return;

    const placeholderText = position === 'thumb-0'
        ? 'Cliquez pour ajouter une photo principale'
        : `Cliquez pour ajouter la photo ${parseInt(position.replace('thumb-', '')) + 1}`;

    const placeholderClass = position === 'thumb-0' ? 'placeholder-text' : 'placeholder-small';

    container.innerHTML = `
        <div class="no-image-placeholder clickable ${placeholderClass}" onclick="triggerImageUpload('${position}')">
            <span>📷</span>
            <p>${placeholderText}</p>
        </div>
    `;
    container.classList.add('no-image-placeholder');
}

function prepareEditFormFiles() {
    const form = document.querySelector('form[action*="/update"]');
    if (!form) return;

    let photosInput = document.getElementById('edit-photos-input');
    if (!photosInput) {
        photosInput = document.createElement('input');
        photosInput.type = 'file';
        photosInput.id = 'edit-photos-input';
        photosInput.name = 'photos[]';
        photosInput.multiple = true;
        photosInput.style.display = 'none';
        form.appendChild(photosInput);
    }

    const dataTransfer = new DataTransfer();
    Object.values(selectedFilesForEdit).forEach(file => {
        if (file) dataTransfer.items.add(file);
    });

    photosInput.files = dataTransfer.files;
}

function initializeEditImageManagement() {
    const editForm = document.querySelector('form[action*="/update"]');
    if (!editForm) return;

    // Initialisation des variables
    imagesToDelete = [];
    selectedFilesForEdit = {};

    // Créer le champ caché pour les suppressions
    if (!document.getElementById('images-to-delete')) {
        const deletionField = document.createElement('input');
        deletionField.type = 'hidden';
        deletionField.id = 'images-to-delete';
        deletionField.name = 'images_to_delete';
        editForm.appendChild(deletionField);
    }

    // Créer les inputs de fichier cachés
    ['thumb-0', 'thumb-1', 'thumb-2'].forEach(position => {
        if (!document.getElementById(position + '-input')) {
            const input = document.createElement('input');
            input.type = 'file';
            input.id = position + '-input';
            input.accept = 'image/*';
            input.style.display = 'none';
            input.addEventListener('change', function () {
                handleImageUpload(this, position);
            });
            editForm.appendChild(input);
        }
    });

    // Validation du formulaire
    editForm.addEventListener('submit', function (e) {
        prepareEditFormFiles();

        // Validation des champs
        const requiredFields = {
            'titre': { min: 3, message: 'Le titre doit contenir au moins 3 caractères.' },
            'description': { min: 10, message: 'La description doit contenir au moins 10 caractères.' },
            'prix': { type: 'number', message: 'Le prix doit être supérieur à 0.' },
            'localite': { min: 1, message: 'La localité est requise.' },
            'category_id': { min: 1, message: 'Une catégorie doit être sélectionnée.' }
        };

        const errors = [];

        Object.entries(requiredFields).forEach(([fieldName, config]) => {
            const field = document.getElementById(fieldName);
            if (!field) return;

            if (config.type === 'number') {
                if (!field.value || parseFloat(field.value) <= 0) {
                    errors.push(config.message);
                }
            } else if (field.value.trim().length < config.min) {
                errors.push(config.message);
            }
        });

        // Validation spécifique pour les véhicules
        const categoryId = document.getElementById('category_id');
        if (categoryId && categoryId.value === CONFIG.vehiculeCategoryId) {
            const kilometrage = document.getElementById('kilometrage');
            if (kilometrage && (!kilometrage.value || parseInt(kilometrage.value) < 0)) {
                errors.push('Le kilométrage est obligatoire pour les véhicules.');
            }
        }

        if (errors.length > 0) {
            e.preventDefault();
            alert('Veuillez corriger les erreurs suivantes :\n\n' +
                errors.map((error, index) => `${index + 1}. ${error}`).join('\n'));
            return false;
        }
    });
}

// === EFFET SCROLL ===
function initializeScrollEffects() {
    const categoriesNav = document.querySelector('.categories-nav');
    if (!categoriesNav) return;

    window.addEventListener('scroll', () => {
        if (window.scrollY > 10) {
            categoriesNav.classList.add('scrolled');
        } else {
            categoriesNav.classList.remove('scrolled');
        }
    });
}

// === INITIALISATION PRINCIPALE ===
document.addEventListener('DOMContentLoaded', () => {
    initializeSearch();
    initializePasswordToggle();
    initializeFiltering();
    initializePhotoUpload();
    initializeEditImageManagement();
    initializeCategoryNavigation();
    initializeConditionalFields();
    initializeScrollEffects();
    initializeBurgerMenu(); // Nouvelle fonction
});

// === EXPOSITION GLOBALE - AJOUT DES NOUVELLES FONCTIONS ===
window.confirmLogout = confirmLogout;
window.confirmDelete = confirmDelete;
window.filterByCategory = filterByCategory;
window.displaySessionMessage = displaySessionMessage;
window.triggerImageUpload = triggerImageUpload;
window.removeExistingImage = removeExistingImage;
window.handleImageUpload = handleImageUpload;
window.removeNewImage = removeNewImage;
window.removeImageFromThumbnail = removeImageFromThumbnail;
window.toggleMobileMenu = toggleMobileMenu; // Nouveau
window.closeMobileMenu = closeMobileMenu;   // Nouveau
window.openMobileMenu = openMobileMenu;     // Nouveau