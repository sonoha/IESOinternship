<?php
	//DBにMysql、データベース名・testを指定。
	$dsn = 'mysql:dbname=co_637_it_3919_com;host=localhost';
	
	//DBに接続するためのユーザー名・パスワードを設定
	$user = 'co-637.it.3919.c';
	$pass = '7vufTva';
	
	
	//データーベースに接続
	$pdo = new PDO($dsn, $user, $pass);
	
	//文字化け対策
	$stmt = $pdo->query('SET NAMES utf8');
	
	
	//クエリ内容
	$sql = 'SELECT * FROM answer WHERE res_num='.$_GET['res_num'];
	
	//実行・結果取得
	$result = $pdo -> query($sql);
	
	//出力
	foreach( $result as $row ){
		header( "Content-Type:".$row['img_ext'] );
		echo $row['img_img'];
	}
	
	//接続終了
	$pdo = null;
?>
