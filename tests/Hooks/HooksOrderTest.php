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

    assertAfterEachHooksOrder($this->name());
});

function assertAfterEachHooksOrder(string $name) {
    $data = match ($name) {
        '__pest_evaluable_simple_test' => [
            [
                'global-beforeEach',
                'local-beforeEach',
            ],
            [
                'local-afterEach',
                'global-afterEach',
            ],
        ],
        '__pest_evaluable__nested_1__→_nested_test' => [
            [
                'global-beforeEach',
                'local-beforeEach',
                'nested-beforeEach-1',
            ],
            [
                'nested-afterEach-1',
                'local-afterEach',
                'global-afterEach',
            ],
        ],
        '__pest_evaluable__nested_1__→__nested_2__→_setup_and_teardown_order_should_be_reversed' => [
            [
                'global-beforeEach',
                'local-beforeEach',
                'nested-beforeEach-1',
                'nested-beforeEach-2',
            ],
            [
                'nested-afterEach-2',
                'nested-afterEach-1',
                'local-afterEach',
                'global-afterEach',
            ],
        ],
        '__pest_evaluable__nested_1__→__nested_2__→__nested_3__→_setup_and_teardown_order_should_be_reversed' => [
            [
                'global-beforeEach',
                'local-beforeEach',
                'nested-beforeEach-1',
                'nested-beforeEach-2',
                'nested-beforeEach-3',
            ],
            [
                'nested-afterEach-3',
                'nested-afterEach-2',
                'nested-afterEach-1',
                'local-afterEach',
                'global-afterEach',
            ],
        ],
        default => test()->fail('Unexpected test name: '.$name),
    };

    expect(test()->setupOrder)->toBe($data[0]);
    expect(test()->teardownOrder)->toBe($data[1]);

}

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
