<?php

namespace App\GraphQL\Queries;

use App\GraphQL\BaseQuery;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ExampleQuery extends BaseQuery
{
    public function index($rootValue, array $args, GraphQLContext $context = null, ResolveInfo $resolveInfo)
    {

    }


}
