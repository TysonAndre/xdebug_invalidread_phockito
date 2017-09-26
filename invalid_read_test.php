<?php

// This is a concatenated collection of multiple files, from Phockito and phpunit.
// similar to https://github.com/ifwe/phockito
// (This makes it easier to set up)
// The original bug was found in a patched version of Phockito.
// Currently, this emits invalid memory read errors if run in valgrind.



/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\Framework\Constraint {

use Countable;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\SelfDescribing;
use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Exporter\Exporter;

/**
 * Abstract base class for constraints which can be applied to any value.
 */
abstract class Constraint implements Countable, SelfDescribing
{
    protected $exporter;

    public function __construct()
    {
        $this->exporter = new Exporter;
    }

    /**
     * Evaluates the constraint for parameter $other
     *
     * If $returnResult is set to false (the default), an exception is thrown
     * in case of a failure. null is returned otherwise.
     *
     * If $returnResult is true, the result of the evaluation is returned as
     * a boolean value instead: true in case of success, false in case of a
     * failure.
     *
     * @param mixed  $other        Value or object to evaluate.
     * @param string $description  Additional information about the test
     * @param bool   $returnResult Whether to return a result or throw an exception
     *
     * @return mixed
     *
     * @throws ExpectationFailedException
     */
    public function evaluate($other, $description = '', $returnResult = false)
    {
        $success = false;

        if ($this->matches($other)) {
            $success = true;
        }

        if ($returnResult) {
            return $success;
        }

        if (!$success) {
            $this->fail($other, $description);
        }
    }

    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     *
     * This method can be overridden to implement the evaluation algorithm.
     *
     * @param mixed $other Value or object to evaluate.
     *
     * @return bool
     */
    protected function matches($other)
    {
        return false;
    }

    /**
     * Counts the number of constraint elements.
     *
     * @return int
     */
    public function count()
    {
        return 1;
    }

    /**
     * Throws an exception for the given compared value and test description
     *
     * @param mixed             $other             Evaluated value or object.
     * @param string            $description       Additional information about the test
     * @param ComparisonFailure $comparisonFailure
     *
     * @throws ExpectationFailedException
     */
    protected function fail($other, $description, ComparisonFailure $comparisonFailure = null)
    {
        $failureDescription = 'x';

        if (!empty($description)) {
            $failureDescription = $description . "\n" . $failureDescription;
        }

        throw new ExpectationFailedException(
            $failureDescription,
            $comparisonFailure
        );
    }
}
}
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework\Constraint {

use PHPUnit\Framework\ExpectationFailedException;

/**
 * Constraint that accepts any input value.
 */
class IsAnything extends Constraint
{
    /**
     * Evaluates the constraint for parameter $other
     *
     * If $returnResult is set to false (the default), an exception is thrown
     * in case of a failure. null is returned otherwise.
     *
     * If $returnResult is true, the result of the evaluation is returned as
     * a boolean value instead: true in case of success, false in case of a
     * failure.
     *
     * @param mixed  $other        Value or object to evaluate.
     * @param string $description  Additional information about the test
     * @param bool   $returnResult Whether to return a result or throw an exception
     *
     * @return mixed
     *
     * @throws ExpectationFailedException
     */
    public function evaluate($other, $description = '', $returnResult = false)
    {
        return $returnResult ? true : null;
    }

    /**
     * Returns a string representation of the constraint.
     *
     * @return string
     */
    public function toString()
    {
        return 'is anything';
    }

    /**
     * Counts the number of constraint elements.
     *
     * @return int
     */
    public function count()
    {
        return 0;
    }
}
}
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\Framework {

/**
 * Interface for classes that can return a description of itself.
 */
interface SelfDescribing
{
    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString();
}
}
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\Framework {

use PHPUnit\Framework\Constraint\IsAnything;

/**
 * A set of assertion methods.
 */
abstract class Assert
{
    /**
     * @return IsAnything
     */
    public static function anything()
    {
        return new IsAnything;
    }
}
}

namespace SebastianBergmann\Exporter {

/*
 * This file is part of the exporter package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use SebastianBergmann\RecursionContext\Context;

/**
 * A nifty utility for visualizing PHP variables.
 *
 * <code>
 * <?php
 * use SebastianBergmann\Exporter\Exporter;
 *
 * $exporter = new Exporter;
 * print $exporter->export(new Exception);
 * </code>
 */
class Exporter
{
    /**
     * Exports a value as a string
     *
     * The output of this method is similar to the output of print_r(), but
     * improved in various aspects:
     *
     *  - NULL is rendered as "null" (instead of "")
     *  - TRUE is rendered as "true" (instead of "1")
     *  - FALSE is rendered as "false" (instead of "")
     *  - Strings are always quoted with single quotes
     *  - Carriage returns and newlines are normalized to \n
     *  - Recursion and repeated rendering is treated properly
     *
     * @param mixed $value
     * @param int   $indentation The indentation level of the 2nd+ line
     *
     * @return string
     */
    public function export($value, $indentation = 0)
    {
        return $this->recursiveExport($value, $indentation);
    }

