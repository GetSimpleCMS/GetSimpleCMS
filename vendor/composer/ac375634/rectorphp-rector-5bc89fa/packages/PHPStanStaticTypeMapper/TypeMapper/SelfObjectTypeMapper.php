<?php

declare (strict_types=1);
namespace Rector\PHPStanStaticTypeMapper\TypeMapper;

use PhpParser\Node;
use PhpParser\Node\Name;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\Type\Type;
use Rector\PHPStanStaticTypeMapper\Contract\TypeMapperInterface;
use Rector\StaticTypeMapper\ValueObject\Type\SelfObjectType;
/**
 * @implements TypeMapperInterface<SelfObjectType>
 */
final class SelfObjectTypeMapper implements TypeMapperInterface
{
    /**
     * @return class-string<Type>
     */
    public function getNodeClass() : string
    {
        return SelfObjectType::class;
    }
    /**
     * @param SelfObjectType $type
     */
    public function mapToPHPStanPhpDocTypeNode(Type $type, string $typeKind) : TypeNode
    {
        return new IdentifierTypeNode('self');
    }
    /**
     * @param SelfObjectType $type
     */
    public function mapToPhpParserNode(Type $type, string $typeKind) : ?Node
    {
        return new Name('self');
    }
}
