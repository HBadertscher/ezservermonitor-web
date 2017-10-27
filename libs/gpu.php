<?php
require '../autoload.php';
$Config = new Config();

// Response
$datas = array();

// Get all info from nvidia-smi
$gpu_info = shell_exec('nvidia-smi --query-gpu=index,name,temperature.gpu,utilization.gpu,utilization.memory,memory.total,memory.free,memory.used --format=csv');
$gpu_info_array = explode("\n", $gpu_info);

// remove first row (contains row titles) and last row (always? empty)
foreach(array_slice($gpu_info_array, 1, -1) as $row)
{
  $this_gpu = explode(",", $row);
  $datas[] = array(
    'id' => trim($this_gpu[0]),
    'model' => trim($this_gpu[1]),
    'temperature' => trim($this_gpu[2])." °C",
    'utilization' => trim(str_replace("%", "", $this_gpu[3])),
    'memory_util' => trim(str_replace("%", "", $this_gpu[4])),
    'memory_total' => trim($this_gpu[5]),
    'memory_free' => trim($this_gpu[6]),
    'memory_used' => trim($this_gpu[7])
  );
}

echo json_encode($datas);
