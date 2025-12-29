<?php
header('Content-Type: application/json');
$sample = [
  ['id'=>1,'trigger'=>'lead.stage==qualified','action'=>'send_email','template'=>'on_qualify'],
  ['id'=>2,'trigger'=>'invoice.status==overdue','action'=>'create_task','task_template'=>'followup_overdue']
];
echo json_encode(['ok'=>true,'data'=>$sample]);
