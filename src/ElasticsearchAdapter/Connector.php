<?php
/**
 *
 * @category linked-swissbib
 * @package  Backend_Eleasticsearch
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://linked.swissbib.ch  Main Page
 */

namespace ElasticsearchAdapter;


use Elasticsearch\ClientBuilder;
use Elasticsearch\Client;


class Connector
{

    /** @var array $indexConfig*/
    private  $indexConfig;

    public function __construct(array $config) {
        $this->indexConfig = $config;
    }


    /**
     * @var
     */
    private $proxy;

    //protected $adapter = 'Zend\Http\Client\Adapter\Socket';


    /**
     * @return void
     *
     * @todo Typehint on ProxyInterface
     */
    public function setProxy($proxy)
    {
        //Todo: do we need this, if I'm not wrong a VuFind specific Proxy is used
        $this->proxy = $proxy;
    }

    /**
     * Execute a search.
     *
     * @return string
     */
    public function search(array $params)
    {

        $client = $this->createClient();
        return $this->send($client,$params);

        //$handler = $this->map->getHandler(__FUNCTION__);
        //$this->map->prepare(__FUNCTION__, $params);
        //return $this->query($handler, $params);
    }



    /**
     *
     */
    protected function send(Client $client,$params)
    {
        /*
        $this->debug(
            sprintf('=> %s %s', $client->getMethod(), $client->getUri())
        );
        */

        //todo; logging of used time (or use time as feedback to user)
        $time     = microtime(true);
        $response =  $client->search($params);
        $time     = microtime(true) - $time;

        //todo: some kind of error handling
        //we can suppress errors by configuration on the client type
        /*
        $this->debug(
            sprintf(
                '<= %s %s', $response->getStatusCode(),
                $response->getReasonPhrase()
            ), ['time' => $time]
        );

        if (!$response->isSuccess()) {
            throw HttpErrorException::createFromResponse($response);
        }
        */
        //todo: return only the body
        return $response;
    }

    /**
     *
     */
    protected function createClient($url = null)
    {
        //todo: configure the ES cluster

        if (!isset($this->indexConfig['hosts'])) {
            //Todo: do we need component specific Exceptions
            throw new \Exception("target hosts are not configured");
        }

        $client = ClientBuilder::create()->setHosts($this->indexConfig['hosts'])->build();

        return $client;
    }


}