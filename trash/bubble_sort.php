<?php

function bubble_sort(array $to_sort): array
{
    $n = count($to_sort);
    for ($i = 0; $i < $n; $i++) {
        for ($j = 0; $j < $n - $i - 1; $j++) {
            if ($to_sort[$j] > $to_sort[$j + 1]) {
                $temp = $to_sort[$j];
                $to_sort[$j] = $to_sort[$j + 1];
                $to_sort[$j + 1] = $temp;
            }
        }
    }
    return $to_sort;
}

$unsorted_array = [64, 34, 25, 12, 22, 11, 90];
$sortedArray = bubble_sort($unsorted_array);

echo "Posortowana tablica: \n";
print_r($sortedArray);

