<html>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>みんなで勉強掲示板「Studywith」</title>
<head>
<script type="text/javascript">
	<!--
	//削除フォームを出現させる関数
	function showdelete(num){
		document.getElementById('delete'+num).style.display = "";
		document.getElementById('edit'+num).style.display = "none";
	}
	
	//編集フォームを表示させる関数
	function showedit(num){
		document.getElementById('delete'+num).style.display = "none";
		document.getElementById('edit'+num).style.display = "";
	}
	//-->
</script>
<style type="text/css">
	<!--
	
	body {
		margin: 0; /* marginは外側の余白 */
		padding: 0; /* paddingは内側の余白 */
		background-color: #FFF; /* ページの背景色 */
		color: #000000; /* 全体の文字色 */
		font-size: 100%; /* 全体の文字サイズ */
	}
	
	/* --- 全体のリンクテキスト --- */
		a:link { color: #0000FF; }
		a:visited { color: #800080; }
		a:hover { color: #FF0000; }
		a:active { color: #FF0000; }
	
	/* --- コンテナ --- */
	#container {
		width: 800px; /* ページの幅 */
		margin: 0 auto; /* センタリング */
		background-color: #F8F8FF;
		border-left: 2px #C0C0C0 solid; /* 左の境界線 */
		border-right: 2px #C0C0C0 solid; /* 右の境界線 */
	}
	
	/* --- ヘッダ --- */
	#header {
		font-family:'Century Gothic', sans-serif; /* ヘッダフォント */
		background-color: #D9E5FF; /* ヘッダの背景色 */
		color: #FFF;
		padding: 5px;
	}
	
	/* --- サイドバー --- */
	#nav {
		float: left;
		width: 160px;
		background-color: #FFFFFF; /* サイドバーの背景色 */
		border-radius: 6px;
		margin: 10px;
		padding: 10px;
	}
	
	/* --- メイン --- */
	#a_content {
		float: left;
		width: 520px;
		background-color: #8EB8FF; /* 回答の背景色 */
		border: 2px dashed #fff;
		border-radius: 8px;
		box-shadow: 0 0 0 4px #8EB8FF;
		color: #fff;
		margin: 10px;
		padding: 20px;
	}
	#q_content {
		float: left;
		background-color: #FFFFFF; /* 質問の背景色 */
		border-radius: 8px;
		color: #000;
		margin: 10px 0;
		padding: 10px;
	}
	
	/* --- フッタ --- */
	#footer {
		clear: left; /* フロートのクリア */
		width: 100%;
		background-color: #F0F0FF; /* フッタの背景色 */
	}
	-->
</style>
</head>
<body>


<div id="header"><center><font size=2>みんなで勉強掲示板<br><font size=6>Studywith</font></font></center></div>
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
	//$deletetable = "DROP TABLE テーブル名";
	//$result = $pdo -> query($deletetable);
	
	
//テーブル作成
	//データベース「test」上にテーブル「user_table」がなければ作成する。IF NOT EXISTSが既にあるかの判定。
	$createtable = 'CREATE TABLE IF NOT EXISTS user_table (
		number int(5) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		id varchar(32) NOT NULL,
		name varchar(10) NOT NULL,
		grade int(2),
		gender int(1),
		area varchar(10),
		good_at varchar(10),
		bad_at varchar(10),
		icon_ext varchar(255),
		icon_img mediumblob,
		good int(3) DEFAULT 0,
		bad int(3) DEFAULT 0,
		address varchar(30),
		regist char(12),
		pass char(10) NOT NULL
	) DEFAULT CHARSET=utf8';
	//実行
	$result = $pdo -> query($createtable);

	//データベース「test」上にテーブル「thread」がなければ作成する。IF NOT EXISTSが既にあるかの判定。
	$createtable = 'CREATE TABLE IF NOT EXISTS thread (
		thread_num int(5) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		orner_id varchar(10) NOT NULL,
		q_content varchar(400) NOT NULL,
		q_time char(30) NOT NULL,
		img1_ext varchar(255),
		img1_img mediumblob,
		img2_ext varchar(255),
		img2_img mediumblob,
		img3_ext varchar(255),
		img3_img mediumblob,
		q_good int(3) DEFAULT 0,
		q_bad int(3) DEFAULT 0
	) DEFAULT CHARSET=utf8';
	//実行
	$result = $pdo -> query($createtable);
	
	//データベース「test」上にテーブル「answer」がなければ作成する。IF NOT EXISTSが既にあるかの判定。
	$createtable = 'CREATE TABLE IF NOT EXISTS answer (
		thread_num int(5) NOT NULL,
		res_num int(5) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		orner_id varchar(10) NOT NULL,		
		a_content varchar(400) NOT NULL,
		a_time char(30) NOT NULL,
		img_ext varchar(255),
		img_img mediumblob,
		a_good int(3) DEFAULT 0,
		a_bad int(3) DEFAULT 0
	) DEFAULT CHARSET=utf8';
	//実行
	$result = $pdo -> query($createtable);
	
	
	//セッション開始（状態の受け渡し）
	session_start();
	
	
