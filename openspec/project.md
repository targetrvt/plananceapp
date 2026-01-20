# Project Context

## Purpose
Planance is a financial planning and management application designed to help individuals and businesses take control of their financial future. The application provides intelligent budgeting, expense tracking, goal planning, and subscription management—all in one platform.

**Key Goals:**
- Enable users to create and manage detailed budgets with customizable categories
- Track income and expenses with receipt scanning and automatic categorization
- Set and monitor financial goals with visual progress indicators
- Manage monthly subscriptions and recurring expenses
- Provide AI-powered insights and customer support
- Offer multi-language support (English, Latvian)
- Deliver a mobile app experience (Flutter-based, under development)

## Tech Stack

### Backend
- **PHP 8.2+** - Server-side language
- **Laravel 11** - PHP framework
- **Filament 3.2** - Admin panel framework
- **Livewire** - Full-stack framework for dynamic components
- **Eloquent ORM** - Database abstraction layer

### Frontend
- **Vite 6.0** - Build tool and dev server
- **Tailwind CSS 3.4** - Utility-first CSS framework
- **Livewire Components** - Dynamic UI components
- **Axios** - HTTP client

### Mobile
- **Flutter** - Cross-platform mobile framework (in development)

### Testing & Quality
- **Pest PHP 3.7** - PHP testing framework
- **Laravel Pint** - Code style fixer (PSR-12)

### Key Packages
- `filament-shield` - Role-based permissions
- `filament-breezy` - Authentication UI
- `filament-chatgpt-agent` - AI chat integration
- `filament-users` - User management
- `filament-backgrounds` - UI enhancements
- `laravel-gpt` - OpenAI integration
- `bezhansalleh/filament-language-switch` - Multi-language support

### Development Tools
- **Laravel Sail** - Docker development environment
- **Laravel Pail** - Log viewing
- **Concurrently** - Run multiple dev servers simultaneously

## Project Conventions

### Code Style
- **PSR-4 Autoloading** - Namespaces follow directory structure (`App\Models`, `App\Filament\Resources`)
- **PSR-12** - Code style standard (enforced via Laravel Pint)
- **Laravel Naming Conventions:**
  - Models: PascalCase, singular (`Transaction`, `Budget`, `FinancialGoal`)
  - Controllers: PascalCase with suffix (`TransactionController`)
  - Resources: PascalCase with suffix (`TransactionResource`)
  - Database tables: snake_case, plural (`transactions`, `budgets`)
  - Migrations: descriptive with timestamps (`2025_01_28_204249_create_transactions_table.php`)
- **Filament Patterns:**
  - Resource classes extend `Filament\Resources\Resource`
  - Resource pages in subdirectory: `TransactionResource/Pages/`
  - Form components use Fluent API pattern
  - Table columns use Fluent configuration

### Architecture Patterns
- **MVC Architecture** - Models, Views, Controllers separation
- **Resource-Based Admin** - Filament resources for CRUD operations
- **Livewire Components** - Full-stack reactive components without JavaScript
- **Repository Pattern** - Eloquent models as repositories
- **Observer Pattern** - Model observers for side effects (e.g., `MonthlySubscriptionObserver`)
- **Policy-Based Authorization** - Role and permission checks via Filament Shield

**File Organization:**
```
app/
├── Filament/           # Admin panel resources
│   ├── Pages/         # Custom Filament pages
│   ├── Resources/     # CRUD resources
│   └── Widgets/       # Dashboard widgets
├── Http/
│   ├── Controllers/   # Web controllers
│   ├── Middleware/    # Custom middleware
│   └── Requests/      # Form request validation
├── Livewire/          # Livewire components
├── Models/            # Eloquent models
├── Observers/         # Model observers
├── Policies/          # Authorization policies
└── Providers/         # Service providers
```

### Testing Strategy
- **Framework:** Pest PHP with Laravel plugin
- **Test Suites:**
  - Unit tests: `tests/Unit/`
  - Feature tests: `tests/Feature/`
- **Test Environment:**
  - In-memory SQLite for fast tests (when configured)
  - Array-based cache and session drivers
  - Sync queue driver
- **Coverage:** Source code in `app/` directory is included for coverage reports

### Git Workflow
[To be determined - add your branching strategy and commit conventions here]

## Domain Context

### Financial Management Domain
Planance operates in the personal and business financial management space, focusing on:
- **Budget Management:** Users create budgets with defined amounts and date ranges
- **Transaction Tracking:** Income and expense transactions with categories, dates, amounts, and optional receipt images
- **Financial Goals:** Target-based savings/investment goals with progress tracking
- **Subscription Management:** Recurring monthly subscriptions tracking
- **User Balances:** Balance tracking and management

### Core Entities

**Transaction Types:**
- `income` - Money received (salary, investment, gift, refund, other)
- `expense` - Money spent

**Transaction Categories:**
- Income: `salary`, `investment`, `gift`, `refund`, `other_income`
- Expenses: `food`, `shopping`, `entertainment`, `transportation`, `housing`, `utilities`, `health`, `education`, `travel`, `unhealthy_habits`, `other_expense`

**User Model:**
- Extends Laravel's `Authenticatable`
- Uses `HasAvatar` interface (likely Filament's avatar feature)
- Multi-tenancy: All entities filtered by `user_id`

### Business Logic
- **Receipt Processing:** AI-powered receipt scanning extracts transaction details using OpenAI's GPT-4 Vision API
- **Default Currency:** EUR (Euro) - displayed with prefix in forms
- **User Isolation:** All queries filter by authenticated user's ID
- **Multi-language:** Support for English (`en`) and Latvian (`lv`) via translation files

## Important Constraints

### Technical Constraints
- **PHP 8.2+** required for Laravel 11
- **Filament 3** compatibility - resources must follow Filament v3 patterns
- **OpenAI API** required for receipt processing and chat support (requires API key configuration)
- **Storage:** Receipt images stored on `public` disk under `receipts/` directory

### Business Constraints
- User data must be isolated per account
- Financial data requires secure storage and access controls
- Receipt processing depends on external AI service availability
- Multi-language support currently limited to EN/LV

### Regulatory Considerations
- Financial data handling may require compliance with data protection regulations
- Receipt/image storage should respect privacy requirements

## External Dependencies

### APIs & Services
- **OpenAI API** (`api.openai.com`)
  - GPT-4o-mini for receipt image processing
  - GPT models for customer support chat
  - Requires API key in `config/services.openai.api_key`
  
### Third-Party Packages
- **Filament Ecosystem:**
  - `filament/filament` - Core admin panel
  - `filament-shield` - Role/permission management
  - `filament-breezy` - Authentication
  - `filament-chatgpt-agent` - AI chat integration
  - `filament-users` - User management UI
  - `filament-backgrounds` - UI enhancements
- **Authentication:** Laravel Breezy (via Filament)
- **Language Support:** `bezhansalleh/filament-language-switch`

### Infrastructure
- **Database:** Supports Laravel's database drivers (MySQL, PostgreSQL, SQLite)
- **Queue System:** Laravel queues (configurable driver)
- **Cache:** Laravel cache (configurable driver)
- **Storage:** Laravel filesystem (local storage for receipts)
- **Mail:** Laravel mail system (configurable driver)
