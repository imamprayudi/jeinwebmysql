<?php 
class Helper 
{
    public static function getEloquentSqlWithBindings($query)
    {
        return vsprintf(str_replace('?', '%s', $query->toSql()), collect($query->getBindings())->map(function ($binding) {
            return is_numeric($binding) ? $binding : "'{$binding}'";
        })->toArray());
    }

    public static function translateQueryMonthlyToMysql($column,$qty,$type='MySQL')
    {
        if($type == 'MySQL'){
           return "LEFT($column,$qty)";
        }
        else{
            return "convert(varchar($qty),$column,120)";
        }
    }

    public static function translateQueryTopToMysql($query,$limit,$type="MySQL")
    {
        if ($type == 'MySQL') {
            return $query . " Limit ". $limit;
        } else {
            return str_replace("select","SELECT TOP $limit ",$query);
        }
    }
    public static function translateQueryGetdateToMysql($type="MySQL")
    {
        if ($type == 'MySQL') {
            return "now()";
        } else {
            return "getdate()";
        }
    }
}
