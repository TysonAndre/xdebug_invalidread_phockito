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

use ArrayAccess;
use Countable;
use DOMDocument;
use DOMElement;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\LogicalAnd;
use PHPUnit\Framework\Constraint\ArrayHasKey;
use PHPUnit\Framework\Constraint\ArraySubset;
use PHPUnit\Framework\Constraint\Attribute;
use PHPUnit\Framework\Constraint\Callback;
use PHPUnit\Framework\Constraint\ClassHasAttribute;
use PHPUnit\Framework\Constraint\ClassHasStaticAttribute;
use PHPUnit\Framework\Constraint\Count;
use PHPUnit\Framework\Constraint\DirectoryExists;
use PHPUnit\Framework\Constraint\FileExists;
use PHPUnit\Framework\Constraint\GreaterThan;
use PHPUnit\Framework\Constraint\IsAnything;
use PHPUnit\Framework\Constraint\IsEmpty;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\Constraint\IsFalse;
use PHPUnit\Framework\Constraint\IsFinite;
use PHPUnit\Framework\Constraint\IsIdentical;
use PHPUnit\Framework\Constraint\IsInfinite;
use PHPUnit\Framework\Constraint\IsInstanceOf;
use PHPUnit\Framework\Constraint\IsJson;
use PHPUnit\Framework\Constraint\IsNan;
use PHPUnit\Framework\Constraint\IsNull;
use PHPUnit\Framework\Constraint\IsReadable;
use PHPUnit\Framework\Constraint\IsTrue;
use PHPUnit\Framework\Constraint\IsType;
use PHPUnit\Framework\Constraint\IsWritable;
use PHPUnit\Framework\Constraint\JsonMatches;
use PHPUnit\Framework\Constraint\LessThan;
use PHPUnit\Framework\Constraint\LogicalNot;
use PHPUnit\Framework\Constraint\ObjectHasAttribute;
use PHPUnit\Framework\Constraint\LogicalOr;
use PHPUnit\Framework\Constraint\RegularExpression;
use PHPUnit\Framework\Constraint\SameSize;
use PHPUnit\Framework\Constraint\StringContains;
use PHPUnit\Framework\Constraint\StringEndsWith;
use PHPUnit\Framework\Constraint\StringMatchesFormatDescription;
use PHPUnit\Framework\Constraint\StringStartsWith;
use PHPUnit\Framework\Constraint\TraversableContains;
use PHPUnit\Framework\Constraint\TraversableContainsOnly;
use PHPUnit\Framework\Constraint\LogicalXor;
use PHPUnit\Util\InvalidArgumentHelper;
use PHPUnit\Util\Type;
use PHPUnit\Util\Xml;
use ReflectionClass;
use ReflectionException;
use ReflectionObject;
use ReflectionProperty;
use Traversable;

/**
 * A set of assertion methods.
 */
abstract class Assert
{
    /**
     * @var int
     */
    private static $count = 0;

    /**
     * Asserts that an array has a specified key.
     *
     * @param mixed             $key
     * @param array|ArrayAccess $array
     * @param string            $message
     */
    public static function assertArrayHasKey($key, $array, $message = '')
    {
        if (!(\is_int($key) || \is_string($key))) {
            throw InvalidArgumentHelper::factory(
                1,
                'integer or string'
            );
        }

        if (!(\is_array($array) || $array instanceof ArrayAccess)) {
            throw InvalidArgumentHelper::factory(
                2,
                'array or ArrayAccess'
            );
        }

        $constraint = new ArrayHasKey($key);

        static::assertThat($array, $constraint, $message);
    }

    /**
     * Asserts that an array has a specified subset.
     *
     * @param array|ArrayAccess $subset
     * @param array|ArrayAccess $array
     * @param bool              $strict  Check for object identity
     * @param string            $message
     */
    public static function assertArraySubset($subset, $array, $strict = false, $message = '')
    {
        if (!(\is_array($subset) || $subset instanceof ArrayAccess)) {
            throw InvalidArgumentHelper::factory(
                1,
                'array or ArrayAccess'
            );
        }

        if (!(\is_array($array) || $array instanceof ArrayAccess)) {
            throw InvalidArgumentHelper::factory(
                2,
                'array or ArrayAccess'
            );
        }

        $constraint = new ArraySubset($subset, $strict);

        static::assertThat($array, $constraint, $message);
    }

    /**
     * Asserts that an array does not have a specified key.
     *
     * @param mixed             $key
     * @param array|ArrayAccess $array
     * @param string            $message
     */
    public static function assertArrayNotHasKey($key, $array, $message = '')
    {
        if (!(\is_int($key) || \is_string($key))) {
            throw InvalidArgumentHelper::factory(
                1,
                'integer or string'
            );
        }

        if (!(\is_array($array) || $array instanceof ArrayAccess)) {
            throw InvalidArgumentHelper::factory(
                2,
                'array or ArrayAccess'
            );
        }

        $constraint = new LogicalNot(
            new ArrayHasKey($key)
        );

        static::assertThat($array, $constraint, $message);
    }

    /**
     * Asserts that a haystack contains a needle.
     *
     * @param mixed  $needle
     * @param mixed  $haystack
     * @param string $message
     * @param bool   $ignoreCase
     * @param bool   $checkForObjectIdentity
     * @param bool   $checkForNonObjectIdentity
     */
    public static function assertContains($needle, $haystack, $message = '', $ignoreCase = false, $checkForObjectIdentity = true, $checkForNonObjectIdentity = false)
    {
        if (\is_array($haystack) ||
            \is_object($haystack) && $haystack instanceof Traversable) {
            $constraint = new TraversableContains(
                $needle,
                $checkForObjectIdentity,
                $checkForNonObjectIdentity
            );
        } elseif (\is_string($haystack)) {
            if (!\is_string($needle)) {
                throw InvalidArgumentHelper::factory(
                    1,
                    'string'
                );
            }

            $constraint = new StringContains(
                $needle,
                $ignoreCase
            );
        } else {
            throw InvalidArgumentHelper::factory(
                2,
                'array, traversable or string'
            );
        }

        static::assertThat($haystack, $constraint, $message);
    }

    /**
     * Asserts that a haystack that is stored in a static attribute of a class
     * or an attribute of an object contains a needle.
     *
     * @param mixed         $needle
     * @param string        $haystackAttributeName
     * @param string|object $haystackClassOrObject
     * @param string        $message
     * @param bool          $ignoreCase
     * @param bool          $checkForObjectIdentity
     * @param bool          $checkForNonObjectIdentity
     */
    public static function assertAttributeContains($needle, $haystackAttributeName, $haystackClassOrObject, $message = '', $ignoreCase = false, $checkForObjectIdentity = true, $checkForNonObjectIdentity = false)
    {
        static::assertContains(
            $needle,
            static::readAttribute($haystackClassOrObject, $haystackAttributeName),
            $message,
            $ignoreCase,
            $checkForObjectIdentity,
            $checkForNonObjectIdentity
        );
    }

    /**
     * Asserts that a haystack does not contain a needle.
     *
     * @param mixed  $needle
     * @param mixed  $haystack
     * @param string $message
     * @param bool   $ignoreCase
     * @param bool   $checkForObjectIdentity
     * @param bool   $checkForNonObjectIdentity
     */
    public static function assertNotContains($needle, $haystack, $message = '', $ignoreCase = false, $checkForObjectIdentity = true, $checkForNonObjectIdentity = false)
    {
        if (\is_array($haystack) ||
            \is_object($haystack) && $haystack instanceof Traversable) {
            $constraint = new LogicalNot(
                new TraversableContains(
                    $needle,
                    $checkForObjectIdentity,
                    $checkForNonObjectIdentity
                )
            );
        } elseif (\is_string($haystack)) {
            if (!\is_string($needle)) {
                throw InvalidArgumentHelper::factory(
                    1,
                    'string'
                );
            }

            $constraint = new LogicalNot(
                new StringContains(
                    $needle,
                    $ignoreCase
                )
            );
        } else {
            throw InvalidArgumentHelper::factory(
                2,
                'array, traversable or string'
            );
        }

        static::assertThat($haystack, $constraint, $message);
    }

    /**
     * Asserts that a haystack that is stored in a static attribute of a class
     * or an attribute of an object does not contain a needle.
     *
     * @param mixed         $needle
     * @param string        $haystackAttributeName
     * @param string|object $haystackClassOrObject
     * @param string        $message
     * @param bool          $ignoreCase
     * @param bool          $checkForObjectIdentity
     * @param bool          $checkForNonObjectIdentity
     */
    public static function assertAttributeNotContains($needle, $haystackAttributeName, $haystackClassOrObject, $message = '', $ignoreCase = false, $checkForObjectIdentity = true, $checkForNonObjectIdentity = false)
    {
        static::assertNotContains(
            $needle,
            static::readAttribute($haystackClassOrObject, $haystackAttributeName),
            $message,
            $ignoreCase,
            $checkForObjectIdentity,
            $checkForNonObjectIdentity
        );
    }

