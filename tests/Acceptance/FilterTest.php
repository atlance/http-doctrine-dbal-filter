<?php

declare(strict_types=1);

namespace Atlance\HttpDbalFilter\Test\Acceptance;

use Atlance\HttpDbalFilter\Filter;
use InvalidArgumentException;

class FilterTest extends TestCase
{
    private const EQ = 'filter[eq]';
    private const NEQ = 'filter[neq]';
    private const GT = 'filter[gt]';
    private const GTE = 'filter[gte]';
    private const ILIKE = 'filter[ilike]';
    private const IN = 'filter[in]';
    private const NOT_IN = 'filter[not_in]';
    private const IS_NULL = 'filter[is_null]';
    private const IS_NOT_NULL = 'filter[is_not_null]';
    private const LIKE = 'filter[like]';
    private const NOT_LIKE = 'filter[not_like]';
    private const LT = 'filter[lt]';
    private const LTE = 'filter[lte]';
    private const BETWEEN = 'filter[between]';

    public function testCreateFilter()
    {
        $this->assertTrue($this->createClearFilter() instanceof Filter);
    }

    /**
     * Test an instance of BETWEEN() function, with the given argument.
     */
    public function testBetween()
    {
        // multiple integer
        $this->assertCountByHttpQuery(self::BETWEEN.'[users_id]=1|3&order[users_id]=asc', 3);
        // multiple datetime
        $this->assertCountByHttpQuery(self::BETWEEN.'[users_created_at]=2019-12-20 21:34:30|2020-01-07 00:21:03', 5);
    }

    /**
     * Test an instance of BETWEEN() function, with the given argument.
     */
    public function testBetweenCheckDate()
    {
        $this->assertCountByHttpQuery(self::BETWEEN.'[cards_expires_at]=2020-03-04|2020-07-08', 3);
        $this->assertCountByHttpQuery(self::BETWEEN.'[users_created_at]=2019-12-20|2020-01-07', 4);
    }

