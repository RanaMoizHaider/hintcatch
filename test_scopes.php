<?php

/**
 * Test script to verify that Laravel 12.x scopes are working correctly
 * This script tests the new #[Scope] attribute syntax
 */

require __DIR__ . '/vendor/autoload.php';

use App\Models\Provider;
use App\Models\AiModel;
use App\Models\Comment;
use App\Models\Platform;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

// Initialize Laravel app
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing Laravel 12.x Scope Functionality\n";
echo "=====================================\n\n";

// Test 1: Check if scopes are callable
echo "1. Testing scope method availability:\n";

$models = [
    'Provider' => Provider::class,
    'AiModel' => AiModel::class,
    'Comment' => Comment::class,
    'Platform' => Platform::class,
    'Category' => Category::class,
];

foreach ($models as $name => $class) {
    echo "   Testing {$name}:\n";
    
    try {
        // Test if approved scope exists and is callable
        $query = $class::approved();
        if ($query instanceof Builder) {
            echo "     ✓ approved() scope works\n";
        } else {
            echo "     ✗ approved() scope returned: " . gettype($query) . "\n";
        }
        
        // Test if unapproved scope exists and is callable
        $query = $class::unapproved();
        if ($query instanceof Builder) {
            echo "     ✓ unapproved() scope works\n";
        } else {
            echo "     ✗ unapproved() scope returned: " . gettype($query) . "\n";
        }
        
        // Test if withUnapproved scope exists and is callable
        $query = $class::withUnapproved();
        if ($query instanceof Builder) {
            echo "     ✓ withUnapproved() scope works\n";
        } else {
            echo "     ✗ withUnapproved() scope returned: " . gettype($query) . "\n";
        }
        
    } catch (Exception $e) {
        echo "     ✗ Error testing {$name}: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

// Test 2: Check SQL generation
echo "2. Testing generated SQL queries:\n";

foreach ($models as $name => $class) {
    echo "   {$name} SQL queries:\n";
    
    try {
        // Test approved scope SQL
        $sql = $class::approved()->toSql();
        echo "     approved(): {$sql}\n";
        
        // Test unapproved scope SQL
        $sql = $class::unapproved()->toSql();
        echo "     unapproved(): {$sql}\n";
        
        // Test withUnapproved scope SQL
        $sql = $class::withUnapproved()->toSql();
        echo "     withUnapproved(): {$sql}\n";
        
    } catch (Exception $e) {
        echo "     ✗ Error generating SQL for {$name}: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

// Test 3: Check global scope application
echo "3. Testing global scope (ApprovedScope) application:\n";

foreach ($models as $name => $class) {
    try {
        $defaultQuery = $class::query()->toSql();
        echo "   {$name} default query: {$defaultQuery}\n";
        
        // Check if is_approved filter is automatically applied
        if (strpos($defaultQuery, 'is_approved') !== false) {
            echo "     ✓ Global ApprovedScope is applied\n";
        } else {
            echo "     ⚠ Global ApprovedScope may not be applied (or table doesn't exist)\n";
        }
        
    } catch (Exception $e) {
        echo "     ✗ Error testing global scope for {$name}: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

echo "Test completed!\n";
echo "\nNote: Some tests may show warnings if database tables don't exist yet.\n";
echo "The important thing is that the scope methods are callable and return Builder instances.\n";
