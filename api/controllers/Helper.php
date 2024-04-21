<?php 
class Helper 
{
    public static function getEloquentSqlWithBindings($query)
    {
        return vsprintf(str_replace('?', '%s', $query->toSql()), collect($query->getBindings())->map(function ($binding) {
            return is_numeric($binding) ? $binding : "'{$binding}'";
        })->toArray());
    }

    public static function translateQueryMonthlyToMysql($column,$qty,$type='SQL')
    {
        if($type == 'MySQL'){
           return "LEFT($column,$qty)";
        }
        else{
            return "convert(varchar($qty),$column,120)";
        }
    }
}
