<?php
namespace ElasticsearchAdapter\Params;

/**
 * ArrayParams
 *
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>, Markus Mächler <markus.maechler@students.fhnw.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php
 * @link     http://linked.swissbib.ch
 */
class ArrayParams implements Params
{
    /**
     * @var array
     */
    protected $params = [];

    /**
     * @inheritdoc
     */
    public function get(string $name) : string
    {
        return $this->params[$name] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function set(string $name, string $value)
    {
        $this->params[$name] = $value;
    }

    /**
     * @inheritdoc
     */
    public function has(string $name) : bool
    {
        return isset($this->params[$name]);
    }

    /**
     * @inheritdoc
     */
    public function remove(string $name)
    {
        if (isset($this->params[$name])) {
            unset($this->params[$name]);
        }
    }
}
