<?php

namespace Digia\GraphQL\Language\AST\Node;

use Digia\GraphQL\Language\AST\KindEnum;

class InputValueDefinitionNode implements NodeInterface
{

    use KindTrait;

    /**
     * @inheritdoc
     */
    protected function configure(): array
    {
        return [
            'kind' => KindEnum::INPUT_VALUE_DEFINITION,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getValue()
    {
        // TODO: Implement getValue() method.
    }
}
