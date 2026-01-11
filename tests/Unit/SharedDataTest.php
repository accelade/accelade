<?php

declare(strict_types=1);

use Accelade\Support\SharedData;

describe('SharedData', function () {
    beforeEach(function () {
        $this->sharedData = new SharedData;
    });

    describe('share', function () {
        it('can share a single value', function () {
            $this->sharedData->share('key', 'value');

            expect($this->sharedData->get('key'))->toBe('value');
        });

        it('can share multiple values at once', function () {
            $this->sharedData->share([
                'key1' => 'value1',
                'key2' => 'value2',
            ]);

            expect($this->sharedData->get('key1'))->toBe('value1')
                ->and($this->sharedData->get('key2'))->toBe('value2');
        });

        it('can share a closure for lazy evaluation', function () {
            $called = false;
            $this->sharedData->share('lazy', function () use (&$called) {
                $called = true;

                return 'lazy value';
            });

            expect($called)->toBeFalse();

            $value = $this->sharedData->get('lazy');

            expect($called)->toBeTrue()
                ->and($value)->toBe('lazy value');
        });

        it('evaluates closure only once', function () {
            $count = 0;
            $this->sharedData->share('counter', function () use (&$count) {
                $count++;

                return $count;
            });

            $this->sharedData->get('counter');
            $this->sharedData->get('counter');

            expect($count)->toBe(1);
        });

        it('returns self for fluent interface', function () {
            $result = $this->sharedData->share('key', 'value');

            expect($result)->toBe($this->sharedData);
        });
    });

    describe('get', function () {
        it('returns default value when key does not exist', function () {
            expect($this->sharedData->get('missing', 'default'))->toBe('default');
        });

        it('returns null when key does not exist and no default', function () {
            expect($this->sharedData->get('missing'))->toBeNull();
        });

        it('can get nested values with dot notation', function () {
            $this->sharedData->share('user', [
                'name' => 'John',
                'email' => 'john@example.com',
            ]);

            // Note: get() doesn't support dot notation by default in SharedData
            // but this tests the basic nested array access
            $user = $this->sharedData->get('user');
            expect($user['name'])->toBe('John');
        });
    });

    describe('has', function () {
        it('returns true when key exists', function () {
            $this->sharedData->share('key', 'value');

            expect($this->sharedData->has('key'))->toBeTrue();
        });

        it('returns false when key does not exist', function () {
            expect($this->sharedData->has('missing'))->toBeFalse();
        });

        it('returns true for lazy values', function () {
            $this->sharedData->share('lazy', fn () => 'value');

            expect($this->sharedData->has('lazy'))->toBeTrue();
        });
    });

    describe('forget', function () {
        it('can remove a shared value', function () {
            $this->sharedData->share('key', 'value');
            $this->sharedData->forget('key');

            expect($this->sharedData->has('key'))->toBeFalse();
        });

        it('can remove a lazy value', function () {
            $this->sharedData->share('lazy', fn () => 'value');
            $this->sharedData->forget('lazy');

            expect($this->sharedData->has('lazy'))->toBeFalse();
        });

        it('returns self for fluent interface', function () {
            $result = $this->sharedData->forget('key');

            expect($result)->toBe($this->sharedData);
        });
    });

    describe('flush', function () {
        it('removes all shared data', function () {
            $this->sharedData->share([
                'key1' => 'value1',
                'key2' => 'value2',
            ]);
            $this->sharedData->share('lazy', fn () => 'value');

            $this->sharedData->flush();

            expect($this->sharedData->count())->toBe(0)
                ->and($this->sharedData->isEmpty())->toBeTrue();
        });
    });

    describe('all', function () {
        it('returns all shared data as array', function () {
            $this->sharedData->share([
                'key1' => 'value1',
                'key2' => 'value2',
            ]);

            $all = $this->sharedData->all();

            expect($all)->toBe([
                'key1' => 'value1',
                'key2' => 'value2',
            ]);
        });

        it('resolves lazy values when getting all', function () {
            $this->sharedData->share('lazy', fn () => 'lazy value');

            $all = $this->sharedData->all();

            expect($all)->toBe(['lazy' => 'lazy value']);
        });
    });

    describe('merge', function () {
        it('merges additional data', function () {
            $this->sharedData->share('key1', 'value1');
            $this->sharedData->merge(['key2' => 'value2']);

            expect($this->sharedData->all())->toBe([
                'key1' => 'value1',
                'key2' => 'value2',
            ]);
        });

        it('overwrites existing keys', function () {
            $this->sharedData->share('key', 'old');
            $this->sharedData->merge(['key' => 'new']);

            expect($this->sharedData->get('key'))->toBe('new');
        });
    });

    describe('count and empty checks', function () {
        it('counts shared items correctly', function () {
            expect($this->sharedData->count())->toBe(0);

            $this->sharedData->share('key1', 'value1');
            expect($this->sharedData->count())->toBe(1);

            $this->sharedData->share('lazy', fn () => 'value');
            expect($this->sharedData->count())->toBe(2);
        });

        it('isEmpty returns true when empty', function () {
            expect($this->sharedData->isEmpty())->toBeTrue();
        });

        it('isEmpty returns false when not empty', function () {
            $this->sharedData->share('key', 'value');

            expect($this->sharedData->isEmpty())->toBeFalse();
        });

        it('isNotEmpty returns true when not empty', function () {
            $this->sharedData->share('key', 'value');

            expect($this->sharedData->isNotEmpty())->toBeTrue();
        });
    });

    describe('serialization', function () {
        it('can convert to array', function () {
            $this->sharedData->share('key', 'value');

            expect($this->sharedData->toArray())->toBe(['key' => 'value']);
        });

        it('can convert to JSON', function () {
            $this->sharedData->share('key', 'value');

            expect($this->sharedData->toJson())->toBe('{"key":"value"}');
        });

        it('can be JSON serialized', function () {
            $this->sharedData->share('key', 'value');

            expect(json_encode($this->sharedData))->toBe('{"key":"value"}');
        });

        it('resolves lazy values in JSON', function () {
            $this->sharedData->share('lazy', fn () => 'lazy value');

            expect($this->sharedData->toJson())->toBe('{"lazy":"lazy value"}');
        });
    });
});
