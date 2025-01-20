async function updateDashboard(userId) {
    try {
        const data = await fetchDashboardData(userId);
        updateAccountList(data.comptes);
        updateTransactionList(data.transactions);
    } catch (error) {
        console.error('Erreur lors de la récupération des données:', error);
    }
}

async function fetchDashboardData(userId) {
    const response = await fetch(`/dashboard/user/${userId}`);
    if (!response.ok) {
        throw new Error('Erreur réseau lors de la récupération des données.');
    }
    return await response.json();
}

function updateAccountList(comptes) {
    const accountsList = document.querySelector('#accountsList');
    accountsList.innerHTML = '';

    comptes.forEach(compte => {
        const accountItem = createAccountItem(compte);
        accountsList.appendChild(accountItem);
    });
}

function createAccountItem(compte) {
    const accountItem = document.createElement('div');
    accountItem.classList.add('accountsItem');
    accountItem.dataset.compteId = compte.id;

    accountItem.innerHTML = `
        <div>
            <div>
                <h3>${compte.numeroDeCompte}</h3>
                <p>${compte.type}</p>
            </div>
            <form method="post" class="delete-account-form" data-compte-id="${compte.id}" onsubmit="return confirmDelete();">
                <input type="hidden" name="_token" value="${csrfToken}">
                <button type="submit" class="btn btn-danger">
                    <img src="${assetPaths.croix}" alt="Supprimer"/>
                </button>
            </form>
            <a href="#"><img src="${assetPaths.cadeau}" alt="Bloquer"></a>
        </div>
        <p>${compte.solde} €</p>
    `;
    return accountItem;
}

function confirmDelete() {
    return confirm('Êtes-vous sûr de vouloir supprimer ce compte ?');
}

function updateTransactionList(transactions) {
    const transactionsList = document.querySelector('#transactionsList');
    transactionsList.innerHTML = '';

    transactions.forEach(transaction => {
        const transactionItem = createTransactionItem(transaction);
        transactionsList.appendChild(transactionItem);
    });
}

function createTransactionItem(transaction) {
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
    return transactionItem;
}

function addUserSelectionListeners() {
    const userLinks = document.querySelectorAll('.userAccountsItem');
    userLinks.forEach(link => {
        link.addEventListener('click', handleUserSelection);
    });
}

function handleUserSelection(event) {
    event.preventDefault();
    const selectedUserId = event.currentTarget.dataset.userId;

    highlightSelectedUser(event.currentTarget);
    updateDashboard(selectedUserId);
}

function highlightSelectedUser(selectedUser) {
    const userLinks = document.querySelectorAll('.userAccountsItem');
    userLinks.forEach(link => link.classList.remove('selected'));
    selectedUser.classList.add('selected');
}

function addTransactionActionsListeners() {
    const transactionsLinks = document.querySelectorAll('.transactionsActionsLinks');
    transactionsLinks.forEach(link => {
        link.addEventListener('click', handleTransactionAction);
    });
}

function handleTransactionAction(event) {
    event.preventDefault();
    const action = event.currentTarget.dataset.action;
    if (!action) return;

    const userId = getSelectedUserId();
    const url = buildTransactionUrl(action, userId);
    window.location.href = url;
}

function getSelectedUserId() {
    const selectedUser = document.querySelector('.userAccountsItem.selected');
    return selectedUser ? selectedUser.dataset.userId : null;
}

function buildTransactionUrl(action, userId) {
    let url = `/transaction/${action}`;
    if (userId) {
        url += `/${userId}`;
    }
    return url;
}

document.addEventListener('DOMContentLoaded', () => {
    addUserSelectionListeners();
    addTransactionActionsListeners();
});