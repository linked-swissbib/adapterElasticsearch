<?php
namespace Tests\ElasticsearchAdapter\Result;

use ElasticsearchAdapter\Result\ElasticsearchClientResult;
use PHPUnit\Framework\TestCase;

/**
 * ElasticsearchClientResultTest
 *
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>, Markus Mächler <markus.maechler@students.fhnw.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php
 * @link     http://linked.swissbib.ch
 */
class ElasticsearchClientResultTest extends TestCase
{
    /**
     * @var array
     */
    protected $response = [
        'took' => 2,
        'timed_out' => false,
        '_shards' => [
            'total' => 5,
            'successful' => 5,
            'failed' => 0,
        ],
        'hits' => [
            'total' => 1,
            'max_score' => 1.0,
            'hits' => [
                0 => [
                    '_index' => 'testsb_160426',
                    '_type' => 'document',
                    '_id' => '000000051',
                    '_score' => 1.0,
                    '_source' => [
                        '@type' => 'http://purl.org/ontology/bibo/document',
                        '@context' => 'http://data.swissbib.ch/document/context.jsonld',
                        'dct:issued' => '2016-04-26T08:41:49.227Z',
                        '@id' => 'http://data.swissbib.ch/resource/000000051/about',
                        'foaf:primaryTopic' => 'http://data.swissbib.ch/resource/000000051/about',
                        'dct:modified' => '2014-08-14T16:40:57+01:00',
                        'dct:contributor' => [
                            0 => 'http://d-nb.info/gnd/1046905-9',
                            1 => 'http://data.swissbib.ch/agent/ABN',
                        ],
                        'bf:local' => [
                            0 => 'OCoLC/775794624',
                            1 => 'ABN/000300043',
                        ],
                    ],
                ],
            ],
        ],
    ];

    /**
     * @return void
     */
    public function testTemplateSearch()
    {
        $result = new ElasticsearchClientResult($this->response);

        $this->assertEquals(1, $result->getTotal());
        $this->assertEquals(2, $result->getTook());
        $this->assertEquals(1.0, $result->getMaxScore());
        $this->assertFalse($result->getTimedOut());
        $this->assertEquals($this->response['hits']['hits'], $result->getHits());
        $this->assertEquals($this->response, $result->getRawResult());
    }
}
