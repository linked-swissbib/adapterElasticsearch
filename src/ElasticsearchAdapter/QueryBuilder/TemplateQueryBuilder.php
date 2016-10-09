<?php
namespace ElasticsearchAdapter\QueryBuilder;
use ElasticsearchAdapter\Query\Query;

/**
 * TemplateQueryBuilder interface
 *
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>, Markus Mächler <markus.maechler@students.fhnw.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php
 * @link     http://linked.swissbib.ch
 */
class TemplateQueryBuilder implements QueryBuilder
{
    /**
     * @var array
     */
    protected $templates = [];

    /**
     * @param array $templates
     */
    public function __construct(array $templates)
    {
        $this->templates = $templates;
    }

    /**
     * @param string $template
     *
     * @return Query
     */
    public function buildQueryFromTemplate(string $template) : Query
    {
        if (!isset($this->templates[$template])) {
            throw new \InvalidArgumentException('No template with name "' . $template . '" found.');
        }

        return null;
    }
}