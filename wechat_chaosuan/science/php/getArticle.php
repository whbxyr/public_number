<?php
header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['kind']) || empty($_GET['kind'])) {
    echo $_GET['kind'].'参数错误';
    exit;
}

if (!isset($_GET['num']) || empty($_GET['num'])) {
    echo $_GET['num'].'参数错误';
    exit;
}

if (!isset($_GET['page']) || empty($_GET['page'])) {
    echo $_GET['page'].'参数错误';
    exit;
}

// 连接数据库
include("connect.php");

$kind = $_GET['kind'];
$select_query = 'select * from text';
if ($kind === 'all') {
    $select_query .= '';
}
else if ($kind === 'front') {
    $select_query .= ' where kind="前沿科技"';	
}
else {
	$select_query .= ' where kind="超算生活"';
}

$data = array();

$select_query_result = $db->query($select_query);
$num_select_query_result = $select_query_result->num_rows;

// 计算所要查询文章的总页数，每页4条文章
$data['allpage'] = ceil($num_select_query_result/4);

// 从数据库中查询当前页的文章详细信息
$page = $_GET['page'];
if ($page != $data['allpage']) {
    $limit = $num_select_query_result - $page * 4;
    $select_query .= ' limit '.$limit.',4';
}
else {
    $limit = 0;
    $select_query .= ' limit '.$limit.','.($num_select_query_result - (4 * ($data['allpage'] - 1)));
}

$select_result = $db->query($select_query);
// 获得查询结果数量
$num_select_result = $select_result->num_rows;
// 打印文章列表，没有则什么都不会打印
for ($i = 0; $i < $num_select_result; $i++) {
    $row = $select_result->fetch_assoc();
    $data['article'.$i]['time'] = $row['time'];
    $data['article'.$i]['kind'] = $row['kind'];
    $data['article'.$i]['title'] = $row['title'];
    $data['article'.$i]['cover'] = $row['cover'];
}


echo json_encode($data);