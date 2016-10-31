<?php
namespace Tests\ElasticsearchAdapter\Params;

use ElasticsearchAdapter\Params\ArrayParams;
use ElasticsearchAdapter\Params\ParamsReplacer;
use PHPUnit\Framework\TestCase;

/**
 * ParamsReplacerTest
 *
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>, Markus Mächler <markus.maechler@students.fhnw.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php
 * @link     http://linked.swissbib.ch
 */
class ParamsReplacerTest extends TestCase
{
    /**
     * return void
     */
    public function testReplaceNullParam()
    {
        $paramsReplacer = new ParamsReplacer();

        $this->assertEquals('test', $paramsReplacer->replace('test'));
        $this->assertEquals('{test}', $paramsReplacer->replace('{test}'));
        $this->assertEquals('{default(test,10)}', $paramsReplacer->replace('{default(test,10)}'));
    }

    /**
     * return void
     */
    public function testReplaceSimpleParam()
    {
        $paramsProphecy = $this->prophesize(ArrayParams::class);
        $paramsProphecy->has('test')->willReturn(false);
        $paramsProphecy->has('q')->willReturn(true);
        $paramsProphecy->get('q')->willReturn('the query string');

        $paramsReplacer = new ParamsReplacer($paramsProphecy->reveal());

        $this->assertEquals('test', $paramsReplacer->replace('test'));
        $this->assertEquals('{test}', $paramsReplacer->replace('{test}'));
        $this->assertEquals('the query string', $paramsReplacer->replace('{q}'));
    }

    /**
     * return void
     */
    public function testReplaceSimpleArrayParam()
    {
        $paramsProphecy = $this->prophesize(ArrayParams::class);
        $paramsProphecy->has('param1')->willReturn(true);
        $paramsProphecy->has('param2')->willReturn(true);
        $paramsProphecy->has('param3')->willReturn(false);
        $paramsProphecy->get('param1')->willReturn('value1');
        $paramsProphecy->get('param2')->willReturn('value2');

        $paramsReplacer = new ParamsReplacer($paramsProphecy->reveal());

        $this->assertEquals(['value1', 'value2', 'param3'], $paramsReplacer->replace(['{param1}', '{param2}', 'param3']));
    }

    /**
     * return void
     */
    public function testDefaultModifier()
    {
        $paramsProphecy = $this->prophesize(ArrayParams::class);
        $paramsProphecy->has('size')->willReturn(true);
        $paramsProphecy->has('from')->willReturn(false);
        $paramsProphecy->get('size')->willReturn(10);

        $paramsReplacer = new ParamsReplacer($paramsProphecy->reveal());

        $this->assertEquals('10', $paramsReplacer->replace('{default(size, 20)}'));
        $this->assertEquals('20', $paramsReplacer->replace('{default(from, 20)}'));
    }

    /**
     * return void
     */
    public function testEmptyDefaultModifier()
    {
        $paramsProphecy = $this->prophesize(ArrayParams::class);
        $paramsProphecy->has('size')->willReturn(false);

        $paramsReplacer = new ParamsReplacer($paramsProphecy->reveal());

        $this->assertEquals('', $paramsReplacer->replace('{default(size)}'));
    }

    /**
     * return void
     */
    public function testParams()
    {
        $paramsProphecy = $this->prophesize(ArrayParams::class);
        $params = $paramsProphecy->reveal();
        $paramsProphecy2 = $this->prophesize(ArrayParams::class);
        $params2 = $paramsProphecy2->reveal();

        $paramsReplacer = new ParamsReplacer($params);

        $this->assertEquals($params, $paramsReplacer->getParams());

        $paramsReplacer->setParams($params2);

        $this->assertEquals($params2, $paramsReplacer->getParams());
    }
}
