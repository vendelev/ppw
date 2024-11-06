<?php

ini_set('display_errors', 'On');

header('content-type: text/plain; version=0.0.4; charset=utf-8; escaping=values');

$connect = new mysqli('pinba', 'root');

$result = $connect->query(
    'select 
        script, 
        req_count, 
        req_per_sec, 
        req_time_total/req_count time_avg, 
        ru_utime_total/req_count as ru_utime_avg,
        ru_stime_total/req_count as ru_stime_avg 
    from pinba.report_by_script_name;');

foreach ($result as $row) {
    printf("req_per_sec{script_name=\"%s\"} %f\n",          $row['script'], $row['req_per_sec']);
    printf("script_time_avg{script_name=\"%s\"} %f\n",      $row['script'], $row['time_avg']);
    printf("script_ru_user_avg{script_name=\"%s\"} %f\n",   $row['script'], $row['ru_utime_avg']);
    printf("script_ru_sys_avg{script_name=\"%s\"} %f\n",    $row['script'], $row['ru_stime_avg']);
}

$result = $connect->query('
    select 
        script, 
        block, 
        req_count, 
        hit_per_sec, 
        time_total/hit_count as time_avg, 
        ru_utime_total/hit_count as ru_user_avg,
        ru_stime_total/hit_count as ru_sys_avg
    from pinba.report_script_tag');

foreach ($result as $row) {
    printf("block_hit_per_sec{script_name=\"%s\",block=\"%s\"} %f\n",       $row['script'], $row['block'], $row['hit_per_sec']);
    printf("block_time_avg{script_name=\"%s\",block=\"%s\"} %f\n",          $row['script'], $row['block'], $row['time_avg']);
    printf("block_ru_user_avg{script_name=\"%s\",block=\"%s\"} %f\n",       $row['script'], $row['block'], $row['ru_user_avg']);
    printf("block_ru_system_avg{script_name=\"%s\",block=\"%s\"} %f\n",     $row['script'], $row['block'], $row['ru_sys_avg']);
}