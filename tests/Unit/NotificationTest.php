<?php

declare(strict_types=1);

use Accelade\Notification\Notification;

test('notification can be created with title', function () {
    $notification = new Notification('Test Title');

    expect($notification->title)->toBe('Test Title');
    expect($notification->id)->toStartWith('notif-');
});

test('notification can be created without title', function () {
    $notification = Notification::make();

    expect($notification->title)->toBe('');
});

test('notification has default values', function () {
    $notification = Notification::make();

    expect($notification->body)->toBe('');
    expect($notification->status)->toBe('success');
    expect($notification->position)->toBe('top-right');
    expect($notification->duration)->toBe(5000);
    expect($notification->persistent)->toBeFalse();
    expect($notification->icon)->toBe('');
    expect($notification->actions)->toBe([]);
});

test('notification can set title', function () {
    $notification = Notification::make()->title('Hello');

    expect($notification->title)->toBe('Hello');
});

test('notification can set body', function () {
    $notification = Notification::make('Title')->body('Hello World');

    expect($notification->body)->toBe('Hello World');
});

test('notification message is alias for body', function () {
    $notification = Notification::make('Title')->message('Alias Test');

    expect($notification->body)->toBe('Alias Test');
});

test('notification can set status to success', function () {
    $notification = Notification::make('Title')->success();

    expect($notification->status)->toBe('success');
});

test('notification can set status to info', function () {
    $notification = Notification::make('Title')->info();

    expect($notification->status)->toBe('info');
});

test('notification can set status to warning', function () {
    $notification = Notification::make('Title')->warning();

    expect($notification->status)->toBe('warning');
});

test('notification can set status to danger', function () {
    $notification = Notification::make('Title')->danger();

    expect($notification->status)->toBe('danger');
});

test('notification can set custom status', function () {
    $notification = Notification::make('Title')->status('custom');

    expect($notification->status)->toBe('custom');
});

test('notification can set icon', function () {
    $notification = Notification::make('Title')->icon('heroicon-o-check');

    expect($notification->icon)->toBe('heroicon-o-check');
});

test('notification can set icon color', function () {
    $notification = Notification::make('Title')->iconColor('#ff0000');

    expect($notification->iconColor)->toBe('#ff0000');
});

test('notification can set color', function () {
    $notification = Notification::make('Title')->color('success');

    expect($notification->color)->toBe('success');
});

test('notification can set position', function () {
    $notification = Notification::make('Title')->position('bottom-left');

    expect($notification->position)->toBe('bottom-left');
});

test('notification can set duration in milliseconds', function () {
    $notification = Notification::make('Title')->duration(3000);

    expect($notification->duration)->toBe(3000);
});

test('notification can set duration in seconds', function () {
    $notification = Notification::make('Title')->seconds(5);

    expect($notification->duration)->toBe(5000);
});

test('notification autoDismiss is alias for seconds', function () {
    $notification = Notification::make('Title')->autoDismiss(10);

    expect($notification->duration)->toBe(10000);
});

test('notification can be persistent', function () {
    $notification = Notification::make('Title')->persistent();

    expect($notification->persistent)->toBeTrue();
    expect($notification->duration)->toBe(0);
});

test('notification can set actions', function () {
    $notification = Notification::make('Title')->actions([
        ['name' => 'view', 'url' => '/posts/1'],
    ]);

    expect($notification->actions)->toHaveCount(1);
    expect($notification->actions[0]['name'])->toBe('view');
});

test('notification supports fluent chaining', function () {
    $notification = Notification::make()
        ->title('Title')
        ->body('Body')
        ->warning()
        ->icon('heroicon-o-bell')
        ->position('top-center')
        ->seconds(3);

    expect($notification->title)->toBe('Title');
    expect($notification->body)->toBe('Body');
    expect($notification->status)->toBe('warning');
    expect($notification->icon)->toBe('heroicon-o-bell');
    expect($notification->position)->toBe('top-center');
    expect($notification->duration)->toBe(3000);
});

test('notification serializes to json correctly', function () {
    $notification = Notification::make('Test')
        ->body('Body')
        ->info()
        ->position('bottom-right');

    $json = $notification->jsonSerialize();

    expect($json)->toBeArray();
    expect($json['title'])->toBe('Test');
    expect($json['body'])->toBe('Body');
    expect($json['status'])->toBe('info');
    expect($json['position'])->toBe('bottom-right');
    expect($json['id'])->toStartWith('notif-');
});

test('notification can get id', function () {
    $notification = Notification::make('Test');

    expect($notification->getId())->toBe($notification->id);
});

test('notification can be json encoded', function () {
    $notification = Notification::make('Test');

    $encoded = json_encode($notification);

    expect($encoded)->toBeString();
    expect(json_decode($encoded, true)['title'])->toBe('Test');
});
