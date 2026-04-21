<?php
$c = app()->make("App\Http\Controllers\Admin\ReportController");
$res = $c->exportCsv(new \Illuminate\Http\Request());
file_put_contents('C:/Users/Admin/.gemini/antigravity/brain/658c8939-cd1c-46ae-8f01-8a3e59c2b718/scratch/bao_cao_he_thong.xlsx', $res->getContent());
echo "Done";