    /**
     * Asserts that a haystack contains only values of a given type.
     *
     * @param string $type
     * @param mixed  $haystack
     * @param bool   $isNativeType
     * @param string $message
     */
    public static function assertContainsOnly($type, $haystack, $isNativeType = null, $message = '')
    {
        if (!(\is_array($haystack) ||
            \is_object($haystack) && $haystack instanceof Traversable)) {
            throw InvalidArgumentHelper::factory(
                2,
                'array or traversable'
            );
        }

        if ($isNativeType == null) {
            $isNativeType = Type::isType($type);
        }

        static::assertThat(
            $haystack,
            new TraversableContainsOnly(
                $type,
                $isNativeType
            ),
            $message
        );
    }

    /**
     * Asserts that a haystack contains only instances of a given classname
     *
     * @param string            $classname
     * @param array|Traversable $haystack
     * @param string            $message
     */
    public static function assertContainsOnlyInstancesOf($classname, $haystack, $message = '')
    {
        if (!(\is_array($haystack) ||
            \is_object($haystack) && $haystack instanceof Traversable)) {
            throw InvalidArgumentHelper::factory(
                2,
                'array or traversable'
            );
        }

        static::assertThat(
            $haystack,
            new TraversableContainsOnly(
                $classname,
                false
            ),
            $message
        );
    }

    /**
     * Asserts that a haystack that is stored in a static attribute of a class
     * or an attribute of an object contains only values of a given type.
     *
     * @param string        $type
     * @param string        $haystackAttributeName
     * @param string|object $haystackClassOrObject
     * @param bool          $isNativeType
     * @param string        $message
     */
    public static function assertAttributeContainsOnly($type, $haystackAttributeName, $haystackClassOrObject, $isNativeType = null, $message = '')
    {
        static::assertContainsOnly(
            $type,
            static::readAttribute($haystackClassOrObject, $haystackAttributeName),
            $isNativeType,
            $message
        );
    }

    /**
     * Asserts that a haystack does not contain only values of a given type.
     *
     * @param string $type
     * @param mixed  $haystack
     * @param bool   $isNativeType
     * @param string $message
     */
    public static function assertNotContainsOnly($type, $haystack, $isNativeType = null, $message = '')
    {
        if (!(\is_array($haystack) ||
            \is_object($haystack) && $haystack instanceof Traversable)) {
            throw InvalidArgumentHelper::factory(
                2,
                'array or traversable'
            );
        }

        if ($isNativeType == null) {
            $isNativeType = Type::isType($type);
        }

        static::assertThat(
            $haystack,
            new LogicalNot(
                new TraversableContainsOnly(
                    $type,
                    $isNativeType
                )
            ),
            $message
        );
    }

    /**
     * Asserts that a haystack that is stored in a static attribute of a class
     * or an attribute of an object does not contain only values of a given
     * type.
     *
     * @param string        $type
     * @param string        $haystackAttributeName
     * @param string|object $haystackClassOrObject
     * @param bool          $isNativeType
     * @param string        $message
     */
    public static function assertAttributeNotContainsOnly($type, $haystackAttributeName, $haystackClassOrObject, $isNativeType = null, $message = '')
    {
        static::assertNotContainsOnly(
            $type,
            static::readAttribute($haystackClassOrObject, $haystackAttributeName),
            $isNativeType,
            $message
        );
    }

    /**
     * Asserts the number of elements of an array, Countable or Traversable.
     *
     * @param int    $expectedCount
     * @param mixed  $haystack
     * @param string $message
     */
    public static function assertCount($expectedCount, $haystack, $message = '')
    {
        if (!\is_int($expectedCount)) {
            throw InvalidArgumentHelper::factory(1, 'integer');
        }

        if (!$haystack instanceof Countable &&
            !$haystack instanceof Traversable &&
            !\is_array($haystack)) {
            throw InvalidArgumentHelper::factory(2, 'countable or traversable');
        }

        static::assertThat(
            $haystack,
            new Count($expectedCount),
            $message
        );
    }

    /**
     * Asserts the number of elements of an array, Countable or Traversable
     * that is stored in an attribute.
     *
     * @param int           $expectedCount
     * @param string        $haystackAttributeName
     * @param string|object $haystackClassOrObject
     * @param string        $message
     */
    public static function assertAttributeCount($expectedCount, $haystackAttributeName, $haystackClassOrObject, $message = '')
    {
        static::assertCount(
            $expectedCount,
            static::readAttribute($haystackClassOrObject, $haystackAttributeName),
            $message
        );
    }

    /**
     * Asserts the number of elements of an array, Countable or Traversable.
     *
     * @param int    $expectedCount
     * @param mixed  $haystack
     * @param string $message
     */
    public static function assertNotCount($expectedCount, $haystack, $message = '')
    {
        if (!\is_int($expectedCount)) {
            throw InvalidArgumentHelper::factory(1, 'integer');
        }

        if (!$haystack instanceof Countable &&
            !$haystack instanceof Traversable &&
            !\is_array($haystack)) {
            throw InvalidArgumentHelper::factory(2, 'countable or traversable');
        }

        $constraint = new LogicalNot(
            new Count($expectedCount)
        );

        static::assertThat($haystack, $constraint, $message);
    }

    /**
     * Asserts the number of elements of an array, Countable or Traversable
     * that is stored in an attribute.
     *
     * @param int           $expectedCount
     * @param string        $haystackAttributeName
     * @param string|object $haystackClassOrObject
     * @param string        $message
     */
    public static function assertAttributeNotCount($expectedCount, $haystackAttributeName, $haystackClassOrObject, $message = '')
    {
        static::assertNotCount(
            $expectedCount,
            static::readAttribute($haystackClassOrObject, $haystackAttributeName),
            $message
        );
    }

    /**
     * Asserts that two variables are equal.
     *
     * @param mixed  $expected
     * @param mixed  $actual
     * @param string $message
     * @param float  $delta
     * @param int    $maxDepth
     * @param bool   $canonicalize
     * @param bool   $ignoreCase
     */
    public static function assertEquals($expected, $actual, $message = '', $delta = 0.0, $maxDepth = 10, $canonicalize = false, $ignoreCase = false)
    {
        $constraint = new IsEqual(
            $expected,
            $delta,
            $maxDepth,
            $canonicalize,
            $ignoreCase
        );

        static::assertThat($actual, $constraint, $message);
    }

    /**
     * Asserts that a variable is equal to an attribute of an object.
     *
     * @param mixed         $expected
     * @param string        $actualAttributeName
     * @param string|object $actualClassOrObject
     * @param string        $message
     * @param float         $delta
     * @param int           $maxDepth
     * @param bool          $canonicalize
     * @param bool          $ignoreCase
     */
    public static function assertAttributeEquals($expected, $actualAttributeName, $actualClassOrObject, $message = '', $delta = 0.0, $maxDepth = 10, $canonicalize = false, $ignoreCase = false)
    {
        static::assertEquals(
            $expected,
            static::readAttribute($actualClassOrObject, $actualAttributeName),
            $message,
            $delta,
            $maxDepth,
            $canonicalize,
            $ignoreCase
        );
    }

    /**
     * Asserts that two variables are not equal.
     *
     * @param mixed  $expected
     * @param mixed  $actual
     * @param string $message
     * @param float  $delta
     * @param int    $maxDepth
     * @param bool   $canonicalize
     * @param bool   $ignoreCase
     */
    public static function assertNotEquals($expected, $actual, $message = '', $delta = 0.0, $maxDepth = 10, $canonicalize = false, $ignoreCase = false)
    {
        $constraint = new LogicalNot(
            new IsEqual(
                $expected,
                $delta,
                $maxDepth,
                $canonicalize,
                $ignoreCase
            )
        );

        static::assertThat($actual, $constraint, $message);
    }

    /**
     * Asserts that a variable is not equal to an attribute of an object.
     *
     * @param mixed         $expected
     * @param string        $actualAttributeName
     * @param string|object $actualClassOrObject
     * @param string        $message
     * @param float         $delta
     * @param int           $maxDepth
     * @param bool          $canonicalize
     * @param bool          $ignoreCase
     */
    public static function assertAttributeNotEquals($expected, $actualAttributeName, $actualClassOrObject, $message = '', $delta = 0.0, $maxDepth = 10, $canonicalize = false, $ignoreCase = false)
    {
        static::assertNotEquals(
            $expected,
            static::readAttribute($actualClassOrObject, $actualAttributeName),
            $message,
            $delta,
            $maxDepth,
            $canonicalize,
            $ignoreCase
        );
    }

    /**
     * Asserts that a variable is empty.
     *
     * @param mixed  $actual
     * @param string $message
     *
     * @throws AssertionFailedError
     */
    public static function assertEmpty($actual, $message = '')
    {
        static::assertThat($actual, static::isEmpty(), $message);
    }

    /**
     * Asserts that a static attribute of a class or an attribute of an object
     * is empty.
     *
     * @param string        $haystackAttributeName
     * @param string|object $haystackClassOrObject
     * @param string        $message
     */
    public static function assertAttributeEmpty($haystackAttributeName, $haystackClassOrObject, $message = '')
    {
        static::assertEmpty(
            static::readAttribute($haystackClassOrObject, $haystackAttributeName),
            $message
        );
    }

