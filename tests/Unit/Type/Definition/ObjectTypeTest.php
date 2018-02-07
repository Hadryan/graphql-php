<?php

namespace Digia\GraphQL\Test\Unit\Type\Definition;

use Digia\GraphQL\Language\AST\Node\ObjectTypeDefinitionNode;
use Digia\GraphQL\Test\Unit\TestCase;
use Digia\GraphQL\Type\Definition\EnumType;
use Digia\GraphQL\Type\Definition\EnumValue;
use Digia\GraphQL\Type\Definition\Field;
use Digia\GraphQL\Type\Definition\IntType;
use Digia\GraphQL\Type\Definition\ObjectType;
use Digia\GraphQL\Type\Definition\StringType;

/**
 * Class ObjectTypeTest
 *
 * @package Digia\GraphQL\Test\Unit\Type\Definition
 * @property ObjectType $type
 */
class ObjectTypeTest extends AbstractTypeTestCase
{

    protected function setUp()
    {
        $this->config = [
            'name'        => 'Address',
            'description' => 'A street address.',
            'fields'      => [
                [
                    'name' => 'street',
                    'type' => new StringType(),
                ],
                [
                    'name' => 'number',
                    'type' => new IntType(),
                ],
                [
                    'name'    => 'formatted',
                    'type'    => new StringType(),
                    'resolve' => function ($obj) {
                        return $obj['number'] . ' ' . $obj['street'];
                    }
                ]
            ],
            'astNode'     => new ObjectTypeDefinitionNode(),
        ];

        $this->type = new ObjectType($this->config);
    }

    /**
     * @throws \Exception
     */
    public function testConfig()
    {
        $this->assertEquals($this->config['name'], $this->type->getName());
        $this->assertEquals($this->config['description'], $this->type->getDescription());
        $this->assertEquals($this->config, $this->type->getConfig());
        $this->assertInstanceOf(ObjectTypeDefinitionNode::class, $this->type->getAstNode());
    }

    /***
     * @throws \Exception
     */
    public function testGetFields()
    {
        $fields = $this->type->getFields();

        $this->assertTrue(is_array($fields));
        $this->assertEquals(3, count($fields));
        $this->assertInstanceOf(Field::class, array_pop($fields));
    }
}
