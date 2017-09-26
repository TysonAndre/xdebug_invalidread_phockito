<?php

/**
 * Constraint that accepts any input value.
 */
class IsAnything
{
    protected $exporter;

    public function __construct() {
        $this->exporter = new Exporter;
    }
}

class Exporter { }

class __phockito_mockedclass_Mock {};

class Phockito {
	/* ** INTERNAL INTERFACES END ** */

	static function mock(string $class) {
		return new __phockito_mockedclass_Mock();
	}

	static function verify($mock, $times = 1) {
		return new Phockito_VerifyBuilder();
	}
}

class Phockito_VerifyBuilder {

	function __call($called, $args) {
        printf("Verifying Phockito_Verify_Builder->%s\n", var_export($args, true));
        flush();

		throw new \Exception('x');
	}
}

function anything()
{
    return new IsAnything();
}

////////////////////////////////////////////////////////////////////////////////
// This is the main part of the test case, below
////////////////////////////////////////////////////////////////////////////////

class Test {
    public function outer() {
        $mock = new __phockito_mockedclass_Mock;
        Phockito::verify($mock, 1)->add(anything(), anything(), anything(), anything(), 1500, 3000, anything(), 'P', 3, anything(), anything(), 'U', anything(), anything(), 0);
    }
}

function foo() {
    $t = new Test();
    $t->outer();
}
foo();
