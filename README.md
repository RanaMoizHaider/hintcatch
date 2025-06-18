# Hint Catch

A modern prompt sharing platform built with Laravel, Livewire, and Flux components. Hint Catch allows users to create, share, and discover AI prompts optimized for various AI models and platforms.

> **Built with AI**: This project was created using AI development tools, primarily GitHub Copilot in VS Code with Claude Sonnet 4, along with various other AI assistants. It's a testament to the power of AI-assisted development!

## Features

- 🚀 **Modern UI** with Flux free components and Tailwind CSS
- 🤖 **AI Model Management** - Support for various AI providers (OpenAI, Anthropic, Google, Meta, etc.)
- 📝 **Prompt Management** - Create, edit, and share prompts with rich categorization
- 🏷️ **Smart Tagging** - Organize prompts with categories, tags, and compatibility info
- 👥 **User Profiles** - User authentication and profile management
- 💝 **Social Features** - Like, comment, and interact with prompts
- 📊 **Analytics** - Track prompt views and engagement
- 🔍 **Advanced Search** - Filter by categories, platforms, models, and providers
- 📱 **Responsive Design** - Works beautifully on all devices
- ⚡ **Real-time Updates** with Livewire
- 🎨 **Beautiful Admin Panel** for content management

## Tech Stack

- **Backend**: Laravel 12
- **Frontend**: Livewire 3 + Volt
- **UI Components**: Flux (free components)
- **Styling**: Tailwind CSS 4
- **Database**: SQLite (configurable)
- **Build Tool**: Vite
- **Development Environment**: Laravel Herd

## Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & npm
- [Laravel Herd](https://herd.laravel.com/) (recommended for local development)

## Installation

### Quick Setup with Herd

1. **Clone the repository**
   ```bash
   git clone https://github.com/RanaMoizHaider/hintcatch.git
   cd hintcatch
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Set up environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database**
   
   The project is pre-configured to use SQLite. The database file will be created automatically when you run migrations.

6. **Run migrations and seed data**
   ```bash
   php artisan migrate --seed
   ```

7. **Build assets**
   ```bash
   npm run build
   ```

8. **Start the development server**
   
   If using Herd, the site should be accessible at `http://hintcatch.test` automatically.
   
   Otherwise, run:
   ```bash
   php artisan serve
   ```

### Manual Setup (without Herd)

If you're not using Herd, you can set up the project manually:

1. Follow steps 1-7 from the Herd setup
2. Configure your web server to point to the `public` directory
3. Make sure the `storage` and `bootstrap/cache` directories are writable

## Development

### Asset Development

Start the Vite development server for hot reloading:

```bash
npm run dev
```

### Running Tests

```bash
php artisan test
```

or with Pest:

```bash
./vendor/bin/pest
```

### Code Formatting

Format your code with Laravel Pint:

```bash
./vendor/bin/pint
```

## Database Structure

The application includes several key models:

- **Users** - User accounts and profiles
- **Prompts** - The core prompt content with metadata
- **Categories** - Hierarchical categorization system
- **Platforms** - AI platforms (ChatGPT, Claude, etc.)
- **Providers** - AI service providers (OpenAI, Anthropic, etc.)
- **AiModels** - Specific AI models (GPT-4, Claude 3.5, etc.)
- **Comments** & **Likes** - Social interaction features

### Key Relationships

- Prompts belong to Users and Categories
- Prompts can be associated with multiple AI Models and Platforms
- AI Models belong to Providers
- Categories support nested hierarchies
- Tagging system using Spatie Laravel Tags

## Contributing

We welcome contributions! Here's how to get started:

### Setting Up for Development

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/amazing-feature`
3. Follow the installation steps above
4. Make your changes
5. Write/update tests as needed
6. Ensure code style compliance: `./vendor/bin/pint`
7. Run tests: `php artisan test`
8. Commit your changes: `git commit -m 'Add amazing feature'`
9. Push to your branch: `git push origin feature/amazing-feature`
10. Open a Pull Request

### Development Guidelines

- **Code Style**: Follow PSR-12 standards (enforced by Pint)
- **Testing**: Write tests for new features using Pest (currently needed throughout the project!)
- **Commits**: Use conventional commit messages
- **Documentation**: Update documentation for new features
- **AI-Assisted Development**: Feel free to use AI tools to accelerate development - this entire project was built with AI assistance!

### Project Structure

```
app/
├── Http/Controllers/     # HTTP controllers
├── Livewire/            # Livewire components
│   ├── Actions/         # Livewire action classes
│   └── Components/      # Reusable Livewire components
├── Models/              # Eloquent models
├── Providers/           # Service providers
└── Traits/              # Reusable traits

resources/
├── css/                 # Stylesheets
├── js/                  # JavaScript files
└── views/               # Blade templates
    ├── livewire/        # Livewire views
    ├── pages/           # Page views (using Volt)
    └── components/      # Blade components

database/
├── factories/           # Model factories
├── migrations/          # Database migrations
└── seeders/             # Database seeders
```

### Key Features to Contribute To

- 🧪 **Testing** - The project currently lacks comprehensive tests! This is a great opportunity to contribute by adding unit, feature, and integration tests using Pest
- 🔄 **Import/Export** - Bulk prompt import/export functionality
- 📱 **Mobile App** - NativePHP mobile application
- 🔗 **API Integration** - Direct integration with AI provider APIs
- 🌍 **Internationalization** - Multi-language support
- 📊 **Analytics Dashboard** - Enhanced analytics and reporting

> **⚠️ Testing Needed**: This project was built rapidly with AI assistance and currently lacks comprehensive test coverage. Contributing tests would be incredibly valuable and is a great way to get familiar with the codebase!

## Environment Configuration

### Required Environment Variables

```bash
APP_NAME="Hint Catch"
APP_ENV=local
APP_KEY=                 # Generated with php artisan key:generate
APP_DEBUG=true
APP_URL=http://localhost

# Database (SQLite by default)
DB_CONNECTION=sqlite

# Session
SESSION_DRIVER=database

# Queue (database by default)
QUEUE_CONNECTION=database

# Cache
CACHE_STORE=database
```

### Optional Configuration

- **Mail**: Configure `MAIL_*` variables for email notifications
- **Redis**: Set up Redis for improved caching and sessions
- **File Storage**: Configure cloud storage for file uploads

## Deployment

### Production Checklist

1. Set `APP_ENV=production` and `APP_DEBUG=false`
2. Configure a production database (MySQL/PostgreSQL)
3. Set up proper caching: `php artisan config:cache`

### Deployment Commands

```bash
# Optimize for production
php artisan optimize
php artisan view:cache
php artisan route:cache
php artisan config:cache

# Run migrations
php artisan migrate --force

# Build assets
npm run build
```

## Troubleshooting

### Common Issues

1. **Permission Issues**: Ensure `storage/` and `bootstrap/cache/` are writable
2. **Asset Issues**: Run `npm run build` if styles/scripts aren't loading
3. **Database Issues**: Verify the SQLite file exists and is writable
4. **Livewire Issues**: Clear browser cache and run `php artisan livewire:discover`

### Getting Help

- Check the [Laravel Documentation](https://laravel.com/docs)
- Review [Livewire Documentation](https://livewire.laravel.com/docs)
- Check [Flux Component Documentation](https://fluxui.dev/docs)
- Open an issue in this repository for project-specific problems

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## Acknowledgments

- Built with [Laravel](https://laravel.com/)
- UI powered by [Livewire](https://livewire.laravel.com/) and [Flux](https://fluxui.dev/)
- Styled with [Tailwind CSS](https://tailwindcss.com/)
- Development environment by [Laravel Herd](https://herd.laravel.com/)
- **Created with AI**: Developed using GitHub Copilot, Claude Sonnet 4, and other AI development tools
- **Icon**: Based on "Background success ideas" by [Freepik](https://www.freepik.com/free-vector/background-success-ideas_1048531.htm)

---

Made with ❤️ and 🤖 for the AI community. Happy prompting!
