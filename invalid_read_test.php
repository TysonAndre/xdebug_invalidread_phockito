<?php

// This is a concatenated collection of multiple files, from Phockito and phpunit.
// similar to https://github.com/ifwe/phockito
// (This makes it easier to set up)
// The original bug was found in a patched version of Phockito.
// Currently, this emits invalid memory read errors if run in valgrind.



namespace PHPUnit\Framework\Constraint {

use SebastianBergmann\Exporter\Exporter;

/**
 * Constraint that accepts any input value.
 */
class IsAnything
{
    protected $exporter;

    public function __construct()
    {
        $this->exporter = new Exporter;
    }
}
}

namespace SebastianBergmann\Exporter {

use SebastianBergmann\RecursionContext\Context;

class Exporter
{
}
}
namespace{

class __phockito_mockedclass_Mock extends mockedclass {

}

class Phockito {
	const MOCK_PREFIX = '__phockito_';

    public static $_instanceid_counter = 0;

	/* ** INTERNAL INTERFACES END ** */

	/**
	 * Given a class name as a string, return a new class name as a string which acts as a mock
	 * of the passed class name. Probably not useful by itself until we start supporting static method stubbing
	 *
	 * @param string $class - The class to mock
	 * @return object - The class that acts as a Phockito mock of the passed class
	 */
	static function mock(string $class) {
		$mockClass = '__phockito_mockedclass_Mock';

		return new $mockClass();
	}

	static function verify($mock, $times = 1) {
		return new Phockito_VerifyBuilder();
	}
}

/**
 * Marks all mocks for easy identification
 */
interface Phockito_MockMarker {

}

class Phockito_VerifyBuilder {

	function __call($called, $args) {
        // The p
        printf("Verifying Phockito_Verify_Builder->%s\n", var_export($args, true));
        flush();

		throw new \Exception('x');
	}
}

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Constraint\IsAnything;

/**
 * Returns a PHPUnit\Framework\Constraint\IsAnything matcher object.
 *
 * @return IsAnything
 */
function anything()
{
    return new IsAnything();
}

////////////////////////////////////////////////////////////////////////////////
// This is the main part of the test case, below
////////////////////////////////////////////////////////////////////////////////

class mockedclass {}

class Test {
    public function outer() {
        $mock = Phockito::mock('mockedclass');
        Phockito::verify($mock, 1)->add(anything(), anything(), anything(), anything(), 1500, 3000, anything(), 'P', 3, anything(), anything(), 'U', anything(), anything(), 0);
    }
}

function foo() {
    $t = new Test();
    $t->outer();
}
foo();
}
