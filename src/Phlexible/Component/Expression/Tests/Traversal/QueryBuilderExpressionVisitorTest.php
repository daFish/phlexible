<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Expression\Test\Traversal;

use Doctrine\ORM\QueryBuilder;
use Phlexible\Component\Expression\Traversal\QueryBuilderExpressionVisitor;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Webmozart\Expression\Expr;
use Webmozart\Expression\Expression;

/**
 * Message criteria array parser
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class QueryBuilderExpressionVisitorTest extends WebTestCase
{
    /**
     * @return QueryBuilder
     */
    private function createQueryBuilder()
    {
        static::bootKernel();

        return self::$kernel->getContainer()->get('doctrine.orm.entity_manager')
            ->createQueryBuilder('m')
            ->select('m')
            ->from('PhlexibleMessageBundle:Message', 'm');
    }

    /**
     * @param Expression $expr
     *
     * @return QueryBuilder $qb
     */
    private function applyVisitor(Expression $expr)
    {
        $qb = $this->createQueryBuilder();

        $visitor = new QueryBuilderExpressionVisitor($qb, 'm');
        $visitor->apply($expr);

        return $qb;
    }

    public function testApplySimpleExpression()
    {
        $expr = Expr::equals('element', 'channel');

        $qb = $this->applyVisitor($expr);

        $this->assertSame("m.channel = 'element'", (string) $qb->getDQLPart('where'));
    }

    public function testApplyTrueExpression()
    {
        $expr = Expr::true();

        $qb = $this->applyVisitor($expr);

        $this->assertSame("1 = 1", (string) $qb->getDQLPart('where'));
    }

    public function testApplySimpleXExpression()
    {
        $expr = Expr::true()->andGreaterThan('2015-01-01 02:03:04', 'createdAt');

        $qb = $this->applyVisitor($expr);

        $this->assertSame("m.createdAt > '2015-01-01 02:03:04'", (string) $qb->getDQLPart('where'));
    }

    public function testApplyAndExpression()
    {
        $expr = Expr::equals('element', 'channel')
            ->andEquals('ROLE_ELEMENT', 'role');

        $qb = $this->applyVisitor($expr);

        $this->assertSame("m.role = 'ROLE_ELEMENT' AND m.channel = 'element'", (string) $qb->getDQLPart('where'));
    }

    public function testApplyOrExpression()
    {
        $expr = Expr::equals('element', 'channel')
            ->orEquals('user', 'channel');

        $qb = $this->applyVisitor($expr);

        $this->assertSame("m.channel = 'user' OR m.channel = 'element'", (string) $qb->getDQLPart('where'));
    }

    public function testApplyNotWithJunctionExpression()
    {
        $expr = Expr::not(
            Expr::equals('element', 'channel')
                ->orEquals('user', 'channel')
        );

        $qb = $this->applyVisitor($expr);

        $this->assertSame("NOT(m.channel = 'user' OR m.channel = 'element')", (string) $qb->getDQLPart('where'));
    }

    public function testApplyNotExpression()
    {
        $expr = Expr::equals('element', 'channel')->orNot(Expr::equals('user', 'channel'));

        $qb = $this->applyVisitor($expr);

        $this->assertSame("NOT(m.channel = 'user') OR m.channel = 'element'", (string) $qb->getDQLPart('where'));
    }

    public function testApplyWeirdNotExpression()
    {
        $expr = Expr::not(Expr::not(Expr::not(Expr::equals('user', 'channel'))));

        $qb = $this->applyVisitor($expr);

        $this->assertSame("NOT(NOT(NOT(m.channel = 'user')))", (string) $qb->getDQLPart('where'));
    }

    public function testApplyJunctionExpression()
    {
        $expr = Expr::equals('ROLE_ELEMENT', 'role')->andX(
            Expr::equals('element', 'channel')
                ->orEquals('user', 'channel')
        );

        $qb = $this->applyVisitor($expr);

        $this->assertSame("(m.channel = 'user' OR m.channel = 'element') AND m.role = 'ROLE_ELEMENT'", (string) $qb->getDQLPart('where'));
    }

    /**
     * @expectedException \Phlexible\Component\Expression\Exception\UnsupportedExpressionException
     */
    public function testApplyExpressionWithoutKeyThrowsException()
    {
        $expr = Expr::equals('user');

        $qb = $this->applyVisitor($expr);
    }

    /**
     * @expectedException \Phlexible\Component\Expression\Exception\UnsupportedExpressionException
     */
    public function testApplyUnsupportedComparisonThrowsException()
    {
        $expr = Expr::matches('test', 'user');

        $qb = $this->applyVisitor($expr);
    }

    /**
     * @expectedException \Phlexible\Component\Expression\Exception\UnsupportedExpressionException
     */
    public function testApplyUnsupportedSelectorThrowsException()
    {
        $expr = Expr::all(Expr::equals('test', 'user'));

        $qb = $this->applyVisitor($expr);
    }

    /**
     * @expectedException \Phlexible\Component\Expression\Exception\UnsupportedExpressionException
     */
    public function testApplyNestedKeysThrowsException()
    {
        $expr = Expr::key('user', Expr::equals('test', 'property'));

        $qb = $this->applyVisitor($expr);
    }
}
