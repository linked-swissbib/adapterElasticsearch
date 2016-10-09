<?php
namespace ElasticsearchAdapter\Params;

/**
 * Params interface
 *
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>, Markus Mächler <markus.maechler@students.fhnw.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php
 * @link     http://linked.swissbib.ch
 */
interface Params
{
    /**
     * @param string $name
     *
     * @return string
     */
    public function get(string $name) : string;

    /**
     * @param string $name
     * @param string $value
     *
     * @return void
     */
    public function set(string $name, string $value);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name) : bool;

    /**
     * @param string $name
     *
     * @return void
     */
    public function remove(string $name);
}