    /**
     * Asserts that a variable is not empty.
     *
     * @param mixed  $actual
     * @param string $message
     *
     * @throws AssertionFailedError
     */
    public static function assertNotEmpty($actual, $message = '')
    {
        static::assertThat($actual, static::logicalNot(static::isEmpty()), $message);
    }

    /**
     * Asserts that a static attribute of a class or an attribute of an object
     * is not empty.
     *
     * @param string        $haystackAttributeName
     * @param string|object $haystackClassOrObject
     * @param string        $message
     */
    public static function assertAttributeNotEmpty($haystackAttributeName, $haystackClassOrObject, $message = '')
    {
        static::assertNotEmpty(
            static::readAttribute($haystackClassOrObject, $haystackAttributeName),
            $message
        );
    }

    /**
     * Asserts that a value is greater than another value.
     *
     * @param mixed  $expected
     * @param mixed  $actual
     * @param string $message
     */
    public static function assertGreaterThan($expected, $actual, $message = '')
    {
        static::assertThat($actual, static::greaterThan($expected), $message);
    }

    /**
     * Asserts that an attribute is greater than another value.
     *
     * @param mixed         $expected
     * @param string        $actualAttributeName
     * @param string|object $actualClassOrObject
     * @param string        $message
     */
    public static function assertAttributeGreaterThan($expected, $actualAttributeName, $actualClassOrObject, $message = '')
    {
        static::assertGreaterThan(
            $expected,
            static::readAttribute($actualClassOrObject, $actualAttributeName),
            $message
        );
    }

    /**
     * Asserts that a value is greater than or equal to another value.
     *
     * @param mixed  $expected
     * @param mixed  $actual
     * @param string $message
     */
    public static function assertGreaterThanOrEqual($expected, $actual, $message = '')
    {
        static::assertThat(
            $actual,
            static::greaterThanOrEqual($expected),
            $message
        );
    }

    /**
     * Asserts that an attribute is greater than or equal to another value.
     *
     * @param mixed         $expected
     * @param string        $actualAttributeName
     * @param string|object $actualClassOrObject
     * @param string        $message
     */
    public static function assertAttributeGreaterThanOrEqual($expected, $actualAttributeName, $actualClassOrObject, $message = '')
    {
        static::assertGreaterThanOrEqual(
            $expected,
            static::readAttribute($actualClassOrObject, $actualAttributeName),
            $message
        );
    }

    /**
     * Asserts that a value is smaller than another value.
     *
     * @param mixed  $expected
     * @param mixed  $actual
     * @param string $message
     */
    public static function assertLessThan($expected, $actual, $message = '')
    {
        static::assertThat($actual, static::lessThan($expected), $message);
    }

    /**
     * Asserts that an attribute is smaller than another value.
     *
     * @param mixed         $expected
     * @param string        $actualAttributeName
     * @param string|object $actualClassOrObject
     * @param string        $message
     */
    public static function assertAttributeLessThan($expected, $actualAttributeName, $actualClassOrObject, $message = '')
    {
        static::assertLessThan(
            $expected,
            static::readAttribute($actualClassOrObject, $actualAttributeName),
            $message
        );
    }

    /**
     * Asserts that a value is smaller than or equal to another value.
     *
     * @param mixed  $expected
     * @param mixed  $actual
     * @param string $message
     */
    public static function assertLessThanOrEqual($expected, $actual, $message = '')
    {
        static::assertThat($actual, static::lessThanOrEqual($expected), $message);
    }

    /**
     * Asserts that an attribute is smaller than or equal to another value.
     *
     * @param mixed         $expected
     * @param string        $actualAttributeName
     * @param string|object $actualClassOrObject
     * @param string        $message
     */
    public static function assertAttributeLessThanOrEqual($expected, $actualAttributeName, $actualClassOrObject, $message = '')
    {
        static::assertLessThanOrEqual(
            $expected,
            static::readAttribute($actualClassOrObject, $actualAttributeName),
            $message
        );
    }

    /**
     * Asserts that the contents of one file is equal to the contents of another
     * file.
     *
     * @param string $expected
     * @param string $actual
     * @param string $message
     * @param bool   $canonicalize
     * @param bool   $ignoreCase
     */
    public static function assertFileEquals($expected, $actual, $message = '', $canonicalize = false, $ignoreCase = false)
    {
        static::assertFileExists($expected, $message);
        static::assertFileExists($actual, $message);

        static::assertEquals(
            \file_get_contents($expected),
            \file_get_contents($actual),
            $message,
            0,
            10,
            $canonicalize,
            $ignoreCase
        );
    }

    /**
     * Asserts that the contents of one file is not equal to the contents of
     * another file.
     *
     * @param string $expected
     * @param string $actual
     * @param string $message
     * @param bool   $canonicalize
     * @param bool   $ignoreCase
     */
    public static function assertFileNotEquals($expected, $actual, $message = '', $canonicalize = false, $ignoreCase = false)
    {
        static::assertFileExists($expected, $message);
        static::assertFileExists($actual, $message);

        static::assertNotEquals(
            \file_get_contents($expected),
            \file_get_contents($actual),
            $message,
            0,
            10,
            $canonicalize,
            $ignoreCase
        );
    }

    /**
     * Asserts that the contents of a string is equal
     * to the contents of a file.
     *
     * @param string $expectedFile
     * @param string $actualString
     * @param string $message
     * @param bool   $canonicalize
     * @param bool   $ignoreCase
     */
    public static function assertStringEqualsFile($expectedFile, $actualString, $message = '', $canonicalize = false, $ignoreCase = false)
    {
        static::assertFileExists($expectedFile, $message);

        static::assertEquals(
            \file_get_contents($expectedFile),
            $actualString,
            $message,
            0,
            10,
            $canonicalize,
            $ignoreCase
        );
    }

    /**
     * Asserts that the contents of a string is not equal
     * to the contents of a file.
     *
     * @param string $expectedFile
     * @param string $actualString
     * @param string $message
     * @param bool   $canonicalize
     * @param bool   $ignoreCase
     */
    public static function assertStringNotEqualsFile($expectedFile, $actualString, $message = '', $canonicalize = false, $ignoreCase = false)
    {
        static::assertFileExists($expectedFile, $message);

        static::assertNotEquals(
            \file_get_contents($expectedFile),
            $actualString,
            $message,
            0,
            10,
            $canonicalize,
            $ignoreCase
        );
    }

    /**
     * Asserts that a file/dir is readable.
     *
     * @param string $filename
     * @param string $message
     */
    public static function assertIsReadable($filename, $message = '')
    {
        if (!\is_string($filename)) {
            throw InvalidArgumentHelper::factory(1, 'string');
        }

        $constraint = new IsReadable;

        static::assertThat($filename, $constraint, $message);
    }

    /**
     * Asserts that a file/dir exists and is not readable.
     *
     * @param string $filename
     * @param string $message
     */
    public static function assertNotIsReadable($filename, $message = '')
    {
        if (!\is_string($filename)) {
            throw InvalidArgumentHelper::factory(1, 'string');
        }

        $constraint = new LogicalNot(
            new IsReadable
        );

        static::assertThat($filename, $constraint, $message);
    }

    /**
     * Asserts that a file/dir exists and is writable.
     *
     * @param string $filename
     * @param string $message
     */
    public static function assertIsWritable($filename, $message = '')
    {
        if (!\is_string($filename)) {
            throw InvalidArgumentHelper::factory(1, 'string');
        }

        $constraint = new IsWritable;

        static::assertThat($filename, $constraint, $message);
    }

    /**
     * Asserts that a file/dir exists and is not writable.
     *
     * @param string $filename
     * @param string $message
     */
    public static function assertNotIsWritable($filename, $message = '')
    {
        if (!\is_string($filename)) {
            throw InvalidArgumentHelper::factory(1, 'string');
        }

        $constraint = new LogicalNot(
            new IsWritable
        );

        static::assertThat($filename, $constraint, $message);
    }

    /**
     * Asserts that a directory exists.
     *
     * @param string $directory
     * @param string $message
     */
    public static function assertDirectoryExists($directory, $message = '')
    {
        if (!\is_string($directory)) {
            throw InvalidArgumentHelper::factory(1, 'string');
        }

        $constraint = new DirectoryExists;

        static::assertThat($directory, $constraint, $message);
    }

    /**
     * Asserts that a directory does not exist.
     *
     * @param string $directory
     * @param string $message
     */
    public static function assertDirectoryNotExists($directory, $message = '')
    {
        if (!\is_string($directory)) {
            throw InvalidArgumentHelper::factory(1, 'string');
        }

        $constraint = new LogicalNot(
            new DirectoryExists
        );

        static::assertThat($directory, $constraint, $message);
    }

