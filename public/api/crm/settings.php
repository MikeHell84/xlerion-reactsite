<?php
header('Content-Type: application/json');
$sample = [
  'roles'=>['admin','sales','support','supervisor'],
  'pipelines'=>['default'=>['new','contacted','proposal','won','lost']]
];
echo json_encode(['ok'=>true,'data'=>$sample]);
