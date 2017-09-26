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
    public function export($value, $indentation = 0)
    {
        return $this->recursiveExport($value, $indentation);
    }

    /**
     * Exports a value into a single-line string
     *
     * The output of this method is similar to the output of
     * SebastianBergmann\Exporter\Exporter::export().
     *
     * Newlines are replaced by the visible string '\n'.
     * Contents of arrays and objects (if any) are replaced by '...'.
     *
     * @param mixed $value
     *
     * @return string
     *
     * @see    SebastianBergmann\Exporter\Exporter::export
     */
    public function shortenedExport($value)
    {
        if (is_string($value)) {
            $string = str_replace("\n", '', $this->export($value));

            if (function_exists('mb_strlen')) {
                if (mb_strlen($string) > 40) {
                    $string = mb_substr($string, 0, 30) . '...' . mb_substr($string, -7);
                }
            } else {
                if (strlen($string) > 40) {
                    $string = substr($string, 0, 30) . '...' . substr($string, -7);
                }
            }

            return $string;
        }

        if (is_object($value)) {
            return sprintf(
                '%s Object (%s)',
                get_class($value),
                '...'
            );
        }

        if (is_array($value)) {
            return sprintf(
                'Array (%s)',
                count($value) > 0 ? '...' : ''
            );
        }

        return $this->export($value);
    }

    protected function recursiveExport(&$value, $indentation, $processed = null)
    {
        return 'x';
    }
}
}
namespace{

class Phockito {
	const MOCK_PREFIX = '__phockito_';


	/** @var string - Class name of a class with a static "register_double" method that will be called with any double to inject into some other type tracking system */
	public static $type_registrar = null;

	/* ** INTERNAL INTERFACES START **
		These are declared as public so that mocks and builders can access them,
		but they're for internal use only, not actually for consumption by the general public
	*/

	/** Each mock instance needs a unique string ID, which we build by incrementing this counter @var int */
	public static $_instanceid_counter = 0;

	/** Array of most-recent-first calls. Each item is an array of (instance, method, args) named hashes. @var array */
	public static $_call_list = array();

	/**
	 * Array of stubs responses
	 * Nested as [instance][method][0..n], each item is an array of ('args' => the method args, 'responses' => stubbed responses)
	 * @var array
	 */
	public static $_responses = array();

	/**
	 * Array of defaults for a given class and method
	 * @var array
	 */
	public static $_defaults = array();

	/**
	 * Records whether a given class is an interface, to avoid repeatedly generating reflection objects just to re-call type registrar
	 * @var array
	 */
	public static $_is_interface = array();

	/**
	 * Called by the mock instances when a method is called. Records the call and returns a response if one has been
	 * stubbed in
	 */
	public static function __called($class, $instance, $method, $args) {
		// Record the call as most recent first
		array_unshift(self::$_call_list, array(
			'class' => $class,
			'instance' => $instance,
			'method' => $method,
			'args' => array_map(function($arg) { return $arg; }, $args),  // Convert any references in $args to values.
		));

		// Look up any stubbed responses
		if (isset(self::$_responses[$instance][$method])) {
			// Find the first one that matches the called-with arguments
			foreach (self::$_responses[$instance][$method] as $i => &$matcher) {
                // Consume the next response - except the last one, which repeats indefinitely
                if (count($matcher['steps']) > 1) return array_shift($matcher['steps']);
                else return reset($matcher['steps']);
			}
		}
	}

	/**
	 * @param mixed[] $args - Arguments padded to a function.
	 */
	public static function __perform_response($response, array $args) {
		// So, I have to pass an array of references?
		if ($response['action'] == 'return') return $response['value'];
		else if ($response['action'] == 'throw') {
			/** @var Exception $class */
			$class = $response['value'];
			throw (is_object($class) ? $class : new $class());
		}
		else if ($response['action'] == 'callback') return call_user_func_array($response['value'], $args);
		else user_error("Got unknown action {$response['action']} - how did that happen?", E_USER_ERROR);
	}

	/* ** INTERNAL INTERFACES END ** */

