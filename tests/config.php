<?php 
$config =  (object)[
    'link' => 'http://tests',
    'db' => (object)[
        'address'=>'localhost',
        'login'=>'root',
        'password'=>'',
        'name'=>'tests'
    ],
    'domen' => 'tests',
    'pages' => (object)[
    	'test'=>'http://tests/t',
    ],
];

$ROOT = $_SERVER['DOCUMENT_ROOT'];
$db = mysqli_connect($config->db->address, $config->db->login, $config->db->password, $config->db->name);
    mysqli_query($db, "SET NAMES 'utf8'");

?>