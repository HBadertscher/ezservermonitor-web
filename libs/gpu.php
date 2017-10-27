<?php
require '../autoload.php';
$Config = new Config();

// Response
$datas = array();

// Get all info from nvidia-smi
$shell_cmd = 'nvidia-smi'
  .' --query-gpu=index,name,temperature.gpu,utilization.gpu,'
  .'memory.total,memory.free,memory.used,'
  .'clocks.current.sm,clocks.max.sm,'
  .'power.draw,power.limit'
  .' --format=csv,noheader';
$gpu_info = shell_exec($shell_cmd);
$gpu_info_array = explode("\n", trim($gpu_info));

foreach($gpu_info_array as $row)
{
  $this_gpu = explode(",", $row);
  $datas[] = array(
    'id' => trim($this_gpu[0]),
    'model' => trim($this_gpu[1]),
    'temperature' => trim($this_gpu[2])." °C",
    'utilization' => trim(str_replace("%", "", $this_gpu[3])),
    'memory_total' => trim($this_gpu[4]),
    'memory_free' => trim($this_gpu[5]),
    'memory_used' => trim($this_gpu[6]),
    'speed' => trim($this_gpu[7])." / ".trim($this_gpu[8]),
    'power' => trim($this_gpu[9])." / ".trim($this_gpu[10])
  );
}

echo json_encode($datas);
