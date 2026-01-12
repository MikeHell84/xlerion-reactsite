<?php
header('Content-Type: application/json');
$sample = [
  ['id'=>101,'type'=>'quote','client_id'=>1,'amount'=>12000,'status'=>'pending'],
  ['id'=>102,'type'=>'invoice','client_id'=>2,'amount'=>4800,'status'=>'paid']
];
echo json_encode(['ok'=>true,'data'=>$sample]);