//ユーザー登録
//本登録
	$idtoken = $_GET['token'];//クリックされたURLからidtokenパラメータを得る
	$u_id = $_GET['id'];//クリックされたURLからidパラメータを得る
	//tokenの比較
	//テーブルからデータを取得（ORDER BYで昇順にソート）
	$showid = "SELECT * FROM user_table";
	//実行・結果取得
	$result = $pdo -> query($showid);
	foreach( $result as $row ){
		//24時間=1440分間本登録されていないデータを削除
		if( date("YmdHi")-$row['regist']>1440 && $row['regist']!=0){
			$deletedata = "DELETE FROM user_table WHERE regist='{$row['regist']}'";
			$result = $pdo -> query($deletedata);
		}
		//既存tokenと取得tokenを比較
		if( $row['id'] == $idtoken ){
			//IDとフラグの書換え
			$registok = "UPDATE user_table SET id='$u_id', regist=0 WHERE id='$idtoken'";
			$result = $pdo -> query($registok);
			//ログインセッション
			$_SESSION['login'] = $u_id;
			echo "本登録を完了しました。<br>";
		}
	}
//仮登録
	//どのフォームからの送信かの判定。
	if( $_POST["type"] == "newlogin"){
		//フォームが空かの判定。
		if( !empty($_POST["pass"]) ){
			if( !empty($_POST["id"]) ){
				if( !empty($_POST["address"]) ){
					if( !empty($_POST["name"]) ){
					//idとaddressの比較
						//テーブルからデータを取得（ORDER BYで昇順にソート）
						$showid = "SELECT * FROM user_table";
						//実行・結果取得
						$result = $pdo -> query($showid);
						//ループして既存IDと新規IDを比較
						foreach( $result as $row ){
							if( strcasecmp($row['id'], $_POST['id']) == 0 ){//strcasecmpは大文字小文字を区別しない比較
								echo "<font color=red>⚠入力したIDは、すでに使用されています。</font>";
								$alart = 1;
								break;
							}elseif( strcasecmp($row['address'], $_POST['address'])==0 && $row['regist']==0 ){//後者は本登録フラグ
								echo "<font color=red>⚠入力したメールアドレスは、すでに使用されています。</font>";
								$alart = 2;
								break;
							}else{ $alart = 0;}
						}
					//idとaddressが使用されていなければ認証メールを送信
						if( $alart == 0 ){
							//INSERT文を変数$newinsertに格納
							$newinsert = $pdo -> prepare("INSERT INTO user_table(
								number,
								id,
								name,
								address,
								regist,
								pass
							) VALUES (
								:number,
								:id,
								:name,
								:address,
								:regist,
								:pass
							)");
							
							//挿入変数を用意
							$idtoken = md5(uniqid(rand()));//一時IDとして32桁の英数字列を生成
							$u_id = $_POST["id"];
							$u_name = $_POST["name"];
							$u_address = $_POST["address"];
							$u_regist = date("YmdHi");;//仮登録フラグ（日時情報を記録）
							$u_pass = $_POST["pass"];
							
							//レコードに変数を入れる。
							//bindParamは第2引数に変数を指定、関数時点で評価。bindValueは第2引数に数値を指定、execute時点で評価。第3引数で型を指定する。
							$newinsert -> bindParam(':number', $n_number, PDO::PARAM_INT);
							$newinsert -> bindParam(':id', $idtoken, PDO::PARAM_STR);
							$newinsert -> bindParam(':name', $u_name, PDO::PARAM_STR);
							$newinsert -> bindParam(':address', $u_address, PDO::PARAM_STR);
							$newinsert -> bindParam(':regist', $u_regist, PDO::PARAM_STR);
							$newinsert -> bindParam(':pass', $u_pass, PDO::PARAM_STR);
							
							//実行
							$newinsert -> execute();
							
							//認証メールを送信
							mb_language("Japanese");
							mb_internal_encoding("UTF-8");
							if( mb_send_mail($u_address, "登録メールアドレスの確認",
							"Studywithより、登録メールアドレスの確認のお願いです。\n\n以下のURLをクリックし、アドレスの認証を行ってください。\n（24時間が経過すると仮登録情報が消去されますのでご注意ください）\n http://co-637.it.99sv-coco.com/mission3/mission_3-6~10_ver1.php"."?id=".$u_id."&token=".$idtoken) ){//URLに?でパラメータを付与
								echo "本登録用メールを送信しました。<br>添付URLからメールアドレスの認証を行ってください。";
							}
							else{
								echo "本登録用メールの送信に失敗しました。<br>操作をやり直してください。";
							}
						}
					}
					else{ echo "<font color=red>⚠ニックネームが未入力です。</font>";}
				}
				else{ echo "<font color=red>⚠メールアドレスが未入力です。</font>";}
			}
			else{ echo "<font color=red>⚠ＩＤが未入力です。</font>";}
		}
		else{ echo "<font color=red>⚠パスが未入力です。</font>";}
	}
	
	
