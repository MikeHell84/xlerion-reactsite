<?php
header('Content-Type: application/json');
$sample = [
  ['id'=>1,'title'=>'Proyecto Alpha','stage'=>'proposal','value'=>12000,'probability'=>0.6,'owner'=>'sales1'],
  ['id'=>2,'title'=>'Implementación Beta','stage'=>'negotiation','value'=>4800,'probability'=>0.3,'owner'=>'sales2']
];
echo json_encode(['ok'=>true,'data'=>$sample]);
