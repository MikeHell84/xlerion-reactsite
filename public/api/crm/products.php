<?php
header('Content-Type: application/json');
$sample = [
  ['id'=>1,'sku'=>'XT-100','name'=>'Xlerion Toolkit','price'=>499.00,'recurring'=>false],
  ['id'=>2,'sku'=>'SVC-MONTH','name'=>'Soporte Mensual','price'=>49.99,'recurring'=>true]
];
echo json_encode(['ok'=>true,'data'=>$sample]);
