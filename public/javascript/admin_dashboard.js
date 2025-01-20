// Fonction pour afficher les comptes et les transactions d'un utilisateur sélectionné
function updateDashboard(userId) {
    fetch(`/dashboard/user/${userId}`)
        .then(response => response.json())
        .then(data => {
            const accountsList = document.querySelector('#accountsList');
            const transactionsList = document.querySelector('#transactionsList');

            // Vider les listes avant de les remplir
            accountsList.innerHTML = '';
            transactionsList.innerHTML = '';

            // Vérification des données reçues
            console.log(data); // Vérifiez dans la console si les données sont bien reçues

            // Générer la liste des comptes
            data.comptes.forEach(compte => {
                const accountItem = document.createElement('div');
                accountItem.classList.add('accountsItem');
                accountItem.setAttribute('data-compte-id', compte.id);

                accountItem.innerHTML = `
                    <div>
                        <div>
                            <h3>${compte.numeroDeCompte}</h3>
                            <p>${compte.type}</p>
                        </div>
                        <!-- Formulaire de suppression du compte -->
                        <form method="post" class="delete-account-form" data-compte-id="${compte.id}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce compte ?');">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <button type="submit" class="btn btn-danger">
                                <img src="${assetPaths.croix}" alt="Supprimer"/>
                            </button>
                        </form>
                        <a href="#"><img src="${assetPaths.cadeau}" alt="Bloquer"></a>
                    </div>
                    <p>${compte.solde} €</p>
                `;
                accountsList.appendChild(accountItem);
            });

            // Générer l'historique des transactions
            data.transactions.forEach(transaction => {
                const transactionItem = document.createElement('div');
                transactionItem.classList.add('transactionsHistoryItem');

                transactionItem.innerHTML = `
                    <div>
                        <p>${transaction.dateHeure}</p>
                        <p>${transaction.type}</p>
                        <p>${transaction.statut}</p>
                    </div>
                    <p>${transaction.montant} €</p>
                    <a href="#"><img src="${assetPaths.croix}" alt="Annuler la transaction"></a>
                `;
                transactionsList.appendChild(transactionItem);
            });

            // Ajouter un événement de suppression pour chaque formulaire
            document.querySelectorAll('.delete-account-form').forEach(form => {
                form.addEventListener('submit', function(event) {
                    event.preventDefault(); // Empêcher l'envoi immédiat du formulaire
                    const compteId = form.getAttribute('data-compte-id'); // Obtenir l'ID du compte

                    // Remplacer '__id__' par l'ID du compte dans l'URL de suppression
                    const actionUrl = deleteAccountUrl.replace('__id__', compteId); // Mettre à jour l'URL de l'action

                    // Mettre à jour l'attribut action du formulaire pour envoyer la bonne requête
                    form.setAttribute('action', actionUrl);
                    form.submit(); // Soumettre le formulaire après avoir mis à jour l'action
                });
            });
        })
        .catch(error => {
            console.error('Erreur lors de la récupération des données:', error);
        });
}

// Ajouter un événement pour mettre à jour les informations lorsque l'utilisateur clique sur un utilisateur
document.querySelectorAll('.userAccountsItem').forEach(item => {
    item.addEventListener('click', function(event) {
        event.preventDefault();
        const userId = this.getAttribute('data-user-id');
        updateDashboard(userId);
    });
});

document.querySelectorAll('.userAccountsItem').forEach(item => {
    item.addEventListener('click', function(event) {
        event.preventDefault();

        // Récupérer l'ID de l'utilisateur sélectionné
        const userId = this.getAttribute('data-user-id');

        // Mettre à jour visuellement l'utilisateur sélectionné
        document.querySelectorAll('.userAccountsItem').forEach(userItem => {
            userItem.classList.remove('selected');
        });
        this.classList.add('selected'); // Ajoutez une classe CSS pour l'utilisateur sélectionné

        // Mettre à jour le tableau de bord
        updateDashboard(userId);
    });
});


document.addEventListener('DOMContentLoaded', () => {
    const transactionsLinks = document.querySelectorAll('.transactionsActionsLinks');
    const userLinks = document.querySelectorAll('.userAccountsItem');

    let selectedUserId = null;

    // Capture de l'utilisateur sélectionné
    userLinks.forEach(userLink => {
        userLink.addEventListener('click', (event) => {
            event.preventDefault();
            selectedUserId = userLink.dataset.userId;

            // Optionnel : mettre en évidence l'utilisateur sélectionné
            userLinks.forEach(link => link.classList.remove('selected'));
            userLink.classList.add('selected');
        });
    });

    // Gestion des clics sur les actions de transaction
    transactionsLinks.forEach(link => {
        link.addEventListener('click', (event) => {
            event.preventDefault();

            const action = link.dataset.action;
            if (!action) return;

            let url = `/transaction/${action}`;

            if (selectedUserId) {
                url += `/${selectedUserId}`;
            }

            // Redirection vers l'URL construite
            window.location.href = url;
        });
    });
});