<?php

declare (strict_types=1);
namespace Rector\PHPStanStaticTypeMapper\TypeMapper;

use PhpParser\Node;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\Type\Accessory\HasMethodType;
use PHPStan\Type\Type;
use Rector\PHPStanStaticTypeMapper\Contract\TypeMapperInterface;
/**
 * @implements TypeMapperInterface<HasMethodType>
 */
final class HasMethodTypeMapper implements TypeMapperInterface
{
    /**
     * @readonly
     * @var \Rector\PHPStanStaticTypeMapper\TypeMapper\ObjectWithoutClassTypeMapper
     */
    private $objectWithoutClassTypeMapper;
    public function __construct(\Rector\PHPStanStaticTypeMapper\TypeMapper\ObjectWithoutClassTypeMapper $objectWithoutClassTypeMapper)
    {
        $this->objectWithoutClassTypeMapper = $objectWithoutClassTypeMapper;
    }
    /**
     * @return class-string<Type>
     */
    public function getNodeClass() : string
    {
        return HasMethodType::class;
    }
    /**
     * @param HasMethodType $type
     */
    public function mapToPHPStanPhpDocTypeNode(Type $type, string $typeKind) : TypeNode
    {
        return new IdentifierTypeNode('object');
    }
    /**
     * @param HasMethodType $type
     */
    public function mapToPhpParserNode(Type $type, string $typeKind) : ?Node
    {
        return $this->objectWithoutClassTypeMapper->mapToPhpParserNode($type, $typeKind);
    }
}
