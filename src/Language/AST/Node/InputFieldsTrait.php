<?php

namespace Digia\GraphQL\Language\AST\Node;

use Digia\GraphQL\Util\SerializationInterface;

trait InputFieldsTrait
{

    /**
     * @var InputValueDefinitionNode[]
     */
    protected $fields;

    /**
     * @return bool
     */
    public function hasFields(): bool
    {
        return !empty($this->fields);
    }

    /**
     * @return InputValueDefinitionNode[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @return array
     */
    public function getFieldsAsArray(): array
    {
        return array_map(function (SerializationInterface $node) {
            return $node->toArray();
        }, $this->fields);
    }
}
