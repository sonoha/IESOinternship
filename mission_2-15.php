<html>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>入力フォーム／テスト</title>
<body>
<h2><i>入力フォーム／テスト</i></h2>

<table border=1>
	<tr><td rowspan=2>
		<!-入力フォーム
		   「comment」という名前のtextを「mission_2-15.php」にPOSTメソッドで送信->
		<form method="post" action="mission_2-15.php"><input type="hidden" name="type" value="comment">
		<p>　　お名前：<input type="text" name="name">　<br>
		　コメント：<input type="text" name="comment">　<br>
		　パス設定：<input type="password" name="pass" size="13"><input type="submit" value="送信">　</p>
		</form>
	</td>
	<td>
		<!-削除フォーム
		   「delete」という名前の値を「mission_2-15.php」にPOSTメソッドで送信->
		<form method="post" action="mission_2-15.php"><input type="hidden" name="type" value="delete">
		<p>　コメント｜番号<input type="text" name="delete" size="2">　　<input type="submit" value="削除">　<br>
		　　削除　｜パス<input type="password" name="pass" size="15">　</p>
		</form>
	</td></tr>
	<tr><td>
		<!-編集フォーム
		   値「edit_number」とtext「edit_comment」を「mission_2-15.php」にPOSTメソッドで送信->
		<form method="post" action="mission_2-15.php"><input type="hidden" name="type" value="edit">
		<p>　コメント｜番号<input type="text" name="edit_number" size="2">　　<input type="submit" value="編集">　<br>
		　　編集　｜内容<input type="text" name="edit_comment" size="15">　<br>
		　　　　　｜パス<input type="password" name="pass" size="15">　</p>
		</form>
	</td></tr>
</table>


<?php

//DBに接続

//例外処理（エラー時に接続情報が漏洩するのを防ぐ）
try{
	//DBにMysql、データベース名「test」を指定
	$dsn = 'mysql:dbname=test;host=localhost';
	
	//DBに接続するためのユーザー名・パスワードを設定
	$user = '*******';
	$pass = '*******';
	
	//データーベースに接続
	$pdo = new PDO($dsn, $user, $pass);
	
	
	//文字化け対策
	$stmt = $pdo -> query('SET NAMES utf8');
	
	//テーブルをリセットしたい場合、コメントアウトを外す。
	//$deletetable = "DROP TABLE formtest";
	//$result = $pdo -> query($deletetable);
	
	//データベース「test」上にテーブル「formtest」がなければ作成する。IF NOT EXISTSが既にあるかの判定。
	$createtable = 'CREATE TABLE IF NOT EXISTS formtest (
		id int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		name varchar(10) NOT NULL,
		comment varchar(100),
		time char(30) NOT NULL,
		pass char(10) NOT NULL
	) DEFAULT CHARSET=utf8';
	
	//実行
	$result = $pdo -> query($createtable);
	
	
//新規コメント入力
	
	//どのフォームからの送信かの判定
	if( $_POST["type"] == "comment"){
		//フォームが空かの判定
		if( !(empty($_POST["pass"]) == TRUE )){
			if( !(empty($_POST["name"]) == TRUE )){
				if( !(empty($_POST["comment"]) == TRUE )){
					//データベースのテーブル「formtest」にデータを挿入する。
					
					//INSERT文を変数$newinsertに格納
					$newinsert = $pdo -> prepare("INSERT INTO formtest(id, name, comment, time, pass) VALUES (:id, :name, :comment, :time, :pass)");
					
					////時間に関するデータを取得
					$timestamp = time();
					
					//挿入変数を用意
					$name = $_POST["name"];
					$comment = $_POST["comment"];
					$time = date( "Y/m/d H:i:s", $timestamp );
					$pass = $_POST["pass"];
					
					//レコードに変数を入れる。
					//bindParamは第2引数に変数を指定、関数時点で評価。bindValueは第2引数に数値を指定、execute時点で評価。第3引数で型を指定する。
					$newinsert -> bindParam(':id', $id, PDO::PARAM_INT);
					$newinsert -> bindParam(':name', $name, PDO::PARAM_STR);
					$newinsert -> bindParam(':comment', $comment, PDO::PARAM_STR);
					$newinsert -> bindParam(':time', $time, PDO::PARAM_STR);
					$newinsert -> bindParam(':pass', $pass, PDO::PARAM_STR);
					
					//実行
					$newinsert -> execute();
				}
				else{ echo "<font color=red>⚠コメントが未入力です。</font>";}
			}
			else{ echo "<font color=red>⚠お名前が未入力です。</font>";}
		}
		else{ echo "<font color=red>⚠パスが未入力です。</font>";}	}
		
	
