<?php

namespace Digia\GraphQL\Language\AST\Builder;

use Digia\GraphQL\Language\AST\Builder\Behavior\ParseKindTrait;
use Digia\GraphQL\Language\AST\Builder\Behavior\ParseLocationTrait;
use Digia\GraphQL\Language\AST\Builder\Behavior\ParseTypeTrait;
use Digia\GraphQL\Language\AST\NodeKindEnum;
use Digia\GraphQL\Language\AST\Node\Contract\NodeInterface;
use Digia\GraphQL\Language\AST\Node\ListTypeNode;

class ListTypeBuilder extends AbstractBuilder
{

    use ParseKindTrait;
    use ParseTypeTrait;
    use ParseLocationTrait;

    /**
     * @inheritdoc
     */
    public function build(array $ast): NodeInterface
    {
        return new ListTypeNode([
            'kind' => $this->parseKind($ast),
            'type' => $this->parseType($ast),
            'loc'  => $this->parseLocation($ast),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function supportsKind(string $kind): bool
    {
        return $kind === NodeKindEnum::LIST_TYPE;
    }
}
