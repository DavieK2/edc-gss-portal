<?php
namespace App\Services;

use Carbon\Carbon;
use Closure;

class DataTable {

    public $start_date;
    public $end_date;
    public $search_query;
    public $page;
    public $pageLength;
    public $columns;
    public $filters;

    public function __construct()
    {
        $this->start_date = is_null(request('startDate')) ? null : Carbon::parse(request('startDate'))->startOfDay()->toDateTimeString();
        $this->end_date = is_null(request('endDate')) ? null : Carbon::parse(request('endDate'))->endOfDay()->toDateTimeString();
        $this->search_query = isset(request('search')['value']) ? request('search')['value'] : null;
        $this->page = intval((request('start') + 10) / 10);
        $this->pageLength = request('length');
        $this->columns = request('column');
        $this->filters = request('filters');
    }


    public function search($builder, array $columns, $rootTable, Closure $closure = null, $withSession = true)
    {
        $fields = explode(',', $this->columns);

        $fields = array_intersect($fields, array_keys($columns));

      
        foreach( $fields as $field ){

            $builder->orWhere(function($query) use($columns, $field, $closure, $rootTable){

                $query->where($columns[$field].".".$field, 'like', '%'.$this->search_query.'%');

                $query = $this->filter($query, $columns);

                $query = $this->dateFilter($query, $rootTable);

                if( $closure instanceof Closure ) $closure( $query );
            });
            
        }        

        if( $withSession ){

            return $this->pageLength == -1 
                    ? $builder->latest($rootTable.'.created_at')->activeSession()->paginate($builder->count(), page: $this->page) 
                    : $builder->latest($rootTable.'.created_at')->activeSession()->paginate($this->pageLength, page: $this->page);
        }

        return $this->pageLength == -1 
                ? $builder->latest($rootTable.'.created_at')->paginate($builder->count(), page: $this->page) 
                : $builder->latest($rootTable.'.created_at')->paginate($this->pageLength, page: $this->page);
    }

    public function response($items = [], $collection = null, $status = true)
    {
        return response()->json([
            'draw' => intval(request('draw')),
            'recordsTotal' => $status ? $collection->total() : 0,
            'recordsFiltered' => $status ? $collection->total() : 0,
            'data' => $status ? $items : []
        ]);
    }


    protected function filter($builder, $columns)
    {
        if( $this->filters ){

            foreach($this->filters as $filter){

                foreach($filter as $key => $value){

                    if( is_null( $value) )  continue;
                    
                    $builder->where(function($query) use($key, $value, $columns) {
                        $query->where($columns[$key].".".$key, $value);
                    });  
                } 
            }
        }

        return $builder;
    }

    protected function dateFilter($builder, $rootTable)
    {

        if( $this->start_date && $this->end_date ){
          
            $builder->where(function($query) use($rootTable){
                $query->where($rootTable.'.created_at', ">=" , $this->start_date)
                      ->where($rootTable.'.created_at', "<=" , $this->end_date);
            });
        }

        return $builder;
    }
}