    /**
     * Asserts that a directory exists and is readable.
     *
     * @param string $directory
     * @param string $message
     */
    public static function assertDirectoryIsReadable($directory, $message = '')
    {
        self::assertDirectoryExists($directory, $message);
        self::assertIsReadable($directory, $message);
    }

    /**
     * Asserts that a directory exists and is not readable.
     *
     * @param string $directory
     * @param string $message
     */
    public static function assertDirectoryNotIsReadable($directory, $message = '')
    {
        self::assertDirectoryExists($directory, $message);
        self::assertNotIsReadable($directory, $message);
    }

    /**
     * Asserts that a directory exists and is writable.
     *
     * @param string $directory
     * @param string $message
     */
    public static function assertDirectoryIsWritable($directory, $message = '')
    {
        self::assertDirectoryExists($directory, $message);
        self::assertIsWritable($directory, $message);
    }

    /**
     * Asserts that a directory exists and is not writable.
     *
     * @param string $directory
     * @param string $message
     */
    public static function assertDirectoryNotIsWritable($directory, $message = '')
    {
        self::assertDirectoryExists($directory, $message);
        self::assertNotIsWritable($directory, $message);
    }

    /**
     * Asserts that a file exists.
     *
     * @param string $filename
     * @param string $message
     */
    public static function assertFileExists($filename, $message = '')
    {
        if (!\is_string($filename)) {
            throw InvalidArgumentHelper::factory(1, 'string');
        }

        $constraint = new FileExists;

        static::assertThat($filename, $constraint, $message);
    }

    /**
     * Asserts that a file does not exist.
     *
     * @param string $filename
     * @param string $message
     */
    public static function assertFileNotExists($filename, $message = '')
    {
        if (!\is_string($filename)) {
            throw InvalidArgumentHelper::factory(1, 'string');
        }

        $constraint = new LogicalNot(
            new FileExists
        );

        static::assertThat($filename, $constraint, $message);
    }

    /**
     * Asserts that a file exists and is readable.
     *
     * @param string $file
     * @param string $message
     */
    public static function assertFileIsReadable($file, $message = '')
    {
        self::assertFileExists($file, $message);
        self::assertIsReadable($file, $message);
    }

    /**
     * Asserts that a file exists and is not readable.
     *
     * @param string $file
     * @param string $message
     */
    public static function assertFileNotIsReadable($file, $message = '')
    {
        self::assertFileExists($file, $message);
        self::assertNotIsReadable($file, $message);
    }

    /**
     * Asserts that a file exists and is writable.
     *
     * @param string $file
     * @param string $message
     */
    public static function assertFileIsWritable($file, $message = '')
    {
        self::assertFileExists($file, $message);
        self::assertIsWritable($file, $message);
    }

    /**
     * Asserts that a file exists and is not writable.
     *
     * @param string $file
     * @param string $message
     */
    public static function assertFileNotIsWritable($file, $message = '')
    {
        self::assertFileExists($file, $message);
        self::assertNotIsWritable($file, $message);
    }

    /**
     * Asserts that a condition is true.
     *
     * @param bool   $condition
     * @param string $message
     *
     * @throws AssertionFailedError
     */
    public static function assertTrue($condition, $message = '')
    {
        static::assertThat($condition, static::isTrue(), $message);
    }

    /**
     * Asserts that a condition is not true.
     *
     * @param bool   $condition
     * @param string $message
     *
     * @throws AssertionFailedError
     */
    public static function assertNotTrue($condition, $message = '')
    {
        static::assertThat($condition, static::logicalNot(static::isTrue()), $message);
    }

    /**
     * Asserts that a condition is false.
     *
     * @param bool   $condition
     * @param string $message
     *
     * @throws AssertionFailedError
     */
    public static function assertFalse($condition, $message = '')
    {
        static::assertThat($condition, static::isFalse(), $message);
    }

    /**
     * Asserts that a condition is not false.
     *
     * @param bool   $condition
     * @param string $message
     *
     * @throws AssertionFailedError
     */
    public static function assertNotFalse($condition, $message = '')
    {
        static::assertThat($condition, static::logicalNot(static::isFalse()), $message);
    }

    /**
     * Asserts that a variable is null.
     *
     * @param mixed  $actual
     * @param string $message
     */
    public static function assertNull($actual, $message = '')
    {
        static::assertThat($actual, static::isNull(), $message);
    }

    /**
     * Asserts that a variable is not null.
     *
     * @param mixed  $actual
     * @param string $message
     */
    public static function assertNotNull($actual, $message = '')
    {
        static::assertThat($actual, static::logicalNot(static::isNull()), $message);
    }

    /**
     * Asserts that a variable is finite.
     *
     * @param mixed  $actual
     * @param string $message
     */
    public static function assertFinite($actual, $message = '')
    {
        static::assertThat($actual, static::isFinite(), $message);
    }

    /**
     * Asserts that a variable is infinite.
     *
     * @param mixed  $actual
     * @param string $message
     */
    public static function assertInfinite($actual, $message = '')
    {
        static::assertThat($actual, static::isInfinite(), $message);
    }

    /**
     * Asserts that a variable is nan.
     *
     * @param mixed  $actual
     * @param string $message
     */
    public static function assertNan($actual, $message = '')
    {
        static::assertThat($actual, static::isNan(), $message);
    }

    /**
     * Asserts that a class has a specified attribute.
     *
     * @param string $attributeName
     * @param string $className
     * @param string $message
     */
    public static function assertClassHasAttribute($attributeName, $className, $message = '')
    {
        if (!\is_string($attributeName)) {
            throw InvalidArgumentHelper::factory(1, 'string');
        }

        if (!\preg_match('/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/', $attributeName)) {
            throw InvalidArgumentHelper::factory(1, 'valid attribute name');
        }

        if (!\is_string($className) || !\class_exists($className)) {
            throw InvalidArgumentHelper::factory(2, 'class name', $className);
        }

        $constraint = new ClassHasAttribute(
            $attributeName
        );

        static::assertThat($className, $constraint, $message);
    }

    /**
     * Asserts that a class does not have a specified attribute.
     *
     * @param string $attributeName
     * @param string $className
     * @param string $message
     */
    public static function assertClassNotHasAttribute($attributeName, $className, $message = '')
    {
        if (!\is_string($attributeName)) {
            throw InvalidArgumentHelper::factory(1, 'string');
        }

        if (!\preg_match('/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/', $attributeName)) {
            throw InvalidArgumentHelper::factory(1, 'valid attribute name');
        }

        if (!\is_string($className) || !\class_exists($className)) {
            throw InvalidArgumentHelper::factory(2, 'class name', $className);
        }

        $constraint = new LogicalNot(
            new ClassHasAttribute($attributeName)
        );

        static::assertThat($className, $constraint, $message);
    }

    /**
     * Asserts that a class has a specified static attribute.
     *
     * @param string $attributeName
     * @param string $className
     * @param string $message
     */
    public static function assertClassHasStaticAttribute($attributeName, $className, $message = '')
    {
        if (!\is_string($attributeName)) {
            throw InvalidArgumentHelper::factory(1, 'string');
        }

        if (!\preg_match('/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/', $attributeName)) {
            throw InvalidArgumentHelper::factory(1, 'valid attribute name');
        }

        if (!\is_string($className) || !\class_exists($className)) {
            throw InvalidArgumentHelper::factory(2, 'class name', $className);
        }

        $constraint = new ClassHasStaticAttribute(
            $attributeName
        );

        static::assertThat($className, $constraint, $message);
    }

    /**
     * Asserts that a class does not have a specified static attribute.
     *
     * @param string $attributeName
     * @param string $className
     * @param string $message
     */
    public static function assertClassNotHasStaticAttribute($attributeName, $className, $message = '')
    {
        if (!\is_string($attributeName)) {
            throw InvalidArgumentHelper::factory(1, 'string');
        }

        if (!\preg_match('/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/', $attributeName)) {
            throw InvalidArgumentHelper::factory(1, 'valid attribute name');
        }

        if (!\is_string($className) || !\class_exists($className)) {
            throw InvalidArgumentHelper::factory(2, 'class name', $className);
        }

        $constraint = new LogicalNot(
            new ClassHasStaticAttribute(
                $attributeName
            )
        );

        static::assertThat($className, $constraint, $message);
    }

    /**
     * Asserts that an object has a specified attribute.
     *
     * @param string $attributeName
     * @param object $object
     * @param string $message
     */
    public static function assertObjectHasAttribute($attributeName, $object, $message = '')
    {
        if (!\is_string($attributeName)) {
            throw InvalidArgumentHelper::factory(1, 'string');
        }

        if (!\preg_match('/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/', $attributeName)) {
            throw InvalidArgumentHelper::factory(1, 'valid attribute name');
        }

        if (!\is_object($object)) {
            throw InvalidArgumentHelper::factory(2, 'object');
        }

        $constraint = new ObjectHasAttribute(
            $attributeName
        );

        static::assertThat($object, $constraint, $message);
    }

