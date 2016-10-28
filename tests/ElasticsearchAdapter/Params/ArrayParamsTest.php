<?php
namespace Tests\ElasticsearchAdapter\Params;

use ElasticsearchAdapter\Params\ArrayParams;
use PHPUnit\Framework\TestCase;

/**
 * ArrayParamsTest
 *
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>, Markus Mächler <markus.maechler@students.fhnw.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php
 * @link     http://linked.swissbib.ch
 */
class ArrayParamsTest extends TestCase
{
    /**
     * @return void
     */
    public function testSetAndGet()
    {
        $params = new ArrayParams();

        $params->set('a', 'value a')->set('b', 'value b');

        $this->assertEquals('value a', $params->get('a'));
        $this->assertEquals('value b', $params->get('b'));
        $this->assertNull($params->get('not a name'));
    }

    /**
     * @return void
     */
    public function testHas()
    {
        $params = new ArrayParams();

        $params->set('name', 'value');

        $this->assertTrue($params->has('name'));
        $this->assertFalse($params->has('not name'));
    }

    /**
     * @return void
     */
    public function testRemove()
    {
        $params = new ArrayParams();

        $params->set('name', 'value');

        $this->assertTrue($params->has('name'));

        $params->remove('name');

        $this->assertFalse($params->has('name'));
    }
}
