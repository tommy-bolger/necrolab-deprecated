<?php
namespace Modules\Necrolab\Models\Dailies\Rankings;

use \Modules\Necrolab\Models\Necrolab;

class Entry
extends Necrolab {
    public static function getFormattedApiRecord($data_row) {
        $processed_row = array();   
        
        $total_points = $data_row['total_points'];
        $total_dailies = $data_row['total_dailies'];
        $sum_of_ranks = $data_row['sum_of_ranks'];
        $total_score = $data_row['total_score'];

        $processed_row['first_place_ranks'] = $data_row['first_place_ranks'];
        $processed_row['top_5_ranks'] = $data_row['top_5_ranks'];
        $processed_row['top_10_ranks'] = $data_row['top_10_ranks'];
        $processed_row['top_20_ranks'] = $data_row['top_20_ranks'];
        $processed_row['top_50_ranks'] = $data_row['top_50_ranks'];
        $processed_row['top_100_ranks'] = $data_row['top_100_ranks'];
        $processed_row['total_points'] = $data_row['total_points'];
        $processed_row['points_per_day'] = ($total_points / $total_dailies);
        $processed_row['total_dailies'] = $data_row['total_dailies'];
        $processed_row['total_wins'] = $data_row['total_wins'];
        $processed_row['total_score'] = $total_score;
        $processed_row['score_per_day'] = ($total_score / $total_dailies);
        $processed_row['rank'] = $data_row['rank'];
        $processed_row['sum_of_ranks'] = $sum_of_ranks;
        $processed_row['average_rank'] = ($sum_of_ranks / $total_dailies);
        
        return $processed_row;
    }
}