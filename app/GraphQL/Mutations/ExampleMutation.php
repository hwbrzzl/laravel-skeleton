<?php

namespace App\GraphQL\Mutations;

use App\GraphQL\BaseMutation;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ExampleMutation extends BaseMutation
{
    public function index($rootValue, array $args, GraphQLContext $context = null, ResolveInfo $resolveInfo)
    {

    }


}