//ログアウト
	//どのフォームからの送信かの判定。
	if( $_POST["type"] == "logout"){ unset($_SESSION['login']);/*セッションを削除*/}
	
	
//ログイン
	//どのフォームからの送信かの判定
	if( $_POST["type"] == "login"){
		//フォームが空かの判定
		if( !empty($_POST["pass"]) ){
			if( !empty($_POST["id"]) ){
				//passの比較
				$loginid = $_POST["id"];
				$loginpass = "SELECT * FROM user_table WHERE id='$loginid'";
				$stmt = $pdo -> query($loginpass);
				
				foreach( $stmt as $row ){
					if( $row['regist'] != 0 ){
						echo "<font color=red>⚠本登録が完了していません。メールを確認してください。</font>";
					}elseif( $row['pass'] == $_POST["pass"] ){
						//loginセッションにidを代入
						$_SESSION['login'] = $loginid;
					}
					else{ echo "<font color=red>⚠パスが間違っています。</font>";}
				}
			}
			else{ echo "<font color=red>⚠ＩＤが未入力です。</font>";}
		}
		else{ echo "<font color=red>⚠パスが未入力です。</font>";}
	}
	
	
//ログインフォーム（ログインしたら非表示）
	if( !isset($_SESSION['login']) ){
		echo"
			<center>
			<h3>ログインフォーム</h3>
			<table border=1 bgcolor=#D9E5FF width=400 height=160>
				<tr bgcolor=#F8F8FF><td>
					<!-ログインフォーム
					   id、passを「mission_3-6~10_ver1.php」にPOSTメソッドで送信->
					<center>
					<form method='post' action='mission_3-6~10_ver1.php'><input type='hidden' name='type' value='login'>
					<p>ＩＤ：<input type='text' name='id' style='ime-mode:disabled'><br>
					パス：<input type='password' name='pass'><br>
					<input type='submit' value='ログイン' style='width:200px; height:30px; background-color:#D9E5FF'></p>
					</form>
					</center>
				</td></tr>
			</table>
			<br><br>
			<h3>新規登録フォーム</h3>
			<table border=1 bgcolor=#D9E5FF width=400 height=160>
				<tr bgcolor=#F8F8FF><td>
					<!-新規登録フォーム
					   id、name、pass、addressを「mission_3-6~10_ver1.php」にPOSTメソッドで送信->
					<center>
					<form method='post' action='mission_3-6~10_ver1.php'><input type='hidden' name='type' value='newlogin'>
					<p>　　　　　ＩＤ：<input type='text' name='id' style='ime-mode:disabled'><br><!-ime-mode:disabledは「英数字入力」->
					　ニックネーム：<input type='text' name='name'><br>
					　　　　　パス：<input type='password' name='pass' maxlength='10'><br><!-最大文字数は10文字->
					メールアドレス：<input type='text' name='address'><br>
					<input type='submit' value='ログイン' style='width:200px; height:30px; background-color:#D9E5FF'></p>
					</form>
					</center>
				</td></tr>
			</table>
			</center>
		";
	}
	else{
		echo "<div id='container'>
			<div id='nav'>
			ログインしています。<br>
		";
		echo $_SESSION['login']."
			<!-ログアウトボタン->
			<center>
			<form method='post' action='mission_3-6~10_ver1.php' style='display:inline'><input type='hidden' name='type' value='logout'>
			<input type='submit' value='ログアウト' style='width:160px; height:30px; background-color:#D9E5FF'>
			</form></center>
			</div>
		";
?>
		
		
<div id="container">
<div id="a_content">
<!-質問スレッド表示->
	<?php if( isset($_SESSION['login']) ): ?>
			<h3><i>掲示板1</i></h3>
			<div id="q_content">
			<p>知恵袋のような掲示板を目指しています。</p>
			
			<p>ログインしている場合はコメントを投稿すると名前つきでコメントを投稿できます。<br>
			その際、画像の添付も可能です。</p>
			
			<p>また、投稿されたコメントに対してgood評価やbad評価をつけることができます。<br>
			（同じコメントに何度も評価できることは現在の課題です）</p>
			
			<p>UIはCSSやJavaScriptを用いて小ぎれいに整えたつもりです。<br>
			一応、複数のブラウザで表示を確かめましたが、ものによってはレイアウト破綻があるかもしれません。</p>
			
			<p>目指すは知恵袋なので、新しいスレッドの投稿ができないのが致命的ですが、その点は現在開発中です。<br>
			特にUIに関して何かアドバイスがあればコメントお願いします。</p>
			</div>
			
			<br>
			<table border=1 width=520>
				<tr><td>
					<center>
					<!-入力フォーム
					   「comment」という名前のtextを「mission_3-6~10_ver1.php」にPOSTメソッドで送信->
					<form method='post' action='mission_3-6~10_ver1.php' enctype='multipart/form-data'><input type='hidden' name='type' value='comment'>
					<p><textarea name='comment' cols=65 rows=4 placeholder='質問に答える' style='width:510px'></textarea><br>
					添付画像：<input type='file' name='img' style='width:250px'> <input type='submit' value='送信' style='width:160px; background-color:#D9E5FF'></p>
					</form></canter>
				</td></tr>
			</table>
	<?php endif; ?>

<?php



$thread_num = 1;//複数スレッドを管理するためのスレッド番号。未完のため一時的に「1」と置く。



	
	
//評価処理
		//どのフォームからの送信かの判定
		if( $_POST["type"] == "good"){
			//データベースのテーブル「answer」にデータを挿入する。
			//goodされたコメント番号を取得
			$res_num = $_POST["res_num"];
			$good = "SELECT * FROM answer WHERE res_num='$res_num'";
			//現在のgood数を取得
			$result = $pdo -> query($good);
			foreach( $result as $row ){ $nowgood = $row['a_good']; }
			//good数を加算し更新
			$nowgood++;
			$good = "UPDATE answer SET a_good='$nowgood' WHERE res_num='$res_num'";
			$result = $pdo -> query($good);
			echo "<span style='background-color:#FFD5EC'>コメントにgood評価しました。</span>";
		}
		if( $_POST["type"] == "bad"){
			//データベースのテーブル「answer」にデータを挿入する。
			//badされたコメント番号を取得
			$res_num = $_POST["res_num"];
			$bad = "SELECT * FROM answer WHERE res_num='$res_num'";
			//現在のbad数を取得
			$result = $pdo -> query($bad);
			foreach( $result as $row ){ $nowbad = $row['a_bad']; }
			//good数を加算し更新
			$nowbad++;
			$bad = "UPDATE answer SET a_bad='$nowbad' WHERE res_num='$res_num'";
			$result = $pdo -> query($bad);
			echo "<span style='background-color:#D7EEFF'>コメントにbad評価しました。</span>";
		}
	
	
//新規コメント入力処理
		//どのフォームからの送信かの判定
		if( $_POST["type"] == "comment"){
			//フォームが空かの判定
			if( !empty($_POST["comment"]) ){
				//データベースのテーブル「answer」にデータを挿入する。
				
				//INSERT文を変数$newinsertに格納
				$newinsert = $pdo -> prepare("INSERT INTO answer(
					thread_num,
					res_num,
					orner_id,
					a_content,
					a_time,
					img_ext,
					img_img,
				) VALUES (
					:thread_num,
					:res_num,
					:orner_id,
					:a_content,
					:a_time,
					:img_ext,
					:img_img,
				)");
					
				//時間に関するデータを取得
				$timestamp = time();
				
				//挿入変数を用意
				$thread_num = 1;
				$orner_id = $_SESSION['login'];
				$a_content = $_POST["comment"];
				$a_time = date( "Y/m/d H:i:s", $timestamp );
				if( $_FILES['img']['error']==0 ){
					$img_ext = $_FILES['img']['type'];
					$img_img = file_get_contents($_FILES['img']['tmp_name']);
				}
				
				//レコードに変数を入れる。
				//bindParamは第2引数に変数を指定、関数時点で評価。bindValueは第2引数に数値を指定、execute時点で評価。第3引数で型を指定する。
				$newinsert -> bindParam(':thread_num', $thread_num, PDO::PARAM_INT);
				$newinsert -> bindParam(':res_num', $res_num, PDO::PARAM_INT);
				$newinsert -> bindParam(':orner_id', $orner_id, PDO::PARAM_STR);
				$newinsert -> bindParam(':a_content', $a_content, PDO::PARAM_STR);
				$newinsert -> bindParam(':a_time', $a_time, PDO::PARAM_STR);
				$newinsert -> bindParam(':img_ext', $img_ext, PDO::PARAM_STR);
				$newinsert -> bindParam(':img_img', $img_img, PDO::PARAM_LOB);
				
				//実行
				$newinsert -> execute();
			}
			else{ echo "<font color=red>⚠コメントが未入力です。</font>";}
		}
		
		
//コメント削除
		//どのフォームからの送信かの判定
		if( $_POST["type"] == "delete"){
			//フォームが空かの判定
			if( !empty($_POST["pass"]) ){
				//passの比較
				$deletenum = $_POST["delete"];
				$deletepass = "SELECT * FROM answer WHERE res_num='$deletenum'";
				$stmt = $pdo -> query($deletepass);
				foreach( $stmt as $row ){
					$deleteid = $row['orner_id'];
				}
				$deletepass = "SELECT * FROM user_table WHERE id='$deleteid'";
				$stmt = $pdo -> query($deletepass);
				foreach( $stmt as $row ){
					if( $row['pass'] == $_POST["pass"] ){
						//コメント削除
						$deletedata = "DELETE FROM answer WHERE res_num='$deletenum'";
						$result = $pdo -> query($deletedata);
						echo "<font color=red>ID:".$deleteid."のコメントを削除しました。</font>";
					}
					else{ echo "<font color=red>⚠パスが間違っています。</font>"; }
				}
			}
			else{ echo "<font color=red>⚠パスが未入力です。</font>";}
		}
		
		
//コメント編集
		//どのフォームからの送信かの判定。
		if( $_POST["type"] == "edit"){
			//フォームが空かの判定。
			if( !empty($_POST["pass"]) ){
				if( !empty($_POST["edit_comment"]) ){
					//passの比較
					$editnum = $_POST["edit_num"];
					$editpass = "SELECT * FROM answer WHERE res_num='$editnum'";
					$stmt = $pdo -> query($editpass);
					foreach( $stmt as $row ){
						$editid = $row['orner_id'];
					}
					
					$editpass = "SELECT * FROM user_table WHERE id='$editid'";
					$stmt = $pdo -> query($editpass);
					
					foreach( $stmt as $row ){
						if( $row['pass'] == $_POST["pass"] ){
							//コメント編集
							$editcom = $_POST["edit_comment"];
							$timestamp = time();
							$time = date( "Y/m/d H:i:s（編集済）", $timestamp );
							
							$editdata = "UPDATE answer SET a_content='$editcom', a_time='$time' WHERE res_num='$editnum'";
							$result = $pdo -> query($editdata);
							echo "<font color=red>ID:".$editid."のコメントを編集しました。</font>";
						}
						else{ echo "<font color=red>⚠パスが間違っています。</font>"; }
					}
				}
				else{ echo "<font color=red>⚠編集内容が未入力です。</font>";}
			}
			else{ echo "<font color=red>⚠パスが未入力です。</font>";}
		}
		
		
//コメント表示
		
		echo "<br /><br />*.｡:ﾟ+..｡*ﾟ+.｡*ﾟ+.*.｡:ﾟ+..｡*ﾟ+ コメントログ .｡*ﾟ+.*.｡:ﾟ+..｡*ﾟ+.｡*ﾟ+.*.｡:ﾟ+<br /><br />";
		
		//テーブルからデータを取得（ORDER BYで昇順にソート）
		$showdata = "SELECT * FROM answer ORDER BY res_num";
		//実行・結果取得
		$result = $pdo -> query($showdata);
		
		//ループを用いてデータを転記
		$i = 0;
		foreach( $result as $row ){
			$data_2Darray[$i][0] = $row['thread_num'];
			$data_2Darray[$i][1] = $row['res_num'];
			$data_2Darray[$i][2] = $row['orner_id'];
			$data_2Darray[$i][3] = $row['a_content'];
			$data_2Darray[$i][4] = $row['a_time'];
			$data_2Darray[$i][5] = $row['img_ext'];
			$data_2Darray[$i][6] = $row['img_img'];
			$data_2Darray[$i][7] = $row['a_good'];
			$data_2Darray[$i][8] = $row['a_bad'];
			$i++ ;
		}
		$datacount = $i;
		
		//順番にコメントを表示
		for( $i=$datacount-1; $i>=0; $i-- ){
			if( $data_2Darray[$i][0]==$thread_num ){
				//コメントをした人のIDをニックネームに変換
				//テーブルからデータを取得（ORDER BYで昇順にソート）
				$showid = "SELECT * FROM user_table WHERE id='{$data_2Darray[$i][2]}'";
				//実行・結果取得
				$result = $pdo -> query($showid);
				//ループしてユーザー情報を照会
				foreach( $result as $row ){	$orner_name = $row['name'];}
				
				echo "<font color='#FCFCFC'> ".$orner_name."</font><font color='#A4C6FF'>@".$data_2Darray[$i][2]."</font><br>";
				echo $data_2Darray[$i][3]."<br>";
				//mission_3_image.phpにres_numをGETメソッドで送り、画像バイナリデータをエンコード
				if( !empty($data_2Darray[$i][6]) ){
					echo "<img src='mission_3_image.php?res_num={$data_2Darray[$i][1]}' style='max-width:520px'><br>";
				}
				echo"<font size='0.7'>投稿日時：".$data_2Darray[$i][4]."</font>";
				$editnum = $data_2Darray[$i][1];
				$goodnum = $data_2Darray[$i][7];
				$badnum = $data_2Darray[$i][8];
				echo "
					<!-goodボタン->
					<form method='post' action='mission_3-6~10_ver1.php' style='display:inline'>
					<input type='hidden' name='type' value='good'>
					<input type='hidden' name='res_num' value='$editnum'>
					<input type='submit' value='👍$goodnum' style='font-size:8px; background-color:#FFD5EC'></form>
					
					<!-badボタン->
					<form method='post' action='mission_3-6~10_ver1.php' style='display:inline'>
					<input type='hidden' name='type' value='bad'>
					<input type='hidden' name='res_num' value='$editnum'>
					<input type='submit' value='👎$badnum' style='font-size:8px; background-color:#D7EEFF'></form>
					
					<!-削除ボタン->
					<form style='display:inline'><input type='button' value='削除' style='font-size:8px' onclick='showdelete($editnum)'></form>
					
					<!-編集ボタン->
					<form style='display:inline'><input type='button' value='編集' style='font-size:8px' onclick='showedit($editnum)'></form>
					
					<!-削除フォーム->
					<form method='post' action='mission_3-6~10_ver1.php' id='delete$editnum' style='display:none'>
					<input type='hidden' name='type' value='delete'>
					<input type='hidden' name='delete' value='$editnum'>
					<p><font size=0.7 color=red>コメントを削除します。　<font color=black>パス</font></font>
					<input type='password' name='pass' size='15' style='font-size:8px'> <input type='submit' value='送信' style='font-size:8px'></p>
					</form>
					
					<!-編集フォーム->
					<form method='post' action='mission_3-6~10_ver1.php' id='edit$editnum' style='display:none'>
					<input type='hidden' name='type' value='edit'>
					<input type='hidden' name='edit_num' value='$editnum'>
					<p><font size=0.7 color=red>コメントを編集します。　<font color=black>パス<input type='password' name='pass' size='15' style='font-size:8px'><br>
					　内容</font></font><input type='text' name='edit_comment' size='46' style='font-size:8px'> <input type='submit' value='送信' style='font-size:8px'></p>
					</form>
				";
				echo "<hr align='left' width='520px' height='2' color='#fff'>";
			}
		}
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
</div>
</div>
<div id="footer"></div><br>
</div>

</body>
</html>