    /**
     * Asserts that an object does not have a specified attribute.
     *
     * @param string $attributeName
     * @param object $object
     * @param string $message
     */
    public static function assertObjectNotHasAttribute($attributeName, $object, $message = '')
    {
        if (!\is_string($attributeName)) {
            throw InvalidArgumentHelper::factory(1, 'string');
        }

        if (!\preg_match('/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/', $attributeName)) {
            throw InvalidArgumentHelper::factory(1, 'valid attribute name');
        }

        if (!\is_object($object)) {
            throw InvalidArgumentHelper::factory(2, 'object');
        }

        $constraint = new LogicalNot(
            new ObjectHasAttribute($attributeName)
        );

        static::assertThat($object, $constraint, $message);
    }

    /**
     * Asserts that two variables have the same type and value.
     * Used on objects, it asserts that two variables reference
     * the same object.
     *
     * @param mixed  $expected
     * @param mixed  $actual
     * @param string $message
     */
    public static function assertSame($expected, $actual, $message = '')
    {
        if (\is_bool($expected) && \is_bool($actual)) {
            static::assertEquals($expected, $actual, $message);
        } else {
            $constraint = new IsIdentical(
                $expected
            );

            static::assertThat($actual, $constraint, $message);
        }
    }

    /**
     * Asserts that a variable and an attribute of an object have the same type
     * and value.
     *
     * @param mixed         $expected
     * @param string        $actualAttributeName
     * @param string|object $actualClassOrObject
     * @param string        $message
     */
    public static function assertAttributeSame($expected, $actualAttributeName, $actualClassOrObject, $message = '')
    {
        static::assertSame(
            $expected,
            static::readAttribute($actualClassOrObject, $actualAttributeName),
            $message
        );
    }

    /**
     * Asserts that two variables do not have the same type and value.
     * Used on objects, it asserts that two variables do not reference
     * the same object.
     *
     * @param mixed  $expected
     * @param mixed  $actual
     * @param string $message
     */
    public static function assertNotSame($expected, $actual, $message = '')
    {
        if (\is_bool($expected) && \is_bool($actual)) {
            static::assertNotEquals($expected, $actual, $message);
        } else {
            $constraint = new LogicalNot(
                new IsIdentical($expected)
            );

            static::assertThat($actual, $constraint, $message);
        }
    }

    /**
     * Asserts that a variable and an attribute of an object do not have the
     * same type and value.
     *
     * @param mixed         $expected
     * @param string        $actualAttributeName
     * @param string|object $actualClassOrObject
     * @param string        $message
     */
    public static function assertAttributeNotSame($expected, $actualAttributeName, $actualClassOrObject, $message = '')
    {
        static::assertNotSame(
            $expected,
            static::readAttribute($actualClassOrObject, $actualAttributeName),
            $message
        );
    }

    /**
     * Asserts that a variable is of a given type.
     *
     * @param string $expected
     * @param mixed  $actual
     * @param string $message
     */
    public static function assertInstanceOf($expected, $actual, $message = '')
    {
        if (!(\is_string($expected) && (\class_exists($expected) || \interface_exists($expected)))) {
            throw InvalidArgumentHelper::factory(1, 'class or interface name');
        }

        $constraint = new IsInstanceOf(
            $expected
        );

        static::assertThat($actual, $constraint, $message);
    }

    /**
     * Asserts that an attribute is of a given type.
     *
     * @param string        $expected
     * @param string        $attributeName
     * @param string|object $classOrObject
     * @param string        $message
     */
    public static function assertAttributeInstanceOf($expected, $attributeName, $classOrObject, $message = '')
    {
        static::assertInstanceOf(
            $expected,
            static::readAttribute($classOrObject, $attributeName),
            $message
        );
    }

    /**
     * Asserts that a variable is not of a given type.
     *
     * @param string $expected
     * @param mixed  $actual
     * @param string $message
     */
    public static function assertNotInstanceOf($expected, $actual, $message = '')
    {
        if (!(\is_string($expected) && (\class_exists($expected) || \interface_exists($expected)))) {
            throw InvalidArgumentHelper::factory(1, 'class or interface name');
        }

        $constraint = new LogicalNot(
            new IsInstanceOf($expected)
        );

        static::assertThat($actual, $constraint, $message);
    }

    /**
     * Asserts that an attribute is of a given type.
     *
     * @param string        $expected
     * @param string        $attributeName
     * @param string|object $classOrObject
     * @param string        $message
     */
    public static function assertAttributeNotInstanceOf($expected, $attributeName, $classOrObject, $message = '')
    {
        static::assertNotInstanceOf(
            $expected,
            static::readAttribute($classOrObject, $attributeName),
            $message
        );
    }

    /**
     * Asserts that a variable is of a given type.
     *
     * @param string $expected
     * @param mixed  $actual
     * @param string $message
     */
    public static function assertInternalType($expected, $actual, $message = '')
    {
        if (!\is_string($expected)) {
            throw InvalidArgumentHelper::factory(1, 'string');
        }

        $constraint = new IsType(
            $expected
        );

        static::assertThat($actual, $constraint, $message);
    }

    /**
     * Asserts that an attribute is of a given type.
     *
     * @param string        $expected
     * @param string        $attributeName
     * @param string|object $classOrObject
     * @param string        $message
     */
    public static function assertAttributeInternalType($expected, $attributeName, $classOrObject, $message = '')
    {
        static::assertInternalType(
            $expected,
            static::readAttribute($classOrObject, $attributeName),
            $message
        );
    }

    /**
     * Asserts that a variable is not of a given type.
     *
     * @param string $expected
     * @param mixed  $actual
     * @param string $message
     */
    public static function assertNotInternalType($expected, $actual, $message = '')
    {
        if (!\is_string($expected)) {
            throw InvalidArgumentHelper::factory(1, 'string');
        }

        $constraint = new LogicalNot(
            new IsType($expected)
        );

        static::assertThat($actual, $constraint, $message);
    }

    /**
     * Asserts that an attribute is of a given type.
     *
     * @param string        $expected
     * @param string        $attributeName
     * @param string|object $classOrObject
     * @param string        $message
     */
    public static function assertAttributeNotInternalType($expected, $attributeName, $classOrObject, $message = '')
    {
        static::assertNotInternalType(
            $expected,
            static::readAttribute($classOrObject, $attributeName),
            $message
        );
    }

    /**
     * Asserts that a string matches a given regular expression.
     *
     * @param string $pattern
     * @param string $string
     * @param string $message
     */
    public static function assertRegExp($pattern, $string, $message = '')
    {
        if (!\is_string($pattern)) {
            throw InvalidArgumentHelper::factory(1, 'string');
        }

        if (!\is_string($string)) {
            throw InvalidArgumentHelper::factory(2, 'string');
        }

        $constraint = new RegularExpression($pattern);

        static::assertThat($string, $constraint, $message);
    }

    /**
     * Asserts that a string does not match a given regular expression.
     *
     * @param string $pattern
     * @param string $string
     * @param string $message
     */
    public static function assertNotRegExp($pattern, $string, $message = '')
    {
        if (!\is_string($pattern)) {
            throw InvalidArgumentHelper::factory(1, 'string');
        }

        if (!\is_string($string)) {
            throw InvalidArgumentHelper::factory(2, 'string');
        }

        $constraint = new LogicalNot(
            new RegularExpression($pattern)
        );

        static::assertThat($string, $constraint, $message);
    }

    /**
     * Assert that the size of two arrays (or `Countable` or `Traversable` objects)
     * is the same.
     *
     * @param array|Countable|Traversable $expected
     * @param array|Countable|Traversable $actual
     * @param string                      $message
     */
    public static function assertSameSize($expected, $actual, $message = '')
    {
        if (!$expected instanceof Countable &&
            !$expected instanceof Traversable &&
            !\is_array($expected)) {
            throw InvalidArgumentHelper::factory(1, 'countable or traversable');
        }

        if (!$actual instanceof Countable &&
            !$actual instanceof Traversable &&
            !\is_array($actual)) {
            throw InvalidArgumentHelper::factory(2, 'countable or traversable');
        }

        static::assertThat(
            $actual,
            new SameSize($expected),
            $message
        );
    }

    /**
     * Assert that the size of two arrays (or `Countable` or `Traversable` objects)
     * is not the same.
     *
     * @param array|Countable|Traversable $expected
     * @param array|Countable|Traversable $actual
     * @param string                      $message
     */
    public static function assertNotSameSize($expected, $actual, $message = '')
    {
        if (!$expected instanceof Countable &&
            !$expected instanceof Traversable &&
            !\is_array($expected)) {
            throw InvalidArgumentHelper::factory(1, 'countable or traversable');
        }

        if (!$actual instanceof Countable &&
            !$actual instanceof Traversable &&
            !\is_array($actual)) {
            throw InvalidArgumentHelper::factory(2, 'countable or traversable');
        }

        $constraint = new LogicalNot(
            new SameSize($expected)
        );

        static::assertThat($actual, $constraint, $message);
    }