    /**
     * @param mixed   $data
     * @param Context $context
     *
     * @return string
     */
    public function shortenedRecursiveExport(&$data, Context $context = null)
    {
        $result   = [];
        $exporter = new self();

        if (!$context) {
            $context = new Context;
        }

        $array = $data;
        $context->add($data);

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if ($context->contains($data[$key]) !== false) {
                    $result[] = '*RECURSION*';
                } else {
                    $result[] = sprintf(
                        'array(%s)',
                        $this->shortenedRecursiveExport($data[$key], $context)
                    );
                }
            } else {
                $result[] = $exporter->shortenedExport($value);
            }
        }

        return implode(', ', $result);
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
                count($this->toArray($value)) > 0 ? '...' : ''
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

    /**
     * Converts an object to an array containing all of its private, protected
     * and public properties.
     *
     * @param mixed $value
     *
     * @return array
     */
    public function toArray($value)
    {
        if (!is_object($value)) {
            return (array) $value;
        }

        $array = [];

        foreach ((array) $value as $key => $val) {
            // properties are transformed to keys in the following way:
            // private   $property => "\0Classname\0property"
            // protected $property => "\0*\0property"
            // public    $property => "property"
            if (preg_match('/^\0.+\0(.+)$/', $key, $matches)) {
                $key = $matches[1];
            }

            // See https://github.com/php/php-src/commit/5721132
            if ($key === "\0gcdata") {
                continue;
            }

            $array[$key] = $val;
        }

        return $array;
    }

    /**
     * Recursive implementation of export
     *
     * @param mixed                                       $value       The value to export
     * @param int                                         $indentation The indentation level of the 2nd+ line
     * @param \SebastianBergmann\RecursionContext\Context $processed   Previously processed objects
     *
     * @return string
     *
     * @see    SebastianBergmann\Exporter\Exporter::export
     */
    protected function recursiveExport(&$value, $indentation, $processed = null)
    {
        if ($value === null) {
            return 'null';
        }

        if ($value === true) {
            return 'true';
        }

        if ($value === false) {
            return 'false';
        }

        if (is_float($value) && floatval(intval($value)) === $value) {
            return "$value.0";
        }

        if (is_string($value)) {
            // Match for most non printable chars somewhat taking multibyte chars into account
            if (preg_match('/[^\x09-\x0d\x1b\x20-\xff]/', $value)) {
                return 'Binary String: 0x' . bin2hex($value);
            }

            return "'" .
            str_replace('<lf>', "\n",
                str_replace(
                    ["\r\n", "\n\r", "\r", "\n"],
                    ['\r\n<lf>', '\n\r<lf>', '\r<lf>', '\n<lf>'],
                    $value
                )
            ) .
            "'";
        }

        $whitespace = str_repeat(' ', 4 * $indentation);

        if (!$processed) {
            $processed = new Context;
        }

        if (is_array($value)) {
            if (($key = $processed->contains($value)) !== false) {
                return 'Array &' . $key;
            }

            $array  = $value;
            $key    = $processed->add($value);
            $values = '';

            if (count($array) > 0) {
                foreach ($array as $k => $v) {
                    $values .= sprintf(
                        '%s    %s => %s' . "\n",
                        $whitespace,
                        $this->recursiveExport($k, $indentation),
                        $this->recursiveExport($value[$k], $indentation + 1, $processed)
                    );
                }

                $values = "\n" . $values . $whitespace;
            }

            return sprintf('Array &%s (%s)', $key, $values);
        }

        if (is_object($value)) {
            $class = get_class($value);

            if ($hash = $processed->contains($value)) {
                return sprintf('%s Object &%s', $class, $hash);
            }

            $hash   = $processed->add($value);
            $values = '';
            $array  = $this->toArray($value);

            if (count($array) > 0) {
                foreach ($array as $k => $v) {
                    $values .= sprintf(
                        '%s    %s => %s' . "\n",
                        $whitespace,
                        $this->recursiveExport($k, $indentation),
                        $this->recursiveExport($v, $indentation + 1, $processed)
                    );
                }

                $values = "\n" . $values . $whitespace;
            }

            return sprintf('%s Object &%s (%s)', $class, $hash, $values);
        }

        return var_export($value, true);
    }
}
}
namespace{

/**
 * Phockito - Mockito for PHP
 *
 * Mocking framework based on Mockito for Java
 *
 * (C) 2011 Hamish Friedlander / SilverStripe. Distributable under the same license as SilverStripe.
 *
 * Patched for php 7.0 and php 7.1 compatibility. Incompatible with php 5.
 *
 * Example usage:
 *
 *   // Create the mock
 *   $iterator = Phockito::mock('ArrayIterator');
 *
 *   // Use the mock object - doesn't do anything, functions return null
 *   $iterator->append('Test');
 *   $iterator->asort();
 *
 *   // Selectively verify execution
 *   Phockito::verify($iterator)->append('Test');
 *   // 1 is default - can also do 2, 3  for exact numbers, or 1+ for at least one, or 0 for never
 *   Phockito::verify($iterator, 1)->asort();
 *
 * Example stubbing:
 *
 *   // Create the mock
 *   $iterator = Phockito::mock('ArrayIterator');
 *
 *   // Stub in a value
 *   Phockito::when($iterator->offsetGet(0))->return('first');
 *
 *   // Prints "first"
 *   print_r($iterator->offsetGet(0));
 *
 *   // Prints null, because get(999) not stubbed
 *   print_r($iterator->offsetGet(999));
 *
 *
 * Note that several functions are declared as public so that builder classes can access them. Anything
 * starting with an "_" is for internal consumption only
 */
class Phockito {
	const MOCK_PREFIX = '__phockito_';

