# Banking Management Application

This is a banking management application built with Symfony, designed for handling user accounts, transactions, and financial operations like deposits, withdrawals, and transfers.

---

## Features

- User registration and authentication
- Create, view, and manage bank accounts
- Perform deposits, withdrawals, and transfers between accounts
- Secure user data with role-based access
- Admin dashboard for user and account management

---

## Installation

### Prerequisites

Ensure you have the following installed on your system:

- **PHP >= 8.1** (Check with `php -v`)
- **Composer** (Install via [getcomposer.org](https://getcomposer.org/))
- **Symfony CLI** (Install via [symfony.com/download](https://symfony.com/download))
- **Database** (MySQL or PostgreSQL)

### Installation Steps

1. **Clone the repository**:
   ```bash
   git clone <https://github.com/Erwan1202/challenge_web.git>
   cd <challenge_web>


2. **Install PHP dependencies**:
    ```bash
    composer install


3. **Set up environment variables: Copy the .env file to .env.local**:


copy and paste the .env to .env.local and change :


DATABASE_URL="mysql://your_username:your_password@127.0.0.1:3306/bank" with your database information (msql for mysql or pgsql)


4. **Set up the database: Run the following commands to create the database and apply migrations**:
    ```bash
    php bin/console doctrine:database:create
    php bin/console doctrine:migrations:migrate


5. **Run the development server: Start the Symfony development server**:
    ```bash
    symfony server:start


The application will be accessible at http://127.0.0.1:8000.


