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
- **Node.js and npm** (Optional, for managing assets)

### Installation Steps

1. **Clone the repository**:
   ```bash
   git clone <repository-url>
   cd <project-directory>


2. Install PHP dependencies:


composer install

3. Set up environment variables: Copy the .env file to .env.local:


copy and paste the .env to .env.local and change DATABASE_URL="mysql://username:password@127.0.0.1:3306/database_name" with your database information (msql for mysql or pgsql)


4. Set up the database: Run the following commands to create the database and apply migrations:



php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate


5. Run the development server: Start the Symfony development server:

symfony server:start

The application will be accessible at http://127.0.0.1:8000.


