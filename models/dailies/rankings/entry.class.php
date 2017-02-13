<?php
namespace Modules\Necrolab\Models\Dailies\Rankings;

use \Modules\Necrolab\Models\Necrolab;

class Entry
extends Necrolab {
    public static function getFormattedApiRecord($data_row) {
        $processed_row = array();
    
        $processed_row['first_place_ranks'] = $data_row['first_place_ranks'];
        $processed_row['top_5_ranks'] = $data_row['top_5_ranks'];
        $processed_row['top_10_ranks'] = $data_row['top_10_ranks'];
        $processed_row['top_20_ranks'] = $data_row['top_20_ranks'];
        $processed_row['top_50_ranks'] = $data_row['top_50_ranks'];
        $processed_row['top_100_ranks'] = $data_row['top_100_ranks'];
        $processed_row['total_points'] = $data_row['total_points'];
        $processed_row['points_per_day'] = $data_row['points_per_day'];
        $processed_row['total_dailies'] = $data_row['total_dailies'];
        $processed_row['total_wins'] = $data_row['total_wins'];
        $processed_row['average_rank'] = $data_row['average_rank'];
        $processed_row['sum_of_ranks'] = $data_row['sum_of_ranks'];
        $processed_row['rank'] = $data_row['rank'];
        
        return $processed_row;
    }
}