	/**
	 * Passed a class as a string to create the mock as, and the class as a string to mock,
	 * create the mocking class php and eval it into the current running environment
	 *
	 * @static
	 * @param bool $partial - Should test double be a partial or a full mock
	 * @param string $mockedClass - The name of the class (or interface) to create a mock of
	 * @return string The name of the mocker class
	 */
	protected static function build_test_double($partial, $mockedClass) : string {
		// Bail if we were passed a classname that doesn't exist
		if (!class_exists($mockedClass) && !interface_exists($mockedClass)) user_error("Can't mock non-existent class $mockedClass", E_USER_ERROR);

		// How to get a reference to the Phockito class itself
		$phockito = '\\Phockito';

		// Reflect on the mocked class
		$reflect = new ReflectionClass($mockedClass);

		if ($reflect->isFinal()) user_error("Can't mock final class $mockedClass", E_USER_ERROR);

		// Build up an array of php fragments that make the mocking class definition
		$php = array();

		// Get the namespace & the shortname of the mocked class
		$mockedNamespace = $reflect->getNamespaceName();
		$mockedShortName = $reflect->getShortName();

		// Build the short name of the mocker class based on the mocked classes shortname
		$mockerShortName = self::MOCK_PREFIX.$mockedShortName.($partial ? '_Spy' : '_Mock');
		// And build the full class name of the mocker by prepending the namespace if appropriate
		$mockerClass = $mockedNamespace . '\\' . $mockerShortName;

		// If we've already built this test double, just return it
		if (class_exists($mockerClass, false)) return $mockerClass;

		// If the mocked class is in a namespace, the test double goes in the same namespace
		$namespaceDeclaration = $mockedNamespace ? "namespace $mockedNamespace;" : '';

		// The only difference between mocking a class or an interface is how the mocking class extends from the mocked
		$extends = $reflect->isInterface() ? 'implements' : 'extends';
		$marker = $reflect->isInterface() ? ", {$phockito}_MockMarker" : "implements {$phockito}_MockMarker";

		// When injecting the class as a string, need to escape the "\" character.
		$mockedClassString = "'".str_replace('\\', '\\\\', $mockedClass)."'";

		// Add opening class stanza
		$php[] = <<<EOT
$namespaceDeclaration
class $mockerShortName $extends $mockedShortName $marker {
  public \$__phockito_class;
  public \$__phockito_instanceid;

  function __construct() {
	\$this->__phockito_class = $mockedClassString;
	\$this->__phockito_instanceid = $mockedClassString.':'.(++{$phockito}::\$_instanceid_counter);
  }
EOT;

		// And record the defaults at the same time
		self::$_defaults[$mockedClass] = array();
		// And whether it's an interface
		self::$_is_interface[$mockedClass] = $reflect->isInterface();

		// Track if the mocked class defines either of the __call and/or __toString magic methods
		$has__call = $has__toString = false;
		$has__call_type = '';

		// Always add a __call method to catch any calls to undefined functions
		$failover = ($partial && $has__call) ? "parent::__call(\$name, \$args)" : "null";

		$php[] = <<<EOT
  function __call(\$name, $has__call_type\$args) {
	\$response = {$phockito}::__called($mockedClassString, \$this->__phockito_instanceid, \$name, \$args);

	if (\$response) return {$phockito}::__perform_response(\$response, \$args);
	else return $failover;
  }
EOT;

		// Always add a __toString method
		if ($partial) {
			if ($has__toString) $failover = "parent::__toString()";
			else $failover = "user_error('Object of class '.$mockedClassString.' could not be converted to string', E_USER_ERROR)";
		}
		else $failover = "''";

		$php[] = <<<EOT
  function __toString() {
	\$args = array();
	\$response = {$phockito}::__called($mockedClassString, \$this->__phockito_instanceid, "__toString", \$args);

	if (\$response) return {$phockito}::__perform_response(\$response, \$args);
	else return $failover;
  }
EOT;

		// Close off the class definition and eval it to create the class as an extant entity.
		$php[] = '}';
		$phpCode = implode("\n\n", $php);

		// Debug: uncomment to spit out the code we're about to compile to stdout
		// echo "\n" . $phpCode . "\n";
		eval($phpCode);
		return $mockerClass;
	}

	/**
	 * @return string|null (e.g. 'SomeClass', 'string', (in php7.1) '?int', etc.)
	 */
	private static function _reflection_type_to_declaration(ReflectionType $type = null) {
		if (!$type) {
			return '';
		}
		return
			($type->allowsNull() ? '?' : '') .
			($type->isBuiltin() ? '' : '\\') .  // include absolute namespace for class names, so this will be able to mock namespaced classes
			(string)$type;
	}

	/**
	 * @return string|null (e.g. 'SomeClass', 'string', (in php7.1) '?int', etc.)
	 */
	private static function _get_return_type(ReflectionMethod $method) {
		return self::_reflection_type_to_declaration($method->getReturnType());
	}

	/**
	 * @return string|null (e.g. 'SomeClass', 'string', (in php7.1) '?int', etc.)
	 */
	private static function _get_type_hint_of_parameter(ReflectionParameter $parameter) {
		return self::_reflection_type_to_declaration($parameter->getType());
	}

	/**
	 * Given a class name as a string, return a new class name as a string which acts as a mock
	 * of the passed class name. Probably not useful by itself until we start supporting static method stubbing
	 *
	 * @param string $class - The class to mock
	 * @return object - The class that acts as a Phockito mock of the passed class
	 */
	static function mock(string $class) {
		$mockClass = self::build_test_double(false, $class);

		return new $mockClass();
	}

	/**
	 * Verify builder. Takes a mock instance and an optional number of times to verify against. Returns a
	 * DSL object that catches the method to verify
	 *
	 * @param Phockito_Mock $mock - The mock instance to verify
	 * @param int|string $times - The number of times the method should be called, either a number, or a number followed by "+"
	 * @return Phockito_VerifyBuilder
	 */
	static function verify($mock, $times = 1) {
		return new Phockito_VerifyBuilder($mock->__phockito_class, $mock->__phockito_instanceid, $times);
	}
}

/**
 * Marks all mocks for easy identification
 */
interface Phockito_MockMarker {

}

/**
 * A builder than is returned by Phockito::verify to capture the method that specifies the verified method
 * Throws an exception if the verified method hasn't been called "$times" times, either a PHPUnit exception
 * or just an Exception if PHPUnit doesn't exist
 */
class Phockito_VerifyBuilder {

	function __construct($class, $instance, $times) {
	}

	function __call($called, $args) {
        // The p
        printf("Verifying Phockito_Verify_Builder->%s\n", var_export($args, true));
        flush();

		throw new \Exception('x');  // undef variable
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
