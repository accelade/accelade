# Notifications

Accelade provides a Filament-inspired notification system for displaying toast messages from PHP or JavaScript.

## Setup

Add the notifications container to your layout:

```blade
<body>
    {{ $slot }}

    @acceladeScripts
    @acceladeNotifications
</body>
```

## PHP Notifications

### Using the Facade

```php
use Accelade\Facades\Notify;

// Quick notifications
Notify::success('Success!')->body('Your changes have been saved.');
Notify::info('Info')->body('Here is some information.');
Notify::warning('Warning')->body('Please review your input.');
Notify::danger('Error')->body('Something went wrong.');
```

### Using the Notification Class

```php
use Accelade\Notification\Notification;

Notification::make()
    ->title('Welcome!')
    ->body('Thanks for signing up.')
    ->success()
    ->send();
```

### In Controllers

```php
class UserController extends Controller
{
    public function store(Request $request)
    {
        // Create user...

        Notify::success('User Created')
            ->body('The user account has been created successfully.');

        return redirect()->route('users.index');
    }

    public function destroy(User $user)
    {
        $user->delete();

        Notify::danger('User Deleted')
            ->body("User {$user->name} has been removed.");

        return redirect()->route('users.index');
    }
}
```

## Notification Methods

### Basic Properties

```php
Notification::make()
    ->title('Title')           // Main title
    ->body('Body text')        // Description/body
    ->status('success')        // Status type
    ->send();
```

### Status Types

```php
->success()    // Green - positive actions
->info()       // Blue - informational
->warning()    // Yellow/Orange - warnings
->danger()     // Red - errors, destructive actions
```

### Custom Icon

```php
Notification::make()
    ->title('Custom Icon')
    ->icon('<svg>...</svg>')
    ->iconColor('#ff0000')
    ->send();
```

### Duration

```php
// Duration in milliseconds
->duration(5000)   // 5 seconds

// Duration in seconds
->seconds(10)      // 10 seconds

// Auto-dismiss helper
->autoDismiss(8)   // 8 seconds
```

### Persistent Notifications

```php
// Won't auto-dismiss, requires manual close
Notification::make()
    ->title('Important')
    ->body('This requires your attention.')
    ->persistent()
    ->send();
```

### Position

```php
->position('top-right')      // Default
->position('top-left')
->position('top-center')
->position('bottom-right')
->position('bottom-left')
->position('bottom-center')
```

### Actions

Add buttons to notifications:

```php
Notification::make()
    ->title('New Message')
    ->body('You have a new message from John.')
    ->info()
    ->actions([
        [
            'name' => 'view',
            'label' => 'View Message',
            'url' => '/messages/1',
        ],
        [
            'name' => 'dismiss',
            'label' => 'Dismiss',
            'close' => true,
        ],
    ])
    ->send();
```

Action properties:

| Property | Type | Description |
|----------|------|-------------|
| `name` | string | Action identifier |
| `label` | string | Button text |
| `url` | string | Link URL |
| `openInNewTab` | bool | Open URL in new tab |
| `close` | bool | Close notification on click |
| `dispatch` | string | Dispatch browser event |

## JavaScript Notifications

### Quick Methods

```javascript
window.Accelade.notify.success('Success!', 'Operation completed.');
window.Accelade.notify.info('Info', 'Here is some information.');
window.Accelade.notify.warning('Warning', 'Please be careful.');
window.Accelade.notify.danger('Error', 'Something went wrong.');
```

### Full Options

```javascript
window.Accelade.notify.show({
    id: 'unique-id',          // Optional, auto-generated if not provided
    title: 'Notification',
    body: 'This is the body text.',
    status: 'success',         // success, info, warning, danger
    position: 'top-right',
    duration: 5000,           // milliseconds
    persistent: false,
    actions: [
        { label: 'View', url: '/view' },
        { label: 'Close', close: true }
    ]
});
```

### Closing Notifications

```javascript
// Close by ID
window.Accelade.notify.close('notification-id');

// Close all
window.Accelade.notify.closeAll();
```

## Customizing Appearance

### CSS Variables

Override these CSS variables to customize appearance:

```css
:root {
    /* Container */
    --accelade-notif-width: 24rem;
    --accelade-notif-radius: 0.75rem;
    --accelade-notif-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1);
    --accelade-notif-bg: #fff;

    /* Success */
    --accelade-notif-success-icon: #10b981;
    --accelade-notif-success-bg: #ecfdf5;
    --accelade-notif-success-border: #a7f3d0;

    /* Info */
    --accelade-notif-info-icon: #3b82f6;
    --accelade-notif-info-bg: #eff6ff;
    --accelade-notif-info-border: #bfdbfe;

    /* Warning */
    --accelade-notif-warning-icon: #f59e0b;
    --accelade-notif-warning-bg: #fffbeb;
    --accelade-notif-warning-border: #fde68a;

    /* Danger */
    --accelade-notif-danger-icon: #ef4444;
    --accelade-notif-danger-bg: #fef2f2;
    --accelade-notif-danger-border: #fecaca;

    /* Animation */
    --accelade-notif-animation-duration: 0.3s;
}
```

### Dark Mode

```css
.dark {
    --accelade-notif-bg: #1f2937;
    --accelade-notif-success-bg: #064e3b;
    --accelade-notif-info-bg: #1e3a5f;
    --accelade-notif-warning-bg: #78350f;
    --accelade-notif-danger-bg: #7f1d1d;
}
```

## Manager Configuration

### Default Position

```php
use Accelade\Facades\Notify;

Notify::defaultPosition('bottom-right');

// All subsequent notifications use this position
Notify::success('Saved!');
```

### Default Duration

```php
Notify::defaultDuration(8000); // 8 seconds

Notify::info('This shows for 8 seconds');
```

## Backend Notification Demo

Visit `/demo/notify/{type}` to test backend notifications:

| URL | Description |
|-----|-------------|
| `/demo/notify/success` | Success notification |
| `/demo/notify/info` | Info notification |
| `/demo/notify/warning` | Warning notification |
| `/demo/notify/danger` | Danger notification |
| `/demo/notify/persistent` | Persistent (no auto-dismiss) |
| `/demo/notify/actions` | With action buttons |
| `/demo/notify/custom` | Custom icon |

## Complete Example

```php
// In a controller
public function processPayment(Request $request)
{
    try {
        $payment = Payment::process($request->all());

        Notification::make()
            ->title('Payment Successful')
            ->body("Transaction #{$payment->id} completed.")
            ->success()
            ->actions([
                [
                    'label' => 'View Receipt',
                    'url' => route('payments.receipt', $payment),
                ],
            ])
            ->seconds(10)
            ->send();

        return redirect()->route('dashboard');

    } catch (PaymentException $e) {
        Notification::make()
            ->title('Payment Failed')
            ->body($e->getMessage())
            ->danger()
            ->persistent()
            ->actions([
                ['label' => 'Try Again', 'url' => route('payments.create')],
                ['label' => 'Contact Support', 'url' => route('support')],
            ])
            ->send();

        return back();
    }
}
```

## Next Steps

- [SPA Navigation](spa-navigation.md) - Client-side routing
- [Components](components.md) - Reactive components
- [API Reference](api-reference.md) - Complete API