    /**
     * Asserts that a string matches a given format string.
     *
     * @param string $format
     * @param string $string
     * @param string $message
     */
    public static function assertStringMatchesFormat($format, $string, $message = '')
    {
        if (!\is_string($format)) {
            throw InvalidArgumentHelper::factory(1, 'string');
        }

        if (!\is_string($string)) {
            throw InvalidArgumentHelper::factory(2, 'string');
        }

        $constraint = new StringMatchesFormatDescription($format);

        static::assertThat($string, $constraint, $message);
    }

    /**
     * Asserts that a string does not match a given format string.
     *
     * @param string $format
     * @param string $string
     * @param string $message
     */
    public static function assertStringNotMatchesFormat($format, $string, $message = '')
    {
        if (!\is_string($format)) {
            throw InvalidArgumentHelper::factory(1, 'string');
        }

        if (!\is_string($string)) {
            throw InvalidArgumentHelper::factory(2, 'string');
        }

        $constraint = new LogicalNot(
            new StringMatchesFormatDescription($format)
        );

        static::assertThat($string, $constraint, $message);
    }

    /**
     * Asserts that a string matches a given format file.
     *
     * @param string $formatFile
     * @param string $string
     * @param string $message
     */
    public static function assertStringMatchesFormatFile($formatFile, $string, $message = '')
    {
        static::assertFileExists($formatFile, $message);

        if (!\is_string($string)) {
            throw InvalidArgumentHelper::factory(2, 'string');
        }

        $constraint = new StringMatchesFormatDescription(
            \file_get_contents($formatFile)
        );

        static::assertThat($string, $constraint, $message);
    }

    /**
     * Asserts that a string does not match a given format string.
     *
     * @param string $formatFile
     * @param string $string
     * @param string $message
     */
    public static function assertStringNotMatchesFormatFile($formatFile, $string, $message = '')
    {
        static::assertFileExists($formatFile, $message);

        if (!\is_string($string)) {
            throw InvalidArgumentHelper::factory(2, 'string');
        }

        $constraint = new LogicalNot(
            new StringMatchesFormatDescription(
                \file_get_contents($formatFile)
            )
        );

        static::assertThat($string, $constraint, $message);
    }

    /**
     * Asserts that a string starts with a given prefix.
     *
     * @param string $prefix
     * @param string $string
     * @param string $message
     */
    public static function assertStringStartsWith($prefix, $string, $message = '')
    {
        if (!\is_string($prefix)) {
            throw InvalidArgumentHelper::factory(1, 'string');
        }

        if (!\is_string($string)) {
            throw InvalidArgumentHelper::factory(2, 'string');
        }

        $constraint = new StringStartsWith(
            $prefix
        );

        static::assertThat($string, $constraint, $message);
    }

    /**
     * Asserts that a string starts not with a given prefix.
     *
     * @param string $prefix
     * @param string $string
     * @param string $message
     */
    public static function assertStringStartsNotWith($prefix, $string, $message = '')
    {
        if (!\is_string($prefix)) {
            throw InvalidArgumentHelper::factory(1, 'string');
        }

        if (!\is_string($string)) {
            throw InvalidArgumentHelper::factory(2, 'string');
        }

        $constraint = new LogicalNot(
            new StringStartsWith($prefix)
        );

        static::assertThat($string, $constraint, $message);
    }

    /**
     * Asserts that a string ends with a given suffix.
     *
     * @param string $suffix
     * @param string $string
     * @param string $message
     */
    public static function assertStringEndsWith($suffix, $string, $message = '')
    {
        if (!\is_string($suffix)) {
            throw InvalidArgumentHelper::factory(1, 'string');
        }

        if (!\is_string($string)) {
            throw InvalidArgumentHelper::factory(2, 'string');
        }

        $constraint = new StringEndsWith($suffix);

        static::assertThat($string, $constraint, $message);
    }

    /**
     * Asserts that a string ends not with a given suffix.
     *
     * @param string $suffix
     * @param string $string
     * @param string $message
     */
    public static function assertStringEndsNotWith($suffix, $string, $message = '')
    {
        if (!\is_string($suffix)) {
            throw InvalidArgumentHelper::factory(1, 'string');
        }

        if (!\is_string($string)) {
            throw InvalidArgumentHelper::factory(2, 'string');
        }

        $constraint = new LogicalNot(
            new StringEndsWith($suffix)
        );

        static::assertThat($string, $constraint, $message);
    }

    /**
     * Asserts that two XML files are equal.
     *
     * @param string $expectedFile
     * @param string $actualFile
     * @param string $message
     */
    public static function assertXmlFileEqualsXmlFile($expectedFile, $actualFile, $message = '')
    {
        $expected = Xml::loadFile($expectedFile);
        $actual   = Xml::loadFile($actualFile);

        static::assertEquals($expected, $actual, $message);
    }

    /**
     * Asserts that two XML files are not equal.
     *
     * @param string $expectedFile
     * @param string $actualFile
     * @param string $message
     */
    public static function assertXmlFileNotEqualsXmlFile($expectedFile, $actualFile, $message = '')
    {
        $expected = Xml::loadFile($expectedFile);
        $actual   = Xml::loadFile($actualFile);

        static::assertNotEquals($expected, $actual, $message);
    }

    /**
     * Asserts that two XML documents are equal.
     *
     * @param string             $expectedFile
     * @param string|DOMDocument $actualXml
     * @param string             $message
     */
    public static function assertXmlStringEqualsXmlFile($expectedFile, $actualXml, $message = '')
    {
        $expected = Xml::loadFile($expectedFile);
        $actual   = Xml::load($actualXml);

        static::assertEquals($expected, $actual, $message);
    }

    /**
     * Asserts that two XML documents are not equal.
     *
     * @param string             $expectedFile
     * @param string|DOMDocument $actualXml
     * @param string             $message
     */
    public static function assertXmlStringNotEqualsXmlFile($expectedFile, $actualXml, $message = '')
    {
        $expected = Xml::loadFile($expectedFile);
        $actual   = Xml::load($actualXml);

        static::assertNotEquals($expected, $actual, $message);
    }

    /**
     * Asserts that two XML documents are equal.
     *
     * @param string|DOMDocument $expectedXml
     * @param string|DOMDocument $actualXml
     * @param string             $message
     */
    public static function assertXmlStringEqualsXmlString($expectedXml, $actualXml, $message = '')
    {
        $expected = Xml::load($expectedXml);
        $actual   = Xml::load($actualXml);

        static::assertEquals($expected, $actual, $message);
    }

    /**
     * Asserts that two XML documents are not equal.
     *
     * @param string|DOMDocument $expectedXml
     * @param string|DOMDocument $actualXml
     * @param string             $message
     */
    public static function assertXmlStringNotEqualsXmlString($expectedXml, $actualXml, $message = '')
    {
        $expected = Xml::load($expectedXml);
        $actual   = Xml::load($actualXml);

        static::assertNotEquals($expected, $actual, $message);
    }

    /**
     * Asserts that a hierarchy of DOMElements matches.
     *
     * @param DOMElement $expectedElement
     * @param DOMElement $actualElement
     * @param bool       $checkAttributes
     * @param string     $message
     */
    public static function assertEqualXMLStructure(DOMElement $expectedElement, DOMElement $actualElement, $checkAttributes = false, $message = '')
    {
        $tmp             = new DOMDocument;
        $expectedElement = $tmp->importNode($expectedElement, true);

        $tmp           = new DOMDocument;
        $actualElement = $tmp->importNode($actualElement, true);

        unset($tmp);

        static::assertEquals(
            $expectedElement->tagName,
            $actualElement->tagName,
            $message
        );

        if ($checkAttributes) {
            static::assertEquals(
                $expectedElement->attributes->length,
                $actualElement->attributes->length,
                \sprintf(
                    '%s%sNumber of attributes on node "%s" does not match',
                    $message,
                    !empty($message) ? "\n" : '',
                    $expectedElement->tagName
                )
            );

            for ($i = 0; $i < $expectedElement->attributes->length; $i++) {
                $expectedAttribute = $expectedElement->attributes->item($i);
                $actualAttribute   = $actualElement->attributes->getNamedItem(
                    $expectedAttribute->name
                );

                if (!$actualAttribute) {
                    static::fail(
                        \sprintf(
                            '%s%sCould not find attribute "%s" on node "%s"',
                            $message,
                            !empty($message) ? "\n" : '',
                            $expectedAttribute->name,
                            $expectedElement->tagName
                        )
                    );
                }
            }
        }

        Xml::removeCharacterDataNodes($expectedElement);
        Xml::removeCharacterDataNodes($actualElement);

        static::assertEquals(
            $expectedElement->childNodes->length,
            $actualElement->childNodes->length,
            \sprintf(
                '%s%sNumber of child nodes of "%s" differs',
                $message,
                !empty($message) ? "\n" : '',
                $expectedElement->tagName
            )
        );

        for ($i = 0; $i < $expectedElement->childNodes->length; $i++) {
            static::assertEqualXMLStructure(
                $expectedElement->childNodes->item($i),
                $actualElement->childNodes->item($i),
                $checkAttributes,
                $message
            );
        }
    }

