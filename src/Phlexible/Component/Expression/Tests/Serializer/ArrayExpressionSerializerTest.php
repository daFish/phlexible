<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Expression\Test\Serializer;

use Phlexible\Component\Expression\Serializer\ArrayExpressionSerializerInterface;
use Webmozart\Expression\Expr;

/**
 * Message criteria array parser
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ArrayExpressionSerializerTest extends \PHPUnit_Framework_TestCase
{
    public function testSerializeAnd()
    {
        $expr = Expr::equals('John', 'firstname')
            ->andEquals('Doe', 'lastname');

        $serializer = new ArrayExpressionSerializerInterface();
        $data = $serializer->serialize($expr);

        $this->assertEquals(
            array(
                'op' => 'and',
                'expressions' => array(
                    array('field' => 'firstname', 'op' => 'equals', 'value' => 'John'),
                    array('field' => 'lastname', 'op' => 'equals', 'value' => 'Doe'),
                ),
            ),
            $data
        );
    }

    public function testSerializeOr()
    {
        $expr = Expr::equals('John', 'firstname')
            ->orEquals('Doe', 'lastname');

        $serializer = new ArrayExpressionSerializerInterface();
        $data = $serializer->serialize($expr);

        $this->assertEquals(
            array(
                'op' => 'or',
                'expressions' => array(
                    array('field' => 'firstname', 'op' => 'equals', 'value' => 'John'),
                    array('field' => 'lastname', 'op' => 'equals', 'value' => 'Doe'),
                ),
            ),
            $data
        );
    }

    public function testSerializeNot()
    {
        $expr = Expr::not(Expr::equals('John', 'firstname'));

        $serializer = new ArrayExpressionSerializerInterface();
        $data = $serializer->serialize($expr);

        $this->assertEquals(
            array(
                'op' => 'not',
                'expression' => array('field' => 'firstname', 'op' => 'equals', 'value' => 'John'),
            ),
            $data
        );
    }

    public function testSerializeJunction()
    {
        $expr = Expr::equals('jdoe', 'username')->andX(
            Expr::equals('John', 'firstname')
                ->orEquals('Doe', 'lastname')
        );

        $serializer = new ArrayExpressionSerializerInterface();
        $data = $serializer->serialize($expr);

        $this->assertEquals(
            array(
                'op' => 'and',
                'expressions' => array(
                    array('field' => 'username', 'op' => 'equals', 'value' => 'jdoe'),
                    array('op' => 'or', 'expressions' => array(
                        array('field' => 'firstname', 'op' => 'equals', 'value' => 'John'),
                        array('field' => 'lastname', 'op' => 'equals', 'value' => 'Doe'),
                    )),
                ),
            ),
            $data
        );
    }

    public function testSerializeComparisons()
    {
        $expr = Expr::equals('jdoe', 'username')
            ->andNotEquals('xdoe', 'username')
            ->andSame('jdoe', 'username')
            ->andNotSame('xdoe', 'username')
            ->andStartswith('jd', 'username')
            ->andEndswith('oe', 'username')
            ->andContains('do', 'username')
            ->andMatches('/do/', 'username')
            ->andIn(array('jdoe', 'xdoe'), 'username')
            ->andKeyExists('xxx', 'properties')
            ->andKeyNotExists('yyy', 'properties')
            //->andTrue()
            //->andFalse()
            ->andNull('username')
            ->andNotNull('username')
            ->andEmpty('username')
            ->andNotEmpty('username')
            ->andGreaterThan(1, 'logins')
            ->andGreaterThanEqual(2, 'logins')
            ->andLessThan(3, 'logins')
            ->andLessThanEqual(4, 'logins');

        $serializer = new ArrayExpressionSerializerInterface();
        $data = $serializer->serialize($expr);

        $this->assertEquals(
            array(
                'op' => 'and',
                'expressions' => array(
                    array('field' => 'username', 'op' => 'equals', 'value' => 'jdoe'),
                    array('field' => 'username', 'op' => 'notEquals', 'value' => 'xdoe'),
                    array('field' => 'username', 'op' => 'same', 'value' => 'jdoe'),
                    array('field' => 'username', 'op' => 'notSame', 'value' => 'xdoe'),
                    array('field' => 'username', 'op' => 'startsWith', 'value' => 'jd'),
                    array('field' => 'username', 'op' => 'endsWith', 'value' => 'oe'),
                    array('field' => 'username', 'op' => 'contains', 'value' => 'do'),
                    array('field' => 'username', 'op' => 'matches', 'value' => '/do/'),
                    array('field' => 'username', 'op' => 'in', 'value' => array('jdoe', 'xdoe')),
                    array('field' => 'properties', 'op' => 'keyExists', 'value' => 'xxx'),
                    array('field' => 'properties', 'op' => 'keyNotExists', 'value' => 'yyy'),
                    array('field' => 'username', 'op' => 'same', 'value' => null),
                    array('field' => 'username', 'op' => 'notSame', 'value' => null),
                    array('field' => 'username', 'op' => 'isEmpty'),
                    array('field' => 'username', 'op' => 'isNotEmpty'),
                    array('field' => 'logins', 'op' => 'greaterThan', 'value' => 1),
                    array('field' => 'logins', 'op' => 'greaterThanEqual', 'value' => 2),
                    array('field' => 'logins', 'op' => 'lessThan', 'value' => 3),
                    array('field' => 'logins', 'op' => 'lessThanEqual', 'value' => 4),
                ),
            ),
            $data
        );
    }

    public function testDeserializeAnd()
    {
        $data = array(
            'op' => 'and',
            'expressions' => array(
                array('field' => 'firstname', 'op' => 'equals', 'value' => 'John'),
                array('field' => 'lastname', 'op' => 'equals', 'value' => 'Doe'),
            ),
        );

        $serializer = new ArrayExpressionSerializerInterface();
        $expr = $serializer->deserialize($data);

        $this->assertSame('firstname=="John" && lastname=="Doe"', (string) $expr);
    }

    public function testDeserializeOr()
    {
        $data = array(
            'op' => 'or',
            'expressions' => array(
                array('field' => 'firstname', 'op' => 'equals', 'value' => 'John'),
                array('field' => 'lastname', 'op' => 'equals', 'value' => 'Doe'),
            ),
        );

        $serializer = new ArrayExpressionSerializerInterface();
        $expr = $serializer->deserialize($data);

        $this->assertSame('firstname=="John" || lastname=="Doe"', (string) $expr);
    }

    public function testDeserializeNot()
    {
        $data = array(
            'op' => 'not',
            'expression' => array('field' => 'firstname', 'op' => 'equals', 'value' => 'John'),
        );

        $serializer = new ArrayExpressionSerializerInterface();
        $expr = $serializer->deserialize($data);

        $this->assertSame('not(firstname=="John")', (string) $expr);
    }

    public function testDeserializeAndWithNestedExpression()
    {
        $data = array(
            'op' => 'and',
            'expressions' => array(
                array('field' => 'username', 'op' => 'equals', 'value' => 'jdoe'),
                array('op' => 'or', 'expressions' => array(
                    array('field' => 'firstname', 'op' => 'startsWith', 'value' => 'Joh'),
                    array('field' => 'lastname', 'op' => 'endsWith', 'value' => 'oe'),
                )),
            ),
        );

        $serializer = new ArrayExpressionSerializerInterface();
        $expr = $serializer->deserialize($data);

        $this->assertSame('username=="jdoe" && (firstname.startsWith("Joh") || lastname.endsWith("oe"))', (string) $expr);
    }


    public function testDeserializeComparisons()
    {
        $data = array(
            'op' => 'and',
            'expressions' => array(
                array('field' => 'username', 'op' => 'equals', 'value' => 'jdoe'),
                array('field' => 'username', 'op' => 'notEquals', 'value' => 'xdoe'),
                array('field' => 'username', 'op' => 'same', 'value' => 'jdoe'),
                array('field' => 'username', 'op' => 'notSame', 'value' => 'xdoe'),
                array('field' => 'username', 'op' => 'startsWith', 'value' => 'Joh'),
                array('field' => 'username', 'op' => 'endsWith', 'value' => 'oe'),
                array('field' => 'username', 'op' => 'contains', 'value' => 'do'),
                array('field' => 'username', 'op' => 'matches', 'value' => '/test/'),
                array('field' => 'username', 'op' => 'in', 'value' => array('jdoe', 'xdoe')),
                array('field' => 'properties', 'op' => 'keyExists', 'value' => 'xxx'),
                array('field' => 'properties', 'op' => 'keyNotExists', 'value' => 'yyy'),
                //array('field' => 'lastname', 'op' => 'true'),
                //array('field' => 'lastname', 'op' => 'false'),
                array('field' => 'username', 'op' => 'null'),
                array('field' => 'username', 'op' => 'notNull'),
                array('field' => 'username', 'op' => 'isEmpty'),
                array('field' => 'username', 'op' => 'notEmpty'),
                array('field' => 'logins', 'op' => 'greaterThan', 'value' => 1),
                array('field' => 'logins', 'op' => 'greaterThanEqual', 'value' => 2),
                array('field' => 'logins', 'op' => 'lessThan', 'value' => 3),
                array('field' => 'logins', 'op' => 'lessThanEqual', 'value' => 4),
            ),
        );

        $serializer = new ArrayExpressionSerializerInterface();
        $expr = $serializer->deserialize($data);

        $this->assertSame('username=="jdoe" && username!="xdoe" && username==="jdoe" && username!=="xdoe" && username.startsWith("Joh") && username.endsWith("oe") && username.contains("do") && username.matches("/test/") && username.in("jdoe", "xdoe") && properties.keyExists("xxx") && properties.keyNotExists("yyy") && username===null && username!==null && username.empty() && username.notEmpty() && logins>1 && logins>=2 && logins<3 && logins<=4', (string) $expr);
    }

}
