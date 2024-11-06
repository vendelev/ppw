<?php

$servers1 = ['s01', 's02', 's03' , 's04', 's05', 's06', 's07', 's08', 's09', 's10'];

//$servers2 = ['s01', 's02', 's03' , 's04', 's05', 's06', 's07', 's09', 's10'];
$servers2 = ['s01', 's02', 's03' , 's04', 's05', 's06', 's07', 's08', 's09', 's10', 's11' , 's12' , 's13'];
$vnodes = 500;
$cycle_size = 500_000;

$cycle = [];


function makeCycle($servers, $vnodes, $cycle_size) {

    foreach ($servers as $server) {
        for ($i=1; $i <= $vnodes; $i++) {
            $hash = crc32($server . '_' . $i) % $cycle_size;
            $cycle[$hash] = $server;
        }
    }

    ksort($cycle);
    return $cycle;
}

function makeDiff($c1, $c2, $max) {
    reset($c1);
    reset($c2);
    $i1 = 0;
    $i2 = 0;
    $p = 0;
    $sum = 0;

    while($i1< $max && $i2 <$max) {

        if (false) {
            printf("k:%d v:%d\n", $i1, key($c1));
            printf("k:%d v:%d\n", $i2, key($c2));
            printf("---p:%d s:%d\n", $p , $sum);
        }
        if ($i1 == $i2) {
            next($c1);
            next($c2);
        } elseif ($i1 < $i2) {
            next($c1);
        } else {
            next($c2);
        }



        $i1 = key($c1);
        if (is_null($i1)) {
            $i1 = $max;
        }
        $i2 = key($c2);
        if (is_null($i2)) {
            $i2 = $max;
        }
        if (current($c1) != current($c2)) {
            $sum += min($i1, $i2) - $p;
        }
        $p =  min($i1,$i2) ;
    } 

    return $sum;
}


function getDistribution($c, $max) {
    $p = 0;
    $d = [];
    foreach($c as $i => $s) {
        if (!isset($d[$s])) {
            $d[$s] = 0 ;
        }
        $d[$s] += $i - $p;
        $p = $i;
    }
    reset($c);
    $d[current($c)] += $max - $p;
    ksort($d);
    return $d;
}

$c1 = makeCycle($servers1, $vnodes, $cycle_size);
$c2 = makeCycle($servers2, $vnodes, $cycle_size);

//print_r($c1);
print_r(getDistribution($c1, $cycle_size));

//print_r($c2);
print_r(getDistribution($c2, $cycle_size));


echo makeDiff($c1,$c2,$cycle_size) , "\n";