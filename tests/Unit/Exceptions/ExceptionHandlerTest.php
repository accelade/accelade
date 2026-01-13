<?php

declare(strict_types=1);

use Accelade\Exceptions\ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

describe('ExceptionHandler', function (): void {
    describe('isAcceladeRequest', function (): void {
        it('returns false for non-AJAX requests', function (): void {
            $request = Request::create('/test', 'GET');

            expect(ExceptionHandler::isAcceladeRequest($request))->toBeFalse();
        });

        it('returns true for accelade route prefix', function (): void {
            $request = Request::create('/accelade/bridge/call', 'POST');
            $request->headers->set('X-Requested-With', 'XMLHttpRequest');

            expect(ExceptionHandler::isAcceladeRequest($request))->toBeTrue();
        });

        it('returns true for _accelade route prefix', function (): void {
            $request = Request::create('/_accelade/update', 'POST');
            $request->headers->set('X-Requested-With', 'XMLHttpRequest');

            expect(ExceptionHandler::isAcceladeRequest($request))->toBeTrue();
        });

        it('returns true for X-Accelade header', function (): void {
            $request = Request::create('/any/route', 'POST');
            $request->headers->set('X-Requested-With', 'XMLHttpRequest');
            $request->headers->set('X-Accelade', 'true');

            expect(ExceptionHandler::isAcceladeRequest($request))->toBeTrue();
        });

        it('returns true for Accept header with accelade', function (): void {
            $request = Request::create('/any/route', 'POST');
            $request->headers->set('X-Requested-With', 'XMLHttpRequest');
            $request->headers->set('Accept', 'application/json, accelade');

            expect(ExceptionHandler::isAcceladeRequest($request))->toBeTrue();
        });
    });

    describe('handle', function (): void {
        it('creates a closure that handles exceptions', function (): void {
            $handler = Mockery::mock(\Illuminate\Foundation\Exceptions\Handler::class);

            $closure = ExceptionHandler::handle($handler);

            expect($closure)->toBeInstanceOf(Closure::class);
        });

        it('returns null for non-Accelade requests', function (): void {
            $handler = Mockery::mock(\Illuminate\Foundation\Exceptions\Handler::class);
            $request = Request::create('/test', 'GET');
            $exception = new \Exception('Test error');

            $closure = ExceptionHandler::handle($handler);
            $result = $closure($exception, $request);

            expect($result)->toBeNull();
        });

        it('handles validation exceptions for Accelade requests', function (): void {
            $handler = Mockery::mock(\Illuminate\Foundation\Exceptions\Handler::class);
            $request = Request::create('/accelade/bridge/call', 'POST');
            $request->headers->set('X-Requested-With', 'XMLHttpRequest');

            // Create a real validator to generate a ValidationException
            $validator = \Illuminate\Support\Facades\Validator::make(
                ['email' => 'invalid'],
                ['email' => 'email']
            );

            $exception = new ValidationException($validator);

            $closure = ExceptionHandler::handle($handler);
            $result = $closure($exception, $request);

            expect($result)->toBeInstanceOf(\Illuminate\Http\JsonResponse::class);
            expect($result->getStatusCode())->toBe(422);

            $data = $result->getData(true);
            expect($data['success'])->toBeFalse();
            expect($data['_accelade']['type'])->toBe('validation');
            expect($data['errors'])->toBeArray();
        });

        it('handles HTTP exceptions for Accelade requests', function (): void {
            $handler = Mockery::mock(\Illuminate\Foundation\Exceptions\Handler::class);
            $request = Request::create('/accelade/bridge/call', 'POST');
            $request->headers->set('X-Requested-With', 'XMLHttpRequest');

            $exception = new NotFoundHttpException('Resource not found');

            $closure = ExceptionHandler::handle($handler);
            $result = $closure($exception, $request);

            expect($result)->toBeInstanceOf(\Illuminate\Http\JsonResponse::class);
            expect($result->getStatusCode())->toBe(404);

            $data = $result->getData(true);
            expect($data['success'])->toBeFalse();
            expect($data['_accelade']['type'])->toBe('http');
            expect($data['_accelade']['status'])->toBe(404);
        });

        it('handles 419 CSRF token mismatch with refresh action', function (): void {
            $handler = Mockery::mock(\Illuminate\Foundation\Exceptions\Handler::class);
            $request = Request::create('/accelade/bridge/call', 'POST');
            $request->headers->set('X-Requested-With', 'XMLHttpRequest');

            $exception = new HttpException(419, 'Page Expired');

            $closure = ExceptionHandler::handle($handler);
            $result = $closure($exception, $request);

            expect($result)->toBeInstanceOf(\Illuminate\Http\JsonResponse::class);
            expect($result->getStatusCode())->toBe(419);

            $data = $result->getData(true);
            expect($data['_accelade']['action'])->toBe('refresh');
        });

        it('handles generic exceptions for Accelade requests', function (): void {
            $handler = Mockery::mock(\Illuminate\Foundation\Exceptions\Handler::class);
            $request = Request::create('/accelade/bridge/call', 'POST');
            $request->headers->set('X-Requested-With', 'XMLHttpRequest');

            $exception = new \RuntimeException('Something went wrong');

            $closure = ExceptionHandler::handle($handler);
            $result = $closure($exception, $request);

            expect($result)->toBeInstanceOf(\Illuminate\Http\JsonResponse::class);
            expect($result->getStatusCode())->toBe(500);

            $data = $result->getData(true);
            expect($data['success'])->toBeFalse();
            expect($data['_accelade']['type'])->toBe('exception');
        });

        it('allows custom handler to process exceptions first', function (): void {
            $handler = Mockery::mock(\Illuminate\Foundation\Exceptions\Handler::class);
            $request = Request::create('/accelade/bridge/call', 'POST');
            $request->headers->set('X-Requested-With', 'XMLHttpRequest');

            $exception = new \RuntimeException('Custom error');
            $customResponse = response()->json(['custom' => true], 418);

            $customHandler = function ($e, $req) use ($customResponse) {
                return $customResponse;
            };

            $closure = ExceptionHandler::handle($handler, $customHandler);
            $result = $closure($exception, $request);

            expect($result)->toBe($customResponse);
        });

        it('falls through to default handler if custom handler returns null', function (): void {
            $handler = Mockery::mock(\Illuminate\Foundation\Exceptions\Handler::class);
            $request = Request::create('/accelade/bridge/call', 'POST');
            $request->headers->set('X-Requested-With', 'XMLHttpRequest');

            $exception = new \RuntimeException('Test error');

            $customHandler = function ($e, $req) {
                return null;
            };

            $closure = ExceptionHandler::handle($handler, $customHandler);
            $result = $closure($exception, $request);

            expect($result)->toBeInstanceOf(\Illuminate\Http\JsonResponse::class);
            expect($result->getStatusCode())->toBe(500);
        });
    });

    describe('debug mode', function (): void {
        it('includes debug info when APP_DEBUG is true', function (): void {
            config(['app.debug' => true]);

            $handler = Mockery::mock(\Illuminate\Foundation\Exceptions\Handler::class);
            $request = Request::create('/accelade/bridge/call', 'POST');
            $request->headers->set('X-Requested-With', 'XMLHttpRequest');

            $exception = new \RuntimeException('Debug test error');

            $closure = ExceptionHandler::handle($handler);
            $result = $closure($exception, $request);

            $data = $result->getData(true);
            expect($data['_accelade']['debug'])->toBeArray();
            expect($data['_accelade']['debug']['exception'])->toBe('RuntimeException');
            expect($data['_accelade']['debug']['message'])->toBe('Debug test error');
        });

        it('excludes debug info when APP_DEBUG is false', function (): void {
            config(['app.debug' => false]);

            $handler = Mockery::mock(\Illuminate\Foundation\Exceptions\Handler::class);
            $request = Request::create('/accelade/bridge/call', 'POST');
            $request->headers->set('X-Requested-With', 'XMLHttpRequest');

            $exception = new \RuntimeException('Secret error');

            $closure = ExceptionHandler::handle($handler);
            $result = $closure($exception, $request);

            $data = $result->getData(true);
            expect($data['_accelade']['debug'] ?? null)->toBeNull();
        });
    });
});
