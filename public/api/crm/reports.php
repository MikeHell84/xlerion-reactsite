<?php
header('Content-Type: application/json');
$sample = [
  'sales_by_period'=>[['period'=>'2025-12','total'=>32000],['period'=>'2025-11','total'=>28000]],
  'leads_by_source'=>[['source'=>'web','count'=>120],['source'=>'whatsapp','count'=>40]]
];
echo json_encode(['ok'=>true,'data'=>$sample]);
