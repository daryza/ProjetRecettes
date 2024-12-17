// Écoute l'événement clic sur le bouton "Ajouter un ingrédient"
document.getElementById('ajouter-ingredient-btn').addEventListener('click', function () {
    // Récupère les valeurs
    const ingredient = document.getElementById('ingredient');
    const quantite = document.getElementById('quantite');
    const unite = document.getElementById('unite');

    // Valide que l'ingrédient est sélectionné
    if (!ingredient.value) {
        alert('Veuillez sélectionner un ingrédient.');
        return;
    }

    // Valide que la quantité est saisie
    if (!quantite.value) {
        alert('Veuillez saisir une quantité.');
        return;
    }

    // Vérifie si l'ingrédient a déjà été ajouté
    const liste = document.getElementById('liste-ingredients');
    const exists = Array.from(liste.children).some(li => li.dataset.ingredientId === ingredient.value);
    if (exists) {
        alert('Cet ingrédient a déjà été ajouté.');
        return;
    }

    // Ajoute l'ingrédient à la liste affichée
    const li = document.createElement('li');
    li.textContent = `${ingredient.options[ingredient.selectedIndex].text} - ${quantite.value} ${unite.value}`;
    li.dataset.ingredientId = ingredient.value; // Stocke l'ID de l'ingrédient
    liste.appendChild(li);

    // Ajoute les champs cachés pour l'envoi du formulaire
    const form = document.querySelector('form');
    form.appendChild(createHiddenInput('ingredients[]', ingredient.value));
    form.appendChild(createHiddenInput('ingredients_quantites[]', quantite.value));
    form.appendChild(createHiddenInput('ingredients_unites[]', unite.value));

    // Réinitialise les champs
    quantite.value = '';
    unite.value = 'g';
});

// Fonction utilitaire pour créer un champ caché
function createHiddenInput(name, value) {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = name;
    input.value = value;
    return input;
}
