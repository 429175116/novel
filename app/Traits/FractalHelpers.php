<?php

namespace App\Traits;

use Spatie\Fractalistic\Fractal;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use App\Ext\DataArraySerializer;

trait FractalHelpers
{
    /**
     * Fractal 序列化转换单个Object
     */
    protected function fractalItem($item, $transformer, $includes=[])
    {
        $data = Fractal::create($item, $transformer)
            ->parseIncludes($includes)
            ->serializeWith(new DataArraySerializer())
            ->toArray();
        return $data['data'];
    }

    /**
     * Fractal 序列化转换Object数组
     */
    protected function fractalItems($items, $transformer, $includes=[])
    {
        $data = Fractal::create($items, $transformer)
            ->parseIncludes($includes)
            ->serializeWith(new DataArraySerializer())
            ->toArray();
        return $data['data'];
    }

    /**
     * Fractal 序列化转换Object数组并添加分页信息
     */
    protected function factalPaginator($paginator, $transformer, $includes=[])
    {
        $data = Fractal::create()
            ->collection($paginator->getCollection())
            ->transformWith($transformer)
            ->parseIncludes($includes)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->serializeWith(new DataArraySerializer())
            ->toArray();
        return $data;
    }

    /**
     * 没有transformer的分页
     * @param $paginator
     * @return array
     */
    protected function factalPaginatorNoTransformer($paginator) {
        $data = Fractal::create()
            ->collection($paginator->getCollection())
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->serializeWith(new DataArraySerializer())
            ->toArray();
        return $data;
    }
}