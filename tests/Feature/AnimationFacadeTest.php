<?php

declare(strict_types=1);

use Accelade\Animation\AnimationPhase;
use Accelade\Animation\AnimationPreset;
use Accelade\Facades\Animation;

beforeEach(function () {
    // Reset animation manager between tests
    app()->forgetInstance('accelade.animation');
});

it('animation facade is registered', function () {
    expect(app('accelade.animation'))
        ->toBeInstanceOf(\Accelade\Animation\AnimationManager::class);
});

it('animation singleton returns same instance', function () {
    $instance1 = app('accelade.animation');
    $instance2 = app('accelade.animation');

    expect($instance1)->toBe($instance2);
});

it('animation facade has built-in presets', function () {
    expect(Animation::has('default'))->toBeTrue();
    expect(Animation::has('opacity'))->toBeTrue();
    expect(Animation::has('fade'))->toBeTrue();
    expect(Animation::has('slide-left'))->toBeTrue();
    expect(Animation::has('slide-right'))->toBeTrue();
    expect(Animation::has('slide-up'))->toBeTrue();
    expect(Animation::has('slide-down'))->toBeTrue();
    expect(Animation::has('scale'))->toBeTrue();
});

it('animation facade can get preset', function () {
    $preset = Animation::get('fade');

    expect($preset)
        ->toBeInstanceOf(AnimationPreset::class)
        ->and($preset->name)->toBe('fade')
        ->and($preset->enter->transition)->toBe('transition-opacity ease-out duration-200')
        ->and($preset->enter->from)->toBe('opacity-0')
        ->and($preset->enter->to)->toBe('opacity-100')
        ->and($preset->leave->transition)->toBe('transition-opacity ease-in duration-150')
        ->and($preset->leave->from)->toBe('opacity-100')
        ->and($preset->leave->to)->toBe('opacity-0');
});

it('animation facade returns null for unknown preset', function () {
    expect(Animation::get('nonexistent'))->toBeNull();
});

it('animation facade can register custom preset', function () {
    Animation::register(
        name: 'custom',
        enter: new AnimationPhase('custom-enter', 'custom-from', 'custom-to'),
        leave: new AnimationPhase('custom-leave', 'custom-leave-from', 'custom-leave-to'),
    );

    expect(Animation::has('custom'))->toBeTrue();

    $preset = Animation::get('custom');
    expect($preset->name)->toBe('custom');
    expect($preset->enter->transition)->toBe('custom-enter');
});

it('animation facade can get all presets', function () {
    $all = Animation::all();

    expect($all)
        ->toBeArray()
        ->toHaveKey('default')
        ->toHaveKey('fade')
        ->toHaveKey('slide-left');
});

it('animation facade can convert to array', function () {
    $array = Animation::toArray();

    expect($array)
        ->toBeArray()
        ->toHaveKey('default')
        ->and($array['default'])->toHaveKey('name')
        ->and($array['default'])->toHaveKey('enter')
        ->and($array['default'])->toHaveKey('enterFrom')
        ->and($array['default'])->toHaveKey('enterTo')
        ->and($array['default'])->toHaveKey('leave')
        ->and($array['default'])->toHaveKey('leaveFrom')
        ->and($array['default'])->toHaveKey('leaveTo');
});

it('preset can convert to array', function () {
    $preset = Animation::get('default');
    $array = $preset->toArray();

    expect($array)
        ->toBeArray()
        ->toHaveKey('name')
        ->toHaveKey('enter')
        ->toHaveKey('enterFrom')
        ->toHaveKey('enterTo')
        ->toHaveKey('leave')
        ->toHaveKey('leaveFrom')
        ->toHaveKey('leaveTo');
});

it('animation facade supports fluent interface', function () {
    $result = Animation::register(
        name: 'fluent-test',
        enter: new AnimationPhase('enter', 'from', 'to'),
        leave: new AnimationPhase('leave', 'leave-from', 'leave-to'),
    );

    expect($result)->toBeInstanceOf(\Accelade\Animation\AnimationManager::class);
});
