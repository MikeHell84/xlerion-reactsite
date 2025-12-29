<?php
header('Content-Type: application/json');
$sample = [
  ['id'=>1,'type'=>'call','subject'=>'Follow-up ACME','due_date'=>'2026-01-05','assigned_to'=>'user1'],
  ['id'=>2,'type'=>'meeting','subject'=>'Demo Producto','due_date'=>'2026-01-10','assigned_to'=>'user2']
];
echo json_encode(['ok'=>true,'data'=>$sample]);
