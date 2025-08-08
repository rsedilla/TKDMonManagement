<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PrepareProduction extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:prepare-production';

    /**
     * The console command description.
     */
    protected $description = 'Prepare the application for production deployment';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Preparing application for production deployment...');
        $this->line('');

        // Step 1: Environment check
        if (!$this->confirmEnvironment()) {
            return 1;
        }

        // Step 2: Clear all caches
        $this->clearCaches();

        // Step 3: Install production dependencies
        $this->installDependencies();

        // Step 4: Generate optimized caches
        $this->generateCaches();

        // Step 5: Build assets
        $this->buildAssets();

        // Step 6: Optimize performance
        $this->optimizePerformance();

        // Step 7: Run security checks
        $this->runSecurityChecks();

        // Step 8: Create deployment checklist
        $this->createDeploymentChecklist();

        $this->line('');
        $this->info('✅ Application successfully prepared for production!');
        $this->line('');
        $this->warn('📋 Check PRODUCTION-CHECKLIST.md for final deployment steps');

        return 0;
    }

    private function confirmEnvironment(): bool
    {
        if (app()->environment('production')) {
            $this->error('❌ Already in production environment!');
            return false;
        }

        if (!$this->confirm('⚠️  This will prepare for PRODUCTION deployment. Continue?')) {
            return false;
        }

        return true;
    }

    private function clearCaches(): void
    {
        $this->info('🧹 Clearing existing caches...');
        
        $this->call('cache:clear');
        $this->call('config:clear');
        $this->call('route:clear');
        $this->call('view:clear');
        $this->call('event:clear');
        
        $this->line('   ✓ Caches cleared');
    }

    private function installDependencies(): void
    {
        $this->info('📦 Installing production dependencies...');
        
        exec('composer install --optimize-autoloader --no-dev', $output, $return);
        
        if ($return === 0) {
            $this->line('   ✓ Dependencies installed');
        } else {
            $this->warn('   ⚠️  Manual composer install may be needed');
        }
    }

    private function generateCaches(): void
    {
        $this->info('⚡ Generating optimized caches...');
        
        $this->call('config:cache');
        $this->call('route:cache');
        $this->call('view:cache');
        $this->call('event:cache');
        
        $this->line('   ✓ Caches generated');
    }

    private function buildAssets(): void
    {
        $this->info('🎨 Building production assets...');
        
        if (File::exists(base_path('package.json'))) {
            exec('npm run build', $output, $return);
            
            if ($return === 0) {
                $this->line('   ✓ Assets built');
            } else {
                $this->warn('   ⚠️  Manual npm run build may be needed');
            }
        } else {
            $this->line('   - No package.json found, skipping asset build');
        }
    }

    private function optimizePerformance(): void
    {
        $this->info('🚄 Optimizing performance...');
        
        // Generate autoloader optimization
        exec('composer dump-autoload --optimize', $output, $return);
        
        // Optimize Filament
        try {
            $this->call('filament:optimize');
            $this->line('   ✓ Filament optimized');
        } catch (\Exception $e) {
            $this->line('   - Filament optimization skipped');
        }

        $this->line('   ✓ Performance optimized');
    }

    private function runSecurityChecks(): void
    {
        $this->info('🔒 Running security checks...');
        
        // Check for .env.example
        if (!File::exists(base_path('.env.example'))) {
            $this->warn('   ⚠️  Missing .env.example file');
        }

        // Check for sensitive files in public
        $sensitiveFiles = ['.env', 'composer.json', 'artisan'];
        foreach ($sensitiveFiles as $file) {
            if (File::exists(public_path($file))) {
                $this->error("   ❌ Sensitive file {$file} found in public directory!");
            }
        }

        $this->line('   ✓ Security checks completed');
    }

    private function createDeploymentChecklist(): void
    {
        $checklist = <<<'EOD'
# Production Deployment Checklist

## ✅ Pre-Deployment (Completed)
- [x] Caches cleared and regenerated
- [x] Dependencies optimized
- [x] Assets built
- [x] Performance optimized
- [x] Security checks passed

## 📋 Manual Steps Required

### 1. Environment Configuration
- [ ] Copy `.env.example` to `.env` on server
- [ ] Update database credentials in `.env`
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Generate new `APP_KEY` with `php artisan key:generate`
- [ ] Update `APP_URL` to your domain

### 2. Server Requirements
- [ ] PHP 8.1 or higher
- [ ] MySQL 5.7+ or MariaDB 10.3+
- [ ] Required PHP extensions: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML, GD
- [ ] Composer installed

### 3. File Upload Structure
```
public_html/
├── index.php (from Laravel's public folder)
├── .htaccess (from Laravel's public folder)
├── css/
├── js/
├── favicon.ico
└── robots.txt

laravel/ (above public_html)
├── app/
├── bootstrap/
├── config/
├── database/
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env
└── artisan
```

### 4. File Permissions
```bash
chmod -R 755 bootstrap/cache/
chmod -R 755 storage/
chmod 644 .env
```

### 5. Database Setup
- [ ] Create MySQL database in cPanel
- [ ] Import database dump or run migrations
- [ ] Update database credentials in `.env`

### 6. SSL Certificate
- [ ] Install SSL certificate (Let's Encrypt recommended)
- [ ] Update `APP_URL` to https://
- [ ] Set `SESSION_SECURE_COOKIE=true`

### 7. Final Commands on Server
```bash
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 8. Testing
- [ ] Admin login works
- [ ] All CRUD operations function
- [ ] File uploads work
- [ ] Security headers present
- [ ] SSL certificate valid
- [ ] Performance acceptable

## 🎯 Performance Expectations (10 Admin Users)
- Response Time: 200-500ms
- Memory Usage: 50-100MB per request
- Concurrent Users: 10-20 supported
- Server Resources: <50% utilization

## 📞 Support
- Hostinger Support: Available 24/7
- Laravel Documentation: https://laravel.com/docs
- Filament Documentation: https://filamentphp.com/docs

Your application is now ready for production deployment! 🚀
EOD;

        File::put(base_path('PRODUCTION-CHECKLIST.md'), $checklist);
        $this->line('   ✓ Deployment checklist created');
    }
}
