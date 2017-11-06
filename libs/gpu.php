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

  // Get all current processes on this GPU
  $shell_command_proc = 'nvidia-smi '
    .'--id='.$this_gpu[0].' '
    .'--query-compute-apps="pid" '
    .'--format=csv,noheader';
  $list_of_processes = trim(shell_exec($shell_command_proc));
  if (empty($list_of_processes)) {
    $users = "None";
  }
  else
  {
    $list_of_users = array();
    $list_of_processes = explode("\n", $list_of_processes);
    foreach($list_of_processes as $process)
    {
      $this_user = shell_exec('ps -o user -p '.$process.' --noheader');
      $list_of_users = $list_of_users.array_push(trim($this_user));
    }
    $users = implode(', ', $list_of_users);
  }

  // Create response
  $datas[] = array(
    'id' => trim($this_gpu[0]),
    'model' => trim($this_gpu[1]),
    'temperature' => trim($this_gpu[2])." °C",
    'utilization' => trim(str_replace("%", "", $this_gpu[3])),
    'memory_total' => trim($this_gpu[4]),
    'memory_free' => trim($this_gpu[5]),
    'memory_used' => trim($this_gpu[6]),
    'speed' => trim($this_gpu[7])." / ".trim($this_gpu[8]),
    'power' => trim($this_gpu[9])." / ".trim($this_gpu[10]),
    'users' => $users
  );
}

echo json_encode($datas);
