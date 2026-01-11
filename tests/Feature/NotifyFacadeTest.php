<?php

declare(strict_types=1);

use Accelade\Facades\Notify;
use Accelade\Notification\Notification;

test('notify facade creates success notification', function () {
    $notification = Notify::success('Success!');

    expect($notification)->toBeInstanceOf(Notification::class);
    expect($notification->status)->toBe('success');
});

test('notify facade creates info notification', function () {
    $notification = Notify::info('Info!');

    expect($notification)->toBeInstanceOf(Notification::class);
    expect($notification->status)->toBe('info');
});

test('notify facade creates warning notification', function () {
    $notification = Notify::warning('Warning!');

    expect($notification)->toBeInstanceOf(Notification::class);
    expect($notification->status)->toBe('warning');
});

test('notify facade creates danger notification', function () {
    $notification = Notify::danger('Danger!');

    expect($notification)->toBeInstanceOf(Notification::class);
    expect($notification->status)->toBe('danger');
});

test('notify facade stores notifications', function () {
    Notify::success('First');
    Notify::info('Second');

    expect(Notify::all())->toHaveCount(2);
});

test('notify facade flushes notifications', function () {
    Notify::success('Test');

    $flushed = Notify::flush();

    expect($flushed)->toHaveCount(1);
    expect(Notify::all())->toBeEmpty();
});

test('notify facade make returns chainable notification', function () {
    $notification = Notify::make()
        ->title('Custom')
        ->body('Body')
        ->warning()
        ->position('bottom-left');

    expect($notification->title)->toBe('Custom');
    expect($notification->body)->toBe('Body');
    expect($notification->status)->toBe('warning');
    expect($notification->position)->toBe('bottom-left');
});

test('notify facade title returns chainable notification', function () {
    $notification = Notify::title('Title')
        ->body('Body')
        ->danger();

    expect($notification->title)->toBe('Title');
    expect($notification->body)->toBe('Body');
    expect($notification->status)->toBe('danger');
});

test('notify facade can set defaults', function () {
    Notify::defaultPosition('bottom-center');
    Notify::defaultDuration(10000);

    $notification = Notify::make();

    expect($notification->position)->toBe('bottom-center');
    expect($notification->duration)->toBe(10000);
});

test('accelade notify singleton is registered', function () {
    $manager = app('accelade.notify');

    expect($manager)->toBeInstanceOf(\Accelade\Notification\NotificationManager::class);
});

test('same notify instance is returned', function () {
    $first = app('accelade.notify');
    $second = app('accelade.notify');

    expect($first)->toBe($second);
});