    /**
     * Evaluates a PHPUnit\Framework\Constraint matcher object.
     *
     * @param mixed      $value
     * @param Constraint $constraint
     * @param string     $message
     */
    public static function assertThat($value, Constraint $constraint, $message = '')
    {
        self::$count += \count($constraint);

        $constraint->evaluate($value, $message);
    }

    /**
     * Asserts that a string is a valid JSON string.
     *
     * @param string $actualJson
     * @param string $message
     */
    public static function assertJson($actualJson, $message = '')
    {
        if (!\is_string($actualJson)) {
            throw InvalidArgumentHelper::factory(1, 'string');
        }

        static::assertThat($actualJson, static::isJson(), $message);
    }

    /**
     * Asserts that two given JSON encoded objects or arrays are equal.
     *
     * @param string $expectedJson
     * @param string $actualJson
     * @param string $message
     */
    public static function assertJsonStringEqualsJsonString($expectedJson, $actualJson, $message = '')
    {
        static::assertJson($expectedJson, $message);
        static::assertJson($actualJson, $message);

        $constraint = new JsonMatches(
            $expectedJson
        );

        static::assertThat($actualJson, $constraint, $message);
    }

    /**
     * Asserts that two given JSON encoded objects or arrays are not equal.
     *
     * @param string $expectedJson
     * @param string $actualJson
     * @param string $message
     */
    public static function assertJsonStringNotEqualsJsonString($expectedJson, $actualJson, $message = '')
    {
        static::assertJson($expectedJson, $message);
        static::assertJson($actualJson, $message);

        $constraint = new JsonMatches(
            $expectedJson
        );

        static::assertThat($actualJson, new LogicalNot($constraint), $message);
    }

    /**
     * Asserts that the generated JSON encoded object and the content of the given file are equal.
     *
     * @param string $expectedFile
     * @param string $actualJson
     * @param string $message
     */
    public static function assertJsonStringEqualsJsonFile($expectedFile, $actualJson, $message = '')
    {
        static::assertFileExists($expectedFile, $message);
        $expectedJson = \file_get_contents($expectedFile);

        static::assertJson($expectedJson, $message);
        static::assertJson($actualJson, $message);

        $constraint = new JsonMatches(
            $expectedJson
        );

        static::assertThat($actualJson, $constraint, $message);
    }

    /**
     * Asserts that the generated JSON encoded object and the content of the given file are not equal.
     *
     * @param string $expectedFile
     * @param string $actualJson
     * @param string $message
     */
    public static function assertJsonStringNotEqualsJsonFile($expectedFile, $actualJson, $message = '')
    {
        static::assertFileExists($expectedFile, $message);
        $expectedJson = \file_get_contents($expectedFile);

        static::assertJson($expectedJson, $message);
        static::assertJson($actualJson, $message);

        $constraint = new JsonMatches(
            $expectedJson
        );

        static::assertThat($actualJson, new LogicalNot($constraint), $message);
    }

    /**
     * Asserts that two JSON files are equal.
     *
     * @param string $expectedFile
     * @param string $actualFile
     * @param string $message
     */
    public static function assertJsonFileEqualsJsonFile($expectedFile, $actualFile, $message = '')
    {
        static::assertFileExists($expectedFile, $message);
        static::assertFileExists($actualFile, $message);

        $actualJson   = \file_get_contents($actualFile);
        $expectedJson = \file_get_contents($expectedFile);

        static::assertJson($expectedJson, $message);
        static::assertJson($actualJson, $message);

        $constraintExpected = new JsonMatches(
            $expectedJson
        );

        $constraintActual = new JsonMatches($actualJson);

        static::assertThat($expectedJson, $constraintActual, $message);
        static::assertThat($actualJson, $constraintExpected, $message);
    }

    /**
     * Asserts that two JSON files are not equal.
     *
     * @param string $expectedFile
     * @param string $actualFile
     * @param string $message
     */
    public static function assertJsonFileNotEqualsJsonFile($expectedFile, $actualFile, $message = '')
    {
        static::assertFileExists($expectedFile, $message);
        static::assertFileExists($actualFile, $message);

        $actualJson   = \file_get_contents($actualFile);
        $expectedJson = \file_get_contents($expectedFile);

        static::assertJson($expectedJson, $message);
        static::assertJson($actualJson, $message);

        $constraintExpected = new JsonMatches(
            $expectedJson
        );

        $constraintActual = new JsonMatches($actualJson);

        static::assertThat($expectedJson, new LogicalNot($constraintActual), $message);
        static::assertThat($actualJson, new LogicalNot($constraintExpected), $message);
    }

    /**
     * @return LogicalAnd
     */
    public static function logicalAnd()
    {
        $constraints = \func_get_args();

        $constraint = new LogicalAnd;
        $constraint->setConstraints($constraints);

        return $constraint;
    }

    /**
     * @return LogicalOr
     */
    public static function logicalOr()
    {
        $constraints = \func_get_args();

        $constraint = new LogicalOr;
        $constraint->setConstraints($constraints);

        return $constraint;
    }

    /**
     * @param Constraint $constraint
     *
     * @return LogicalNot
     */
    public static function logicalNot(Constraint $constraint)
    {
        return new LogicalNot($constraint);
    }

    /**
     * @return LogicalXor
     */
    public static function logicalXor()
    {
        $constraints = \func_get_args();

        $constraint = new LogicalXor;
        $constraint->setConstraints($constraints);

        return $constraint;
    }

    /**
     * @return IsAnything
     */
    public static function anything()
    {
        return new IsAnything;
    }

    /**
     * @return IsTrue
     */
    public static function isTrue()
    {
        return new IsTrue;
    }

    /**
     * @param callable $callback
     *
     * @return Callback
     */
    public static function callback($callback)
    {
        return new Callback($callback);
    }

    /**
     * @return IsFalse
     */
    public static function isFalse()
    {
        return new IsFalse;
    }

    /**
     * @return IsJson
     */
    public static function isJson()
    {
        return new IsJson;
    }

    /**
     * @return IsNull
     */
    public static function isNull()
    {
        return new IsNull;
    }

    /**
     * @return IsFinite
     */
    public static function isFinite()
    {
        return new IsFinite;
    }

    /**
     * @return IsInfinite
     */
    public static function isInfinite()
    {
        return new IsInfinite;
    }

    /**
     * @return IsNan
     */
    public static function isNan()
    {
        return new IsNan;
    }

    /**
     * @param Constraint $constraint
     * @param string     $attributeName
     *
     * @return Attribute
     */
    public static function attribute(Constraint $constraint, $attributeName)
    {
        return new Attribute(
            $constraint,
            $attributeName
        );
    }

    /**
     * @param mixed $value
     * @param bool  $checkForObjectIdentity
     * @param bool  $checkForNonObjectIdentity
     *
     * @return TraversableContains
     */
    public static function contains($value, $checkForObjectIdentity = true, $checkForNonObjectIdentity = false)
    {
        return new TraversableContains($value, $checkForObjectIdentity, $checkForNonObjectIdentity);
    }

    /**
     * @param string $type
     *
     * @return TraversableContainsOnly
     */
    public static function containsOnly($type)
    {
        return new TraversableContainsOnly($type);
    }

    /**
     * @param string $classname
     *
     * @return TraversableContainsOnly
     */
    public static function containsOnlyInstancesOf($classname)
    {
        return new TraversableContainsOnly($classname, false);
    }

    /**
     * @param mixed $key
     *
     * @return ArrayHasKey
     */
    public static function arrayHasKey($key)
    {
        return new ArrayHasKey($key);
    }

    /**
     * @param mixed $value
     * @param float $delta
     * @param int   $maxDepth
     * @param bool  $canonicalize
     * @param bool  $ignoreCase
     *
     * @return IsEqual
     */
    public static function equalTo($value, $delta = 0.0, $maxDepth = 10, $canonicalize = false, $ignoreCase = false)
    {
        return new IsEqual(
            $value,
            $delta,
            $maxDepth,
            $canonicalize,
            $ignoreCase
        );
    }

    /**
     * @param string $attributeName
     * @param mixed  $value
     * @param float  $delta
     * @param int    $maxDepth
     * @param bool   $canonicalize
     * @param bool   $ignoreCase
     *
     * @return Attribute
     */
    public static function attributeEqualTo($attributeName, $value, $delta = 0.0, $maxDepth = 10, $canonicalize = false, $ignoreCase = false)
    {
        return static::attribute(
            static::equalTo(
                $value,
                $delta,
                $maxDepth,
                $canonicalize,
                $ignoreCase
            ),
            $attributeName
        );
    }

    /**
     * @return IsEmpty
     */
    public static function isEmpty()
    {
        return new IsEmpty;
    }

    /**
     * @return IsWritable
     */
    public static function isWritable()
    {
        return new IsWritable;
    }

    /**
     * @return IsReadable
     */
    public static function isReadable()
    {
        return new IsReadable;
    }

    /**
     * @return DirectoryExists
     */
    public static function directoryExists()
    {
        return new DirectoryExists;
    }

    /**
     * @return FileExists
     */
    public static function fileExists()
    {
        return new FileExists;
    }

