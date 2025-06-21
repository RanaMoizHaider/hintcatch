# Model Scopes Usage Examples

## Global Scopes Applied by Default

### Prompt Model
The following global scopes are applied automatically to all Prompt queries:
1. **PublishedScope**: Only includes prompts with `status = 'published'`, `published_at IS NOT NULL`, and `published_at <= now()`
2. **VisibilityScope**: Only includes prompts with `visibility = 'public'`

### Approval-Based Models (Comment, Category, Platform, Provider, AiModel)
The following global scope is applied automatically:
1. **ApprovedScope**: Only includes records with `is_approved = true`

### User Model
No global scopes applied - all users are accessible by default.

## Available Local Scopes

### Prompt Scopes

#### Status Scopes
```php
// Get only published prompts (this is already applied by default)
Prompt::published()->get();

// Get only draft prompts
Prompt::draft()->get();
```

#### Visibility Scopes
```php
// Get only public prompts (this is already applied by default)
Prompt::public()->get();

// Get only private prompts
Prompt::private()->get();

// Get only unlisted prompts
Prompt::unlisted()->get();
```

#### Feature Scope
```php
// Get only featured prompts
Prompt::featured()->get();
```

#### Bypass Global Scopes
```php
// Include draft prompts (removes PublishedScope only)
Prompt::withDrafts()->get(); // Gets: public drafts + public published

// Include private/unlisted prompts (removes VisibilityScope only)
Prompt::withPrivate()->get(); // Gets: published public/private/unlisted

// Get ALL prompts regardless of status or visibility
Prompt::withAll()->get(); // Gets: everything (draft/published + public/private/unlisted)
```

### User Scopes

#### Role-Based Scopes
```php
// Get only admin users
User::admins()->get();

// Get only regular (non-admin) users
User::regular()->get();
```

#### Email Verification Scopes
```php
// Get only users with verified email
User::verified()->get();

// Get only users with unverified email
User::unverified()->get();
```

### Approval Scopes (Comment, Category, Platform, Provider, AiModel)

#### Approval Status Scopes
```php
// Get only approved records (this is already applied by default)
Comment::approved()->get();
Category::approved()->get();
Platform::approved()->get();
Provider::approved()->get();
AiModel::approved()->get();

// Get only unapproved records
Comment::unapproved()->get();
Category::unapproved()->get();
Platform::unapproved()->get();
Provider::unapproved()->get();
AiModel::unapproved()->get();
```

#### Bypass Approval Scope
```php
// Include unapproved records (removes ApprovedScope)
Comment::withUnapproved()->get(); // Gets: approved + unapproved comments
Category::withUnapproved()->get(); // Gets: approved + unapproved categories
Platform::withUnapproved()->get(); // Gets: approved + unapproved platforms
Provider::withUnapproved()->get(); // Gets: approved + unapproved providers
AiModel::withUnapproved()->get(); // Gets: approved + unapproved AI models
```

## Practical Usage Examples

### Frontend (default behavior)
```php
// These automatically get only public + published prompts
$featuredPrompts = Prompt::featured()->get();
$allPrompts = Prompt::all();
$categoryPrompts = Prompt::where('category_id', 1)->get();

// These automatically get only approved records
$approvedCategories = Category::all();
$approvedProviders = Provider::all();
$approvedPlatforms = Platform::all();
$approvedComments = Comment::all();
$approvedAiModels = AiModel::all();

// User queries (no restrictions by default)
$allUsers = User::all();
$adminUsers = User::admins()->get();
$verifiedUsers = User::verified()->get();
```

### Admin Panel - View All Content
```php
// Admin wants to see all prompts regardless of status/visibility
$allPrompts = Prompt::withAll()->get();

// Admin wants to see all drafts (public, private, unlisted)
$allDrafts = Prompt::withAll()->draft()->get();

// Admin wants to see all private prompts (published and draft)
$privatePrompts = Prompt::withAll()->private()->get();

// Admin wants to see all records including unapproved
$allCategories = Category::withUnapproved()->get();
$allProviders = Provider::withUnapproved()->get();
$allComments = Comment::withUnapproved()->get();
```

### Author Dashboard - View Own Content
```php
// Author sees their own drafts (public visibility only)
$myDrafts = Prompt::withDrafts()->draft()->where('user_id', auth()->id())->get();

// Author sees their own private published prompts
$myPrivatePrompts = Prompt::withPrivate()->private()->where('user_id', auth()->id())->get();

// Author sees ALL their own prompts
$myAllPrompts = Prompt::withAll()->where('user_id', auth()->id())->get();

// Author sees their own categories (including unapproved)
$myCategories = Category::withUnapproved()->where('user_id', auth()->id())->get();

// Author sees their own platforms (including unapproved)
$myPlatforms = Platform::withUnapproved()->where('user_id', auth()->id())->get();

// Author sees their own providers (including unapproved)
$myProviders = Provider::withUnapproved()->where('user_id', auth()->id())->get();

// Author sees their own AI models (including unapproved)
$myAiModels = AiModel::withUnapproved()->where('user_id', auth()->id())->get();
```

### Moderator Panel - Review Content
```php
// Moderator sees all published content (public, private, unlisted)
$publishedContent = Prompt::withPrivate()->get();

// Moderator sees all draft content for review
$draftsForReview = Prompt::withAll()->draft()->get();

// Moderator sees unapproved content for review
$unapprovedCategories = Category::unapproved()->get();
$unapprovedProviders = Provider::unapproved()->get();
$unapprovedPlatforms = Platform::unapproved()->get();
$unapprovedComments = Comment::unapproved()->get();
$unapprovedAiModels = AiModel::unapproved()->get();

// Moderator sees all users for management
$allUsers = User::all();
$unverifiedUsers = User::unverified()->get();
```

## Combining Scopes
```php
// Featured public published prompts (default behavior)
Prompt::featured()->get();

// Featured private published prompts
Prompt::withPrivate()->private()->featured()->get();

// Featured draft prompts (any visibility)
Prompt::withAll()->draft()->featured()->get();

// Private unlisted published prompts
Prompt::withPrivate()->where('visibility', 'unlisted')->get();

// Unapproved providers from specific user
Provider::withUnapproved()->unapproved()->where('user_id', 1)->get();

// Admin users with verified emails
User::admins()->verified()->get();

// Regular users with unverified emails
User::regular()->unverified()->get();
```

## Field Values
- **Prompt Status**: `published`, `draft`
- **Prompt Visibility**: `public`, `private`, `unlisted`  
- **Approval Status**: `is_approved` (boolean) - used by Comment, Category, Platform, Provider, AiModel
- **User Admin Status**: `is_admin` (boolean) - used by User model
- **Email Verification**: `email_verified_at` (datetime/null) - used by User model

## Models Summary

### Models with Global Scopes:
1. **Prompt**: PublishedScope + VisibilityScope (published + public only by default)
2. **Comment**: ApprovedScope (approved only by default)
3. **Category**: ApprovedScope (approved only by default)
4. **Platform**: ApprovedScope (approved only by default)
5. **Provider**: ApprovedScope (approved only by default)
6. **AiModel**: ApprovedScope (approved only by default)

### Models without Global Scopes:
1. **User**: No global scopes (all users accessible by default)
2. **Like**: Simple model with no scopes needed
