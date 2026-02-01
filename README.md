# StudyBuddy Backend

E-learning marketplace backend for buying and selling study materials. Built with Laravel 12.

![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php&logoColor=white)
![Sanctum](https://img.shields.io/badge/Sanctum-4.0-FF2D20?style=flat&logo=laravel&logoColor=white)
![Stripe](https://img.shields.io/badge/Stripe-Payments-635BFF?style=flat&logo=stripe&logoColor=white)

## Features

- **User Authentication** - Register/login with Laravel Sanctum API tokens
- **Material Marketplace** - Browse, filter, search, and sort study materials
- **Shopping Cart** - Add materials to cart and checkout with Stripe
- **Note Management** - Upload notes with file attachments and preview images
- **Admin Dashboard** - Manage users, materials, schools, levels, and modules
- **User Blocking** - Admin can block users from accessing the platform

## Tech Stack

- **Framework:** Laravel 12
- **Authentication:** Laravel Jetstream + Sanctum
- **Frontend Components:** Livewire 3.6
- **File Management:** Spatie MediaLibrary
- **Payments:** Stripe PHP SDK
- **Testing:** PHPUnit 11.5

## Installation

```bash
# Clone the repository
git clone https://github.com/nethmipalliyaguruge/StudyBuddy-Backend-Laravel.git
cd studybuddy-backend

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run database migrations
php artisan migrate

# Build frontend assets
npm run build
```

Or use the composer setup script:

```bash
composer setup
```

## Environment Variables

Configure these variables in your `.env` file:

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=studybuddy
DB_USERNAME=root
DB_PASSWORD=

# Stripe
STRIPE_KEY=pk_test_xxx
STRIPE_SECRET=sk_test_xxx

# Mail (optional)
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
```

## Running the Application

```bash
# Run all services concurrently (server, queue, logs, vite)
composer dev

# Or run individually
php artisan serve      # Laravel server
npm run dev            # Vite dev server
php artisan queue:listen  # Queue worker
```

## API Endpoints

### Public Routes

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/register` | Register a new user |
| POST | `/api/login` | Login and get token |
| GET | `/api/schools` | List all schools |
| GET | `/api/schools/{id}` | Get school details |
| GET | `/api/levels` | List all levels |
| GET | `/api/levels/{id}` | Get level details |
| GET | `/api/modules` | List all modules |
| GET | `/api/modules/{id}` | Get module details |
| GET | `/api/materials` | Browse materials (with filtering) |
| GET | `/api/materials/{id}` | Get material details |

### Protected Routes (Require Authentication)

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/logout` | Logout current session |
| POST | `/api/logout-all` | Logout all sessions |
| GET | `/api/user` | Get authenticated user |
| GET | `/api/my-notes` | List user's notes |
| POST | `/api/notes` | Create a new note |
| PUT | `/api/notes/{id}` | Update a note |
| DELETE | `/api/notes/{id}` | Delete a note |
| GET | `/api/my-purchases` | List user's purchases |
| GET | `/api/purchases/{id}` | Get purchase details |
| GET | `/api/purchases/{id}/download` | Download purchased file |
| POST | `/api/cart` | Get cart summary |
| GET | `/api/cart/validate/{id}` | Validate cart item |
| POST | `/api/checkout` | Create Stripe checkout session |
| POST | `/api/checkout/verify` | Verify payment completion |

### Authentication

Include the token in the Authorization header:

```
Authorization: Bearer <your-token>
```

## Database Schema

### Core Tables

| Table | Description |
|-------|-------------|
| `users` | User accounts with roles (admin/student) and blocking status |
| `schools` | Educational institutions |
| `levels` | Academic levels (e.g., Year 1, Year 2) |
| `modules` | Course modules linked to levels |
| `notes` | Study materials uploaded by users |
| `purchases` | Purchase records with Stripe payment info |
| `media` | File attachments (Spatie MediaLibrary) |

### Key Relationships

- Users have many Notes and Purchases
- Schools have many Levels
- Levels have many Modules
- Modules have many Notes
- Notes have many Purchases

## Testing

```bash
# Run all tests
php artisan test

# Or use composer script
composer test
```

Test coverage includes:
- Unit tests for models
- Controller tests for API endpoints
- Middleware tests (Admin, Blocked)
- Integration tests for auth and purchase flows

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/          # API controllers
│   │   └── Web/          # Web controllers
│   ├── Livewire/         # Livewire components
│   └── Middleware/       # Custom middleware
├── Models/               # Eloquent models
└── Providers/            # Service providers

database/
├── factories/            # Model factories
├── migrations/           # Database migrations
└── seeders/              # Database seeders

routes/
├── api.php              # API routes
└── web.php              # Web routes

tests/
├── Feature/             # Feature tests
└── Unit/                # Unit tests
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