	/* ** Static Configuration *
		Feel free to change these at any time.
	*/

	/** @var bool - If true, don't warn when doubling classes with final methods, just ignore the methods. If false, throw warnings when final methods encountered */
	public static $ignore_finals = true;

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
	 * Checks if the two argument sets (passed as arrays) match. Simple serialized check for now, to be replaced by
	 * something that can handle anyString etc matchers later
	 */
	public static function _arguments_match($mockclass, $method, $a, $b) {
		return true;
	}

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
				if (self::_arguments_match($class, $method, $matcher['args'], $args)) {
					// Consume the next response - except the last one, which repeats indefinitely
					if (count($matcher['steps']) > 1) return array_shift($matcher['steps']);
					else return reset($matcher['steps']);
				}
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

		// Step through every method declared on the object
		foreach ($reflect->getMethods() as $method) {
			// Skip private methods. They shouldn't ever be called anyway
			if ($method->isPrivate()) continue;

			// Either skip or throw error on final methods.
			if ($method->isFinal()) {
				if (self::$ignore_finals) continue;
				else user_error("Class $mockedClass has final method {$method->name}, which we can\'t mock", E_USER_WARNING);
			}

			// Get the modifiers for the function as a string (static, public, etc) - ignore abstract though, all mock methods are concrete
			$modifiers = implode(' ', Reflection::getModifierNames($method->getModifiers() & ~(ReflectionMethod::IS_ABSTRACT)));

			// See if the method is return byRef
			$byRef = $method->returnsReference() ? "&" : "";

			// PHP fragment that is the arguments definition for this method
			$defparams = array(); $callparams = array();

			// Array of defaults (sparse numeric)
			self::$_defaults[$mockedClass][$method->name] = array();

			foreach ($method->getParameters() as $i => $parameter) {
				// Turn the method arguments into a php fragment that calls a function with them
				$callparams[] = '$'.$parameter->getName();

				// Get the (optional) type hint of the parameter (with space padding on the right)
				$typeRaw = self::_get_type_hint_of_parameter($parameter);
				$type = $typeRaw ? "$typeRaw " : "";

				// Check if the parameter is variadic - it is possible there is a type hint before this token
				// NOTE: isOptional() can be true while isDefaultValueAvailable from isDefaultValueAvailable in php 7.1
				$hasDefault = ($parameter->isOptional() || $parameter->isDefaultValueAvailable()) && !$parameter->isVariadic();

				try {
					$defaultValue = $parameter->getDefaultValue();
				}
				catch (ReflectionException $e) {
					$defaultValue = null;
				}

				// Turn the method arguments into a php fragment the defines a function with them, including possibly the by-reference "&" and any default
				$defparams[] =
					$type .
					($parameter->isVariadic() ? '...' : '') .
					($parameter->isPassedByReference() ? '&' : '') .
					'$'.$parameter->getName() .
					($hasDefault ? '=' . var_export($defaultValue, true) : '')
				;

				// Finally cache the default value for matching against later
				if ($parameter->isOptional()) self::$_defaults[$mockedClass][$method->name][$i] = $defaultValue;
			}

			// Turn that array into a comma seperated list
			$defparams = implode(', ', $defparams); $callparams = implode(', ', $callparams);

			// Need to have a method signature with the same return type in order to create a subclass. This will limit what can be returned by a mock.
			// At runtime, an Error will be thrown (e.g. if no return value is mocked for a function with a return type of array)
			$returnType = self::_get_return_type($method);
			$defReturn = $returnType ? " : $returnType " : "";

			// What to do if there's no stubbed response
			if ($partial && !$method->isAbstract()) {
				$failover = "call_user_func_array(array($mockedClassString, '{$method->name}'), \$args)";
			}
			else {
				$failover = "null";
			}

			// Constructor is handled specially. For spies, we do call the parent's constructor. For mocks we ignore
			if ($method->name == '__construct') {
				if ($partial) {
					$php[] = <<<EOT
  function __phockito_parent_construct( $defparams ){
	parent::__construct( $callparams );
  }
EOT;
				}
			}
			elseif ($method->name == '__call') {
				$has__call = true;
				if ($method->getNumberOfParameters() >= 2 && $method->getParameters()[1]->isArray()) {
					$has__call_type = 'array';
				}
			}
			elseif ($method->name == '__toString') {
				$has__toString = true;
			}
			// Build an overriding method that calls Phockito::__called, and never calls the parent
			else {
				$initArgsStatements = self::_create_array_from_func_args($method->getParameters());
				$returnResultStatement = $returnType !== 'void' ? 'return $result;' : 'return;';
				$php[] = <<<EOT
  $modifiers function $byRef {$method->name}( $defparams )$defReturn{
	// Usually \$args = func_get_args();, but special case for references
	$initArgsStatements

	\$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
	\$instance = \$backtrace[0]['type'] == '::' ? ('::'.$mockedClassString) : \$this->__phockito_instanceid;

	\$response = {$phockito}::__called($mockedClassString, \$instance, '{$method->name}', \$args);

	\$result = \$response ? {$phockito}::__perform_response(\$response, \$args) : ($failover);
	$returnResultStatement
  }
EOT;
			}
		}

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
	 * @param ReflectionParameter[] $parameters
	 * @return string a command to initialize $args in an eval()ed mock.
	 */
	private static function _create_array_from_func_args(array $parameters) : string {
		$hasReferences = false;
		foreach ($parameters as $param) {
			if ($param->isPassedByReference()) {
				$hasReferences = true;
			}
		}
		if (!$hasReferences) {
			return "	\$args = func_get_args();\n";
		}
		$contents = "	\$args = [];\n";
		foreach ($parameters as $param) {
			$varName = '$' . $param->getName();
			if ($param->isVariadic()) {
				$contents .= "	foreach ($varName as \$__var) { \$args[] = \$__var; };\n";
			} else if ($param->isPassedByReference()) {
				$contents .= "	\$args[] = &$varName;\n";
			} else {
				$contents .= "	\$args[] = $varName;\n";
			}
		}
		$contents .= <<<EOT
	\$args = array_slice(\$args, 0, func_num_args());  // ignore default or variadic params
	for (\$__i = count(\$args); \$__i < func_num_args(); \$__i++) {
	  \$args[] = func_get_arg(\$__i);
	}
EOT;
		return $contents;
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

		// If we've been given a type registrar, call it (we need to do this even if class exists, since PHPUnit resets globals, possibly de-registering between tests)
		$type_registrar = self::$type_registrar;
		if ($type_registrar) $type_registrar::register_double($mockClass, $class, self::$_is_interface[$class]);

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

	protected $class;
	protected $instance;
	protected $times;

	function __construct($class, $instance, $times) {
		$this->class = $class;
		$this->instance = $instance;
		$this->times = $times;
	}

	function __call($called, $args) {
        // The p
        printf("Verifying Phockito_Verify_Builder->%s\n", $called, var_export($args, true), serialize(Phockito::$_call_list));
        flush();
		$count = 0;

		// TODO: SplObjectStorage to speed up large number of assertions? Or use spl_object_hash() and method()?
		foreach (Phockito::$_call_list as $call) {
			if ($call['instance'] == $this->instance && $call['method'] == $called && Phockito::_arguments_match($this->class, $called, $args, $call['args'])) {
				$count++;
			}
		}

		if (preg_match('/([0-9]+)\+/', $this->times, $match)) {
			if ($count >= (int)$match[1]) return;
		}
		else {
			if ($count == $this->times) return;
		}

		$message  = "Failed asserting that method $called was called {$this->times} times - actually called $count times.\n";
		$message .= "Wanted call:\n";

		$message .= "Calls:\n";

        //unset($exporter);  // testing workaround

		throw new \Exception($message);
	}
}
use PHPUnit\Framework\Assert;
/**
 * Returns a PHPUnit\Framework\Constraint\IsAnything matcher object.
 *
 * @return IsAnything
 */
function anything()
{
    return Assert::anything();
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