    /**
     * Test an instance of BETWEEN() function, with the given argument.
     */
    public function testBetweenNotEqualCount2Values()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->assertCountByHttpQuery(self::BETWEEN.'[users_id]=1|2|3', 3);
    }

    public function testBooleanValue()
    {
        // single boolean
        $this->assertCountByHttpQuery(self::EQ.'[cards_available]=0', 17);
        $this->assertCountByHttpQuery(self::EQ.'[cards_available]=1', 8);
    }

    /**
     * Test a "=" expression with the given HTTP query arguments.
     */
    public function testEq()
    {
        // single integer
        $this->assertCountByHttpQuery(self::EQ.'[users_id]=1', 1);
        $this->assertCountByHttpQuery(self::EQ.'[users_id]=7&'.self::EQ.'[users_email]=tmed@zvnhkcpnq.shop', 1);
        // multiple integer & boolean & float
        $this->assertCountByHttpQuery(self::EQ.'[users_id]=1|2|3&'.self::EQ.'[cards_available]=1&'.self::EQ.'[cards_balance]=24760.21', 1);
        // single float
        $this->assertCountByHttpQuery(self::EQ.'[cards_balance]=24760.21', 1);
        // multiple float
        $this->assertCountByHttpQuery(self::EQ.'[cards_balance]=112825122.79|394952707.36', 2);
        // single string
        $this->assertCountByHttpQuery(self::EQ.'[cards_bank_name]=???????????????????????? ????????', 1);
        // multiple string
        $this->assertCountByHttpQuery(self::EQ.'[cards_bank_name]=???????????????????????? ????????|??????????', 3);
        // single boolean
        $this->assertCountByHttpQuery(self::EQ.'[cards_available]=1', 8);
        // single datetime
        $this->assertCountByHttpQuery(self::EQ.'[users_created_at]=2019-12-04 07:21:44', 1);
        // multiple datetime
        $this->assertCountByHttpQuery(self::EQ.'[users_created_at]=2019-12-04 07:21:44|2019-12-04 23:19:41', 2);
    }

    /**
     * Test a ">" expression with the given HTTP query arguments.
     */
    public function testGt()
    {
        // single integer
        $this->assertCountByHttpQuery(self::GT.'[users_id]=2', 23);
        // single datetime
        $this->assertCountByHttpQuery(self::GT.'[users_created_at]=2020-01-17 21:50:14', 1);
    }

    /**
     * Test a ">=" expression with the given HTTP query arguments.
     */
    public function testGte()
    {
        // single integer
        $this->assertCountByHttpQuery(self::GTE.'[users_id]=2', 24);
        // single datetime
        $this->assertCountByHttpQuery(self::GTE.'[users_created_at]=2020-01-17 21:50:14', 2);
    }

    /**
     * Test a ">=" expression with the given HTTP query arguments.
     */
    public function testGteMultipleValueException()
    {
        // multiple integer
        $this->expectException(InvalidArgumentException::class);
        $this->assertCountByHttpQuery(self::GTE.'[users_id]=1|2', 1);
    }

    /**
     * Test a ">" expression with the given HTTP query arguments.
     */
    public function testGtMultipleValueException()
    {
        // multiple integer
        $this->expectException(InvalidArgumentException::class);
        $this->assertCountByHttpQuery(self::GT.'[users_id]=1|2', 1);
    }

    /**
     * Test a LIKE() LOWER() comparison expression with the given HTTP query arguments.
     */
    public function testIlike()
    {
        // single integer
        $this->assertCountByHttpQuery(self::ILIKE.'[passport_sn]=676', 2);
        // multiple integer
        $this->assertCountByHttpQuery(self::ILIKE.'[passport_sn]=676|303', 4);
        // single string
        $this->assertCountByHttpQuery(self::ILIKE.'[users_email]=info', 2);
        // multiple string
        $this->assertCountByHttpQuery(self::ILIKE.'[users_email]=info|shop', 3);
        // single boolean
        $this->assertCountByHttpQuery(self::ILIKE.'[cards_available]=1', 8);
        // single datetime
        $this->assertCountByHttpQuery(self::ILIKE.'[users_created_at]=2020-01', 10);
        // multiple datetime
        $this->assertCountByHttpQuery(self::ILIKE.'[users_created_at]=2020-01|2019', 25);
    }

    /**
     * Test a IN() expression with the given HTTP query arguments.
     */
    public function testIn()
    {
        $this->assertCountByHttpQuery(self::IN.'[passport_sn]=4794840291|6761458394', 2);
        $this->assertCountByHttpQuery(self::IN.'[cards_bank_name]=????????|??????????', 4);
        $this->assertCountByHttpQuery(self::IN.'[users_created_at]=2019-12-04 07:21:44|2019-12-04 23:19:41', 2);
    }

    public function testInvalidMaxMinForBetweenDate()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->assertCountByHttpQuery(self::BETWEEN.'[users_created_at]=2020-01-01|2019-01-01', 0);
    }

    public function testInvalidMaxMinForBetweenInteger()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->assertCountByHttpQuery(self::BETWEEN.'[users_created_at]=2|1', 0);
    }

    /**
     * Test an IS NOT NULL expression with the given HTTP query arguments.
     */
    public function testIsNotNull()
    {
        // single integer
        $this->assertCountByHttpQuery(self::IS_NOT_NULL.'[users_id]', 25);
        // single string
        $this->assertCountByHttpQuery(self::IS_NOT_NULL.'[users_email]', 5);
        // single boolean
        $this->assertCountByHttpQuery(self::IS_NOT_NULL.'[cards_available]', 25);
    }

    /**
     * Test an IS NULL expression with the given HTTP query arguments.
     */
    public function testIsNull()
    {
        // single integer
        $this->assertCountByHttpQuery(self::IS_NULL.'[users_id]', 0);
        // single string
        $this->assertCountByHttpQuery(self::IS_NULL.'[users_email]', 20);
        // single boolean
        $this->assertCountByHttpQuery(self::IS_NULL.'[cards_available]', 0);
    }

    /**
     * Test a LIKE() comparison expression with the given HTTP query arguments.
     */
    public function testLike()
    {
        // single integer
        $this->assertCountByHttpQuery(self::LIKE.'[passport_sn]=676', 2);
        // multiple integer
        $this->assertCountByHttpQuery(self::LIKE.'[passport_sn]=676|303', 4);
        // single string
        $this->assertCountByHttpQuery(self::LIKE.'[users_email]=info', 2);
        // multiple string
        $this->assertCountByHttpQuery(self::LIKE.'[users_email]=info|shop', 3);
        // single boolean
        $this->assertCountByHttpQuery(self::LIKE.'[cards_available]=1', 8);
        // single datetime
        $this->assertCountByHttpQuery(self::LIKE.'[users_created_at]=2020-01', 10);
        // multiple datetime
        $this->assertCountByHttpQuery(self::LIKE.'[users_created_at]=2020-01|2019', 25);
    }

    /**
     * Test a "=<" expression with the given HTTP query arguments.
     */
    public function testLt()
    {
        // single integer
        $this->assertCountByHttpQuery(self::LT.'[users_id]=2', 1);
        // single float
        $this->assertCountByHttpQuery(self::LT.'[cards_balance]=2.70', 2);
        // single datetime
        $this->assertCountByHttpQuery(self::LT.'[users_created_at]=2020-01-17 21:50:14', 23);
    }

    /**
     * Test a "<" expression with the given HTTP query arguments.
     */
    public function testLte()
    {
        // single integer
        $this->assertCountByHttpQuery(self::LTE.'[users_id]=2', 2);
        // single datetime
        $this->assertCountByHttpQuery(self::LTE.'[users_created_at]=2020-01-17 21:50:14', 24);
    }

    /**
     * Test a "<" expression with the given HTTP query arguments.
     */
    public function testLteMultipleValueException()
    {
        // multiple integer
        $this->expectException(InvalidArgumentException::class);
        $this->assertCountByHttpQuery(self::LTE.'[users_id]=1|2', 1);
    }

    /**
     * Test a "=<" expression with the given HTTP query arguments.
     */
    public function testLtMultipleValueException()
    {
        // multiple integer
        $this->expectException(InvalidArgumentException::class);
        $this->assertCountByHttpQuery(self::LT.'[users_id]=1|2', 1);
    }

    /**
     * Test a "<>" expression with the given HTTP query arguments.
     */
    public function testNeq()
    {
        // single integer
        $this->assertCountByHttpQuery(self::NEQ.'[passport_sn]=6762843688', 24);
        // multiple integer
        $this->assertCountByHttpQuery(self::NEQ.'[passport_sn]=4794840291|6761458394', 23);
        // single string
        $this->assertCountByHttpQuery(self::NEQ.'[cards_bank_name]=??????????', 23);
        // multiple string
        $this->assertCountByHttpQuery(self::NEQ.'[cards_bank_name]=????????|??????????', 21);
        // single boolean
        $this->assertCountByHttpQuery(self::NEQ.'[cards_available]=1', 17);
        // single datetime
        $this->assertCountByHttpQuery(self::NEQ.'[users_created_at]=2019-12-04 07:21:44', 24);
        // multiple datetime
        $this->assertCountByHttpQuery(self::NEQ.'[users_created_at]=2019-12-04 07:21:44|2019-12-04 23:19:41', 23);
    }

    /**
     * Test a NOT IN() expression with the given HTTP query arguments.
     */
    public function testNotIn()
    {
        $this->assertCountByHttpQuery(self::NOT_IN.'[passport_sn]=4794840291|6761458394', 23);
        $this->assertCountByHttpQuery(self::NOT_IN.'[cards_bank_name]=????????|??????????', 21);
        $this->assertCountByHttpQuery(self::NOT_IN.'[users_created_at]=2019-12-04 07:21:44|2019-12-04 23:19:41', 23);
    }

    /**
     * Test a NOT LIKE() comparison expression with the given HTTP query arguments.
     */
    public function testNotLike()
    {
        // single integer
        $this->assertCountByHttpQuery(self::NOT_LIKE.'[passport_sn]=676', 23);
        // multiple integer
        $this->assertCountByHttpQuery(self::NOT_LIKE.'[passport_sn]=676|303', 21);
        // single string
        $this->assertCountByHttpQuery(self::NOT_LIKE.'[users_email]=info', 3);
        // multiple string
        $this->assertCountByHttpQuery(self::NOT_LIKE.'[users_email]=info|shop', 2);
        // single boolean
        $this->assertCountByHttpQuery(self::NOT_LIKE.'[cards_available]=1', 17);
        // single datetime
        $this->assertCountByHttpQuery(self::NOT_LIKE.'[users_created_at]=2020-01', 15);
        // multiple datetime
        $this->assertCountByHttpQuery(self::NOT_LIKE.'[users_created_at]=2020-01|2019', 0);
    }

    public function testOrderBy()
    {
        $filter = $this->createClearFilter();
        $qb = $this->prepareQueryBuilderQuery();
        $request = $this->createHttpConfiguration('order[cards_expires_at]=asc');
        $query = $filter->apply($qb, $request);
        $this->assertFalse(strpos($query->getSQL(), 'ORDER BY cards.expires_at') === false);
    }

    public function testSelectByNotValidFieldName()
    {
        $filter = $this->createClearFilter();
        $qb = $this->prepareQueryBuilderQuery();
        $request = $this->createHttpConfiguration(self::EQ.'[cards_foo]=1');

        $this->expectException(InvalidArgumentException::class);
        $filter->apply($qb, $request);
    }

    public function testSelectNotAllowedAlias()
    {
        $filter = $this->createClearFilter();
        $qb = $this->prepareQueryBuilderQuery();
        $request = $this->createHttpConfiguration(self::EQ.'[foo_id]=1');

        $this->expectException(InvalidArgumentException::class);
        $filter->apply($qb, $request);
    }

    public function testSelectNotAllowedMethod()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->createHttpConfiguration('filter[foo][users_id]=1');
    }
}
