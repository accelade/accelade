<?php

declare(strict_types=1);

use Accelade\Notification\Notification;
use Accelade\Notification\NotificationManager;

test('manager starts with empty collection', function () {
    $manager = new NotificationManager;

    expect($manager->all())->toBeEmpty();
});

test('manager can create notification via make', function () {
    $manager = new NotificationManager;

    $notification = $manager->make();

    expect($notification)->toBeInstanceOf(Notification::class);
});

test('manager can create success notification', function () {
    $manager = new NotificationManager;

    $notification = $manager->success('Success Title');

    expect($notification)->toBeInstanceOf(Notification::class);
    expect($notification->status)->toBe('success');
    expect($notification->title)->toBe('Success Title');
});

test('manager can create info notification', function () {
    $manager = new NotificationManager;

    $notification = $manager->info('Info Title');

    expect($notification->status)->toBe('info');
    expect($notification->title)->toBe('Info Title');
});

test('manager can create warning notification', function () {
    $manager = new NotificationManager;

    $notification = $manager->warning('Warning Title');

    expect($notification->status)->toBe('warning');
    expect($notification->title)->toBe('Warning Title');
});

test('manager can create danger notification', function () {
    $manager = new NotificationManager;

    $notification = $manager->danger('Danger Title');

    expect($notification->status)->toBe('danger');
    expect($notification->title)->toBe('Danger Title');
});

test('manager adds notification to collection', function () {
    $manager = new NotificationManager;

    $manager->success('Test');

    expect($manager->all())->toHaveCount(1);
});

test('manager can push multiple notifications', function () {
    $manager = new NotificationManager;

    $manager->success('First');
    $manager->info('Second');
    $manager->warning('Third');

    expect($manager->all())->toHaveCount(3);
});

test('manager can flush notifications', function () {
    $manager = new NotificationManager;

    $manager->success('One');
    $manager->info('Two');

    $flushed = $manager->flush();

    expect($flushed)->toHaveCount(2);
    expect($manager->all())->toBeEmpty();
});

test('manager can close notification by id', function () {
    $manager = new NotificationManager;

    $notification = Notification::make('Test')->success();
    $id = $notification->getId();

    // Manually push the notification (setManager + destruct relies on variable going out of scope)
    $manager->push($notification);

    expect($manager->all())->toHaveCount(1);

    $manager->close($id);

    expect($manager->all())->toBeEmpty();
});

test('manager can convert to array', function () {
    $manager = new NotificationManager;

    $manager->success('Test');

    $array = $manager->toArray();

    expect($array)->toBeArray();
    expect($array[0])->toBeInstanceOf(Notification::class);
    expect($array[0]->title)->toBe('Test');
});

test('manager can push custom notification', function () {
    $manager = new NotificationManager;

    $notification = Notification::make('Custom')
        ->danger()
        ->position('bottom-center');

    $manager->push($notification);

    expect($manager->all()->first())->toBe($notification);
});

test('manager applies default callback to new notifications', function () {
    $manager = new NotificationManager;

    $manager->setDefault(function (Notification $n) {
        $n->position('bottom-right');
    });

    $notification = $manager->make();

    expect($notification->position)->toBe('bottom-right');
});

test('manager can set default position', function () {
    $manager = new NotificationManager;

    $manager->defaultPosition('top-center');

    $notification = $manager->make();

    expect($notification->position)->toBe('top-center');
});

test('manager can set default duration', function () {
    $manager = new NotificationManager;

    $manager->defaultDuration(10000);

    $notification = $manager->make();

    expect($notification->duration)->toBe(10000);
});

test('manager flush returns collection', function () {
    $manager = new NotificationManager;

    $manager->info('Test');

    $result = $manager->flush();

    expect($result)->toBeInstanceOf(\Illuminate\Support\Collection::class);
});

test('manager title method returns notification', function () {
    $manager = new NotificationManager;

    $notification = $manager->title('Custom Title');

    expect($notification)->toBeInstanceOf(Notification::class);
    expect($notification->title)->toBe('Custom Title');
});
