<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 23.12.2015
 * Time: 18:41
 */

namespace Simplified\Database\SqlBuilder;


class BaseQuery {
    private $builder;
    public function __construct(Builder $builder) {
        $this->builder = $builder;
    }
}