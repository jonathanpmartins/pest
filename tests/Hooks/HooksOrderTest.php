<?php

pest()->beforeEach(function () {
    $this->setupOrder = [];
    $this->teardownOrder = [];

    $this->setupOrder[] = 'global-beforeEach';
});

beforeEach(function () {
    $this->setupOrder[] = 'local-beforeEach';
});

afterEach(function () {
    $this->teardownOrder[] = 'local-afterEach';
});

pest()->afterEach(function () {
    $this->teardownOrder[] = 'global-afterEach';

    if (count($this->teardownOrder) === 2) {
        expect($this->setupOrder)->toBe([
            'global-beforeEach',
            'local-beforeEach',
        ]);
        expect($this->teardownOrder)->toBe([
            'local-afterEach',
            'global-afterEach',
        ]);
    } elseif (count($this->teardownOrder) === 3) {
        expect($this->setupOrder)->toBe([
            'global-beforeEach',
            'local-beforeEach',
            'nested-beforeEach-1',
        ]);
        expect($this->teardownOrder)->toBe([
            'nested-afterEach-1',
            'local-afterEach',
            'global-afterEach',
        ]);
    } elseif (count($this->teardownOrder) === 4) {
        expect($this->setupOrder)->toBe([
            'global-beforeEach',
            'local-beforeEach',
            'nested-beforeEach-1',
            'nested-beforeEach-2',
        ]);
        expect($this->teardownOrder)->toBe([
            'nested-afterEach-2',
            'nested-afterEach-1',
            'local-afterEach',
            'global-afterEach',
        ]);
    } elseif (count($this->teardownOrder) === 5) {
        expect($this->setupOrder)->toBe([
            'global-beforeEach',
            'local-beforeEach',
            'nested-beforeEach-1',
            'nested-beforeEach-2',
            'nested-beforeEach-3',
        ]);
        expect($this->teardownOrder)->toBe([
            'nested-afterEach-3',
            'nested-afterEach-2',
            'nested-afterEach-1',
            'local-afterEach',
            'global-afterEach',
        ]);
    } else {
        $this->fail('Unexpected teardown order');
    }
});

test('simple test', function () {
    expect($this->setupOrder)->toBe([
        'global-beforeEach',
        'local-beforeEach',
    ]);
    expect($this->teardownOrder)->toBe([]);
});

describe('nested 1', function () {
    beforeEach(function () {
        $this->setupOrder[] = 'nested-beforeEach-1';
    });

    afterEach(function () {
        $this->teardownOrder[] = 'nested-afterEach-1';
    });

    test('nested test', function () {
        expect($this->setupOrder)->toBe([
            'global-beforeEach',
            'local-beforeEach',
            'nested-beforeEach-1',
        ]);
        expect($this->teardownOrder)->toBe([]);
    });

    describe('nested 2', function () {
        beforeEach(function () {
            $this->setupOrder[] = 'nested-beforeEach-2';
        });

        afterEach(function () {
            $this->teardownOrder[] = 'nested-afterEach-2';
        });

        test('setup and teardown order should be reversed', function () {
            expect($this->setupOrder)->toBe([
                'global-beforeEach',
                'local-beforeEach',
                'nested-beforeEach-1',
                'nested-beforeEach-2',
            ]);
            expect($this->teardownOrder)->toBe([]);
        });

        describe('nested 3', function () {
            beforeEach(function () {
                $this->setupOrder[] = 'nested-beforeEach-3';
            });

            afterEach(function () {
                $this->teardownOrder[] = 'nested-afterEach-3';
            });

            test('setup and teardown order should be reversed', function () {
                expect($this->setupOrder)->toBe([
                    'global-beforeEach',
                    'local-beforeEach',
                    'nested-beforeEach-1',
                    'nested-beforeEach-2',
                    'nested-beforeEach-3',
                ]);
                expect($this->teardownOrder)->toBe([]);
            });
        });
    });
});
