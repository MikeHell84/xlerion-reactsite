<?php
header('Content-Type: application/json');
$sample = [
  ['id'=>1,'channel'=>'email','subject'=>'Bienvenida','template'=>'welcome_v1'],
  ['id'=>2,'channel'=>'whatsapp','subject'=>'Recordatorio','template'=>'reminder_v1']
];
echo json_encode(['ok'=>true,'data'=>$sample]);
