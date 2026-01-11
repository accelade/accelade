<?php

declare(strict_types=1);

namespace Accelade\Http\Controllers;

use Accelade\Notification\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class NotifyDemoController extends Controller
{
    public function show(Request $request, string $type): RedirectResponse
    {
        $manager = app('accelade.notify');

        match ($type) {
            'success' => $manager->success('Saved Successfully!')
                ->body('Your changes have been saved to the database.'),

            'info' => $manager->info('Did you know?')
                ->body('You can customize notifications with CSS variables.'),

            'warning' => $manager->warning('Please Review')
                ->body('Some fields may need your attention before proceeding.'),

            'danger' => $manager->danger('Action Failed')
                ->body('Unable to complete the request. Please try again.'),

            'persistent' => Notification::make()
                ->title('Important Notice')
                ->body('This notification will stay until you dismiss it.')
                ->info()
                ->persistent()
                ->send(),

            'actions' => Notification::make()
                ->title('New Message')
                ->body('You have received a new message from John.')
                ->info()
                ->actions([
                    ['name' => 'view', 'label' => 'View Message', 'url' => '#'],
                    ['name' => 'dismiss', 'label' => 'Dismiss', 'close' => true],
                ])
                ->send(),

            'custom' => Notification::make()
                ->title('Custom Styled')
                ->body('This uses custom icon and colors.')
                ->status('success')
                ->icon('<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg>')
                ->seconds(8)
                ->send(),

            default => $manager->info('Unknown Type')
                ->body("Type '{$type}' is not recognized."),
        };

        return redirect()->back();
    }
}
