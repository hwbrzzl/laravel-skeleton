<?php

namespace App\GraphQL\Queries\Fxs;

use DB;
use Illuminate\Database\Eloquent\Builder;
use MatrixLab\LaravelAdvancedSearch\ModelScope;
use MatrixLab\LaravelAdvancedSearch\When;

class ExampleBuilder
{

    public function wheres()
    {
        $example = [
            'status',
            // 如果传参 status=1 的话 where status = '1'；
            // 如果 status 前端没有传值，那么不会构造
            // gt >    ge gte >=     lt <    le lte <=
            'created_at.gt' => $this->fireInput('year', function ($year) {
                return carbon($year.'-01-01 00:00:00');
            }),
            // 如果 year 不传值，什么都不会构造。
            //如果传值 year=2018，那么就会执行闭包方法， 根据闭包结果构造 where year>'2018-01-01 00:00:00'

            'name.like' => $this->appendInput('name', '%'),
            // 如果 name 不传值，什么都不会构造。
            // 如果传值 name=张 ，那么就会构造 where name like '张%'

            'id.in' => $this->getInputArgs('ids', []),
            // 如果 ids 不传值，什么都不会构造，因为默认值为 [] ，构造时会被忽略。
            // 如果传值 ids=[1,3,4] ，那么就会构造 where id in (1,3,4)

            'deleted_at.is_not' => true,
            // 如果判断某个字段是否为 null ，使用 is_not 或者 is ，但是注意对应的值不能为 null ，因为值为 null 时，会被自动跳过

            'age' => [
                'gt' => 12,
                'lt' => 16,
            ],
            // where age > 12 and age < 16

            'height' => [
                'gt'  => '180',
                'lt'  => '160',
                'mix' => 'or',
            ],
            // (age > 180 or age < 160)

            DB::raw('3=4'),
            // where 3=4

            function (Builder $q) {
                $q->where('id', 4);
            },
            // where id=4

            new ModelScope('popular'),
            // 会调用的对应的模型  scopePopular 方法

            new ModelScope('older', 60),
            // 等同于
            function (Builder $q) {
                $q->older(60);
            },

            'id'  => When::make(true)->success('34'),
            // where id = 34
            'url' => When::make(false)->success('http://www.baidu.com')->fail('http://www.google.com'),
            // where url='http://www.google.com'
        ];

        return [
            When::make($this->getInputArgs('keyword'))->success(new ModelScope('keyword',
                $this->getInputArgs('keyword'))),
        ];
    }

}