    /**
     * @param mixed $value
     *
     * @return GreaterThan
     */
    public static function greaterThan($value)
    {
        return new GreaterThan($value);
    }

    /**
     * @param mixed $value
     *
     * @return LogicalOr
     */
    public static function greaterThanOrEqual($value)
    {
        return static::logicalOr(
            new IsEqual($value),
            new GreaterThan($value)
        );
    }

    /**
     * @param string $attributeName
     *
     * @return ClassHasAttribute
     */
    public static function classHasAttribute($attributeName)
    {
        return new ClassHasAttribute(
            $attributeName
        );
    }

    /**
     * @param string $attributeName
     *
     * @return ClassHasStaticAttribute
     */
    public static function classHasStaticAttribute($attributeName)
    {
        return new ClassHasStaticAttribute(
            $attributeName
        );
    }

    /**
     * @param string $attributeName
     *
     * @return ObjectHasAttribute
     */
    public static function objectHasAttribute($attributeName)
    {
        return new ObjectHasAttribute(
            $attributeName
        );
    }

    /**
     * @param mixed $value
     *
     * @return IsIdentical
     */
    public static function identicalTo($value)
    {
        return new IsIdentical($value);
    }

    /**
     * @param string $className
     *
     * @return IsInstanceOf
     */
    public static function isInstanceOf($className)
    {
        return new IsInstanceOf($className);
    }

    /**
     * @param string $type
     *
     * @return IsType
     */
    public static function isType($type)
    {
        return new IsType($type);
    }

    /**
     * @param mixed $value
     *
     * @return LessThan
     */
    public static function lessThan($value)
    {
        return new LessThan($value);
    }

    /**
     * @param mixed $value
     *
     * @return LogicalOr
     */
    public static function lessThanOrEqual($value)
    {
        return static::logicalOr(
            new IsEqual($value),
            new LessThan($value)
        );
    }

    /**
     * @param string $pattern
     *
     * @return RegularExpression
     */
    public static function matchesRegularExpression($pattern)
    {
        return new RegularExpression($pattern);
    }

    /**
     * @param string $string
     *
     * @return StringMatchesFormatDescription
     */
    public static function matches($string)
    {
        return new StringMatchesFormatDescription($string);
    }

    /**
     * @param mixed $prefix
     *
     * @return StringStartsWith
     */
    public static function stringStartsWith($prefix)
    {
        return new StringStartsWith($prefix);
    }

    /**
     * @param string $string
     * @param bool   $case
     *
     * @return StringContains
     */
    public static function stringContains($string, $case = true)
    {
        return new StringContains($string, $case);
    }

    /**
     * @param mixed $suffix
     *
     * @return StringEndsWith
     */
    public static function stringEndsWith($suffix)
    {
        return new StringEndsWith($suffix);
    }

    /**
     * @param int $count
     *
     * @return Count
     */
    public static function countOf($count)
    {
        return new Count($count);
    }

    /**
     * Fails a test with the given message.
     *
     * @param string $message
     *
     * @throws AssertionFailedError
     */
    public static function fail($message = '')
    {
        self::$count++;

        throw new AssertionFailedError($message);
    }

    /**
     * Returns the value of an attribute of a class or an object.
     * This also works for attributes that are declared protected or private.
     *
     * @param string|object $classOrObject
     * @param string        $attributeName
     *
     * @return mixed
     *
     * @throws Exception
     */
    public static function readAttribute($classOrObject, $attributeName)
    {
        if (!\is_string($attributeName)) {
            throw InvalidArgumentHelper::factory(2, 'string');
        }

        if (!\preg_match('/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/', $attributeName)) {
            throw InvalidArgumentHelper::factory(2, 'valid attribute name');
        }

        if (\is_string($classOrObject)) {
            if (!\class_exists($classOrObject)) {
                throw InvalidArgumentHelper::factory(
                    1,
                    'class name'
                );
            }

            return static::getStaticAttribute(
                $classOrObject,
                $attributeName
            );
        }

        if (\is_object($classOrObject)) {
            return static::getObjectAttribute(
                $classOrObject,
                $attributeName
            );
        }

        throw InvalidArgumentHelper::factory(
            1,
            'class name or object'
        );
    }

    /**
     * Returns the value of a static attribute.
     * This also works for attributes that are declared protected or private.
     *
     * @param string $className
     * @param string $attributeName
     *
     * @return mixed
     *
     * @throws Exception
     */
    public static function getStaticAttribute($className, $attributeName)
    {
        if (!\is_string($className)) {
            throw InvalidArgumentHelper::factory(1, 'string');
        }

        if (!\class_exists($className)) {
            throw InvalidArgumentHelper::factory(1, 'class name');
        }

        if (!\is_string($attributeName)) {
            throw InvalidArgumentHelper::factory(2, 'string');
        }

        if (!\preg_match('/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/', $attributeName)) {
            throw InvalidArgumentHelper::factory(2, 'valid attribute name');
        }

        $class = new ReflectionClass($className);

        while ($class) {
            $attributes = $class->getStaticProperties();

            if (\array_key_exists($attributeName, $attributes)) {
                return $attributes[$attributeName];
            }

            $class = $class->getParentClass();
        }

        throw new Exception(
            \sprintf(
                'Attribute "%s" not found in class.',
                $attributeName
            )
        );
    }

    /**
     * Returns the value of an object's attribute.
     * This also works for attributes that are declared protected or private.
     *
     * @param object $object
     * @param string $attributeName
     *
     * @return mixed
     *
     * @throws Exception
     */
    public static function getObjectAttribute($object, $attributeName)
    {
        if (!\is_object($object)) {
            throw InvalidArgumentHelper::factory(1, 'object');
        }

        if (!\is_string($attributeName)) {
            throw InvalidArgumentHelper::factory(2, 'string');
        }

        if (!\preg_match('/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/', $attributeName)) {
            throw InvalidArgumentHelper::factory(2, 'valid attribute name');
        }

        try {
            $attribute = new ReflectionProperty($object, $attributeName);
        } catch (ReflectionException $e) {
            $reflector = new ReflectionObject($object);

            while ($reflector = $reflector->getParentClass()) {
                try {
                    $attribute = $reflector->getProperty($attributeName);
                    break;
                } catch (ReflectionException $e) {
                }
            }
        }

        if (isset($attribute)) {
            if (!$attribute || $attribute->isPublic()) {
                return $object->$attributeName;
            }

            $attribute->setAccessible(true);
            $value = $attribute->getValue($object);
            $attribute->setAccessible(false);

            return $value;
        }

        throw new Exception(
            \sprintf(
                'Attribute "%s" not found in object.',
                $attributeName
            )
        );
    }

    /**
     * Mark the test as incomplete.
     *
     * @param string $message
     *
     * @throws IncompleteTestError
     */
    public static function markTestIncomplete($message = '')
    {
        throw new IncompleteTestError($message);
    }

    /**
     * Mark the test as skipped.
     *
     * @param string $message
     *
     * @throws SkippedTestError
     */
    public static function markTestSkipped($message = '')
    {
        throw new SkippedTestError($message);
    }

    /**
     * Return the current assertion count.
     *
     * @return int
     */
    public static function getCount()
    {
        return self::$count;
    }

    /**
     * Reset the assertion counter.
     */
    public static function resetCount()
    {
        self::$count = 0;
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
use SebastianBergmann\Exporter\Exporter;

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
		// See if there are any defaults for the given method
		if (isset(self::$_defaults[$mockclass][$method])) {
			// If so, get them
			$defaults = self::$_defaults[$mockclass][$method];
			// And merge them with the passed args
			$a = $a + $defaults; $b = $b + $defaults;
		}

		// If two argument arrays are different lengths, automatic fail
		if (count($a) != count($b)) return false;

		// Step through each item
		$i = count($a);
		while($i--) {
			$u = $a[$i]; $v = $b[$i];

			// If the argument in $a is a hamcrest matcher, call match on it. WONTFIX: Can't check if function was passed a hamcrest matcher
			if (interface_exists('Hamcrest_Matcher') && ($u instanceof Hamcrest_Matcher || isset($u->__phockito_matcher))) {
				// The matcher can either be passed directly, or wrapped in a mock (for type safety reasons)
				$matcher = null;
				if ($u instanceof Hamcrest_Matcher) {
					$matcher = $u;
				} elseif (isset($u->__phockito_matcher)) {
					$matcher = $u->__phockito_matcher;
				}
				if ($matcher != null && !$matcher->matches($v)) return false;
			}
			// Otherwise check for equality by checking the equality of the serialized version
			else {
				if (serialize($u) != serialize($v)) return false;
			}
		}

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
	 * @return string - The class that acts as a Phockito mock of the passed class
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

	static $exception_class = 'Exception';

	protected $class;
	protected $instance;
	protected $times;

	function __construct($class, $instance, $times) {
		$this->class = $class;
		$this->instance = $instance;
		$this->times = $times;
	}

    private function printargs($called, $args) {
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
		//$exporter = new Exporter();

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
