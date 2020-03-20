<?php

namespace App\GraphQL\Queries;

use App\GraphQL\BaseQuery;
use App\Models\Admin;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class AdminPaginatorQuery extends BaseQuery
{

    protected function wheres()
    {
        return [
            'name.like' => $this->fireInput('name', function ($value) {
                return '%'.$value.'%';
            }),
        ];
    }

    protected function order()
    {
        return ['-id'];
    }

    public function adminPaginator($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        return Admin::getList($this->getConditions($args));
    }

}
