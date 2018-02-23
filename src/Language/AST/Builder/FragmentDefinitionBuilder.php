<?php

namespace Digia\GraphQL\Language\AST\Builder;

use Digia\GraphQL\Language\AST\Node\Contract\NodeInterface;
use Digia\GraphQL\Language\AST\Node\FragmentSpreadNode;
use Digia\GraphQL\Language\AST\NodeKindEnum;

class FragmentDefinitionBuilder extends AbstractBuilder
{

    /**
     * @inheritdoc
     */
    public function build(array $ast): NodeInterface
    {
        return new FragmentSpreadNode([
            'name'          => $this->buildOne($ast, 'name'),
            'typeCondition' => $this->buildOne($ast, 'typeCondition'),
            'directives'    => $this->buildMany($ast, 'directives'),
            'selectionSet'  => $this->buildOne($ast, 'selectionSet'),
            'location'      => $this->createLocation($ast),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function supportsKind(string $kind): bool
    {
        return $kind === NodeKindEnum::FRAGMENT_DEFINITION;
    }
}