//コメント削除
	
	//どのフォームからの送信かの判定
	if( $_POST["type"] == "delete"){
		//フォームが空かの判定
		if( !(empty($_POST["pass"]) == TRUE )){
			if( !(empty($_POST["delete"]) == TRUE )){
				//passの比較
				$deletenum = $_POST["delete"];
				$deletepass = "SELECT * FROM formtest WHERE id='$deletenum'";
				$stmt = $pdo -> query($deletepass);
				
				foreach( $stmt as $row ){
					if( $row['pass'] == $_POST["pass"] ){
						//コメント削除
						$deletedata = "DELETE FROM formtest WHERE id='$deletenum'";
						$result = $pdo -> query($deletedata);
						echo "<font color=red>".$deletenum."のコメントを削除しました。</font>";
					}
					else{ echo "<font color=red>⚠パスが間違っています。</font>"; }
				}
			}
			else{ echo "<font color=red>⚠番号が未入力です。</font>";}
		}
		else{ echo "<font color=red>⚠パスが未入力です。</font>";}
	}
	
	
//コメント編集
	
	//どのフォームからの送信かの判定
	if( $_POST["type"] == "edit"){
		//フォームが空かの判定
		if( !(empty($_POST["pass"]) == TRUE )){
			if( !(empty($_POST["edit_number"]) == TRUE )){
				if( !(empty($_POST["edit_comment"]) == TRUE )){
					//passの比較
					$editnum = $_POST["edit_number"];
					$editpass = "SELECT * FROM formtest WHERE id='$editnum'";
					$stmt = $pdo -> query($editpass);
					
					foreach( $stmt as $row ){
						if( $row['pass'] == $_POST["pass"] ){
							//コメント編集
							$editcom = $_POST["edit_comment"];
							$timestamp = time();
							$time = date( "Y/m/d H:i:s（編集済）", $timestamp );
							
							$editdata = "UPDATE formtest SET comment='$editcom', time='$time' WHERE id='$editnum'";
							$result = $pdo -> query($editdata);
							echo "<font color='#87CEEB'>".$editnum."のコメントを編集しました。</font>";
						}
						else{ echo "<font color=red>⚠パスが間違っています。</font>"; }
					}
				}
				else{ echo "<font color=red>⚠編集内容が未入力です。</font>";}
			}
			else{ echo "<font color=red>⚠番号が未入力です。</font>";}
		}
		else{ echo "<font color=red>⚠パスが未入力です。</font>";}
	}
	
	
//コメント表示
	
	echo "<br /><br />*.｡:ﾟ+..｡*ﾟ+.｡*ﾟ+.*.｡:ﾟ+..｡*ﾟ+ コメントログ .｡*ﾟ+.*.｡:ﾟ+..｡*ﾟ+.｡*ﾟ+.*.｡:ﾟ+<br /><br />";
	
	//テーブルからデータを取得（ORDER BYで昇順にソート）
	$showdata = "SELECT * FROM formtest ORDER BY id";
	//実行・結果取得
	$result = $pdo -> query($showdata);
	
	//ループを用いてデータを表示
	foreach( $result as $row ){
		echo $row['id']."<font color='#87CEEB'> ".$row['name']."</font><br>";
		echo $row['comment']."<br>";
		echo "<font size='0.7'>投稿日時：".$row['time']."</font><br>";
		echo "<hr align='left' width='520px' height='2'>";
	}
	
	
	//接続終了
	$pdo = null;
}
catch(PDOException $e){
	//エラー出力
	echo "データベースエラー（PDOエラー）<br>";
	//エラーの詳細
	var_dump($e->getMessage());
}
?>

</body>
</html>
