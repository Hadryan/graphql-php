<?php

namespace Digia\GraphQL\Error;

use Digia\GraphQL\Language\Node\NodeInterface;
use Digia\GraphQL\Language\Source;
use Digia\GraphQL\Language\SourceLocation;
use Digia\GraphQL\Util\ArrayToJsonTrait;
use Digia\GraphQL\Util\SerializationInterface;

/**
 * An GraphQLException describes an exception thrown during the execute
 * phase of performing a GraphQL operation. In addition to a message
 * and stack trace, it also includes information about the locations in a
 * GraphQL document and/or execution result that correspond to the Error.
 */
class GraphQLException extends AbstractException implements SerializationInterface
{
    use ArrayToJsonTrait;

    /**
     * An array of { line, column } locations within the source GraphQL document
     * which correspond to this error.
     *
     * Errors during validation often contain multiple locations, for example to
     * point out two things with the same name. Errors during execution include a
     * single location, the field which produced the error.
     *
     * @var array|null
     */
    protected $locations;

    /**
     * An array describing the JSON-path into the execution response which
     * corresponds to this error. Only included for errors during execution.
     *
     * @var string[]|null
     */
    protected $path;

    /**
     * An array of GraphQL AST Nodes corresponding to this error.
     *
     * @var NodeInterface[]|null
     */
    protected $nodes;

    /**
     * The source GraphQL document for the first location of this error.
     *
     * Note that if this Error represents more than one node, the source may not
     * represent nodes after the first node.
     *
     * @var Source|null
     */
    protected $source;

    /**
     * An array of character offsets within the source GraphQL document
     * which correspond to this error.
     *
     * @var int[]|null
     */
    protected $positions;

    /**
     * Extension fields to add to the formatted error.
     *
     * @var array|null
     */
    protected $extensions;

    /**
     * @var null|\Throwable
     */
    protected $originalException;

    /**
     * ExecutionException constructor.
     *
     * @param string          $message
     * @param array|null      $nodes
     * @param Source|null     $source
     * @param array|null      $positions
     * @param array|null      $path
     * @param array|null      $extensions
     * @param \Throwable|null $originalException
     */
    public function __construct(
        string $message,
        ?array $nodes = null,
        ?Source $source = null,
        ?array $positions = null,
        ?array $path = null,
        ?array $extensions = null,
        ?\Throwable $originalException = null
    ) {
        parent::__construct($message);

        $this->resolveNodes($nodes);
        $this->resolveSource($source);
        $this->resolvePositions($positions);
        $this->resolveLocations($positions, $source);

        $this->path              = $path;
        $this->extensions        = $extensions;
        $this->originalException = $originalException;
    }

    /**
     * @return NodeInterface[]
     */
    public function getNodes(): ?array
    {
        return $this->nodes;
    }

    /**
     * @return bool
     */
    public function hasSource(): bool
    {
        return null !== $this->source;
    }

    /**
     * @return Source|null
     */
    public function getSource(): ?Source
    {
        return $this->source;
    }

    /**
     * @return int[]|null
     */
    public function getPositions(): ?array
    {
        return $this->positions;
    }

    /**
     * @return bool
     */
    public function hasLocations(): bool
    {
        return !empty($this->locations);
    }

    /**
     * @return array|null
     */
    public function getLocations(): ?array
    {
        return $this->locations;
    }

    /**
     * @return array|null
     */
    public function getLocationsAsArray(): ?array
    {
        return !empty($this->locations) ? \array_map(function (SourceLocation $location) {
            return $location->toArray();
        }, $this->locations) : null;
    }

    /**
     * @return array|null
     */
    public function getPath(): ?array
    {
        return $this->path;
    }

    /**
     * @return array|null
     */
    public function getExtensions(): ?array
    {
        return $this->extensions;
    }

    /**
     * @param array|null $extensions
     * @return self
     */
    public function setExtensions(?array $extensions): self
    {
        $this->extensions = $extensions;
        return $this;
    }

    /**
     * @return \Throwable|null
     */
    public function getOriginalException(): ?\Throwable
    {
        return $this->originalException;
    }

    /**
     * @return null|string
     */
    public function getOriginalErrorMessage(): ?string
    {
        return null !== $this->originalException ? $this->originalException->getMessage() : null;
    }

    /**
     * @inheritdoc
     */
    public function toArray(): array
    {
        $result = [
            'message'   => $this->message,
            // TODO: Do not include `locations` if `null` (similar to `path` and `extensions`).
            'locations' => $this->getLocationsAsArray(),
        ];

        if (null !== $this->path) {
            $result['path'] = $this->path;
        }

        if (null !== $this->extensions) {
            $result['extensions'] = $this->extensions;
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function __toString(): string
    {
        return printError($this);
    }

    /**
     * @param array|null $nodes
     * @return $this
     */
    protected function resolveNodes(?array $nodes): self
    {
        if (\is_array($nodes)) {
            $nodes = !empty($nodes) ? $nodes : [];
        } else {
            $nodes = [$nodes];
        }

        $this->nodes = \array_filter($nodes, function ($node) {
            return null !== $node;
        });

        return $this;
    }

    /**
     * @param Source|null $source
     * @return $this
     */
    protected function resolveSource(?Source $source): self
    {
        if (null === $source && !empty($this->nodes)) {
            $firstNode = $this->nodes[0] ?? null;
            $location  = null !== $firstNode ? $firstNode->getLocation() : null;
            $source    = null !== $location ? $location->getSource() : null;
        }

        $this->source = $source;

        return $this;
    }

    /**
     * @param array|null $positions
     * @return $this
     */
    protected function resolvePositions(?array $positions): self
    {
        if (null === $positions && !empty($this->nodes)) {
            $positions = \array_reduce($this->nodes, function (array $list, ?NodeInterface $node) {
                if (null !== $node) {
                    $location = $node->getLocation();
                    if (null !== $location) {
                        $list[] = $location->getStart();
                    }
                }
                return $list;
            }, []);
        }

        if (null !== $positions && empty($positions)) {
            $positions = null;
        }

        $this->positions = $positions;

        return $this;
    }

    /**
     * @param array|null  $positions
     * @param Source|null $source
     * @return $this
     */
    protected function resolveLocations(?array $positions, ?Source $source): self
    {
        $locations = null;

        if (null !== $positions && null !== $source) {
            $locations = \array_map(function ($position) use ($source) {
                return SourceLocation::fromSource($source, $position);
            }, $positions);
        } elseif (!empty($this->nodes)) {
            $locations = \array_reduce($this->nodes, function (array $list, NodeInterface $node) {
                $location = $node->getLocation();
                if (null !== $location) {
                    $list[] = SourceLocation::fromSource($location->getSource(), $location->getStart());
                }
                return $list;
            }, []);
        }

        if ($locations !== null) {
            $this->locations = $locations;
        }

        return $this;
    }
}
