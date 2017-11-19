<html>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>入力フォーム／テスト</title>
<body>
<h2><i>入力フォーム／テスト</i></h2>

<table border=1>
	<tr><td rowspan=2>
		<!-入力フォーム
		   「comment」という名前のtextを「mission_2-6.php」にPOSTメソッドで送信->
		<form method="post" action="mission_2-6.php"><input type="hidden" name="type" value="comment">
		<p>　　お名前：<input type="text" name="name">　<br>
		　コメント：<input type="text" name="comment">　<br>
		　パス設定：<input type="password" name="pass" size="13"><input type="submit" value="送信">　</p>
		</form>
	</td>
	<td>
		<!-削除フォーム
		   「delete」という名前の値を「mission_2-6.php」にPOSTメソッドで送信->
		<form method="post" action="mission_2-6.php"><input type="hidden" name="type" value="delete">
		<p>　コメント｜番号<input type="text" name="delete" size="2">　　<input type="submit" value="削除">　<br>
		　　削除　｜パス<input type="password" name="pass" size="15">　</p>
		</form>
	</td></tr>
	<tr><td>
		<!-編集フォーム
		   値「edit_number」とtext「edit_comment」を「mission_2-6.php」にPOSTメソッドで送信->
		<form method="post" action="mission_2-6.php"><input type="hidden" name="type" value="edit">
		<p>　コメント｜番号<input type="text" name="edit_number" size="2">　　<input type="submit" value="編集">　<br>
		　　編集　｜内容<input type="text" name="edit_comment" size="15">　<br>
		　　　　　｜パス<input type="password" name="pass" size="15">　</p>
		</form>
	</td></tr>
</table>


<?php

//新規入力

//どのフォームからの送信かの判定。
if( $_POST["type"] == "comment"){
	//フォームが空かの判定。
	if( !(empty($_POST["pass"]) == TRUE )){
		if( !(empty($_POST["name"]) == TRUE )){
			if( !(empty($_POST["comment"]) == TRUE )){
				//「kadai2-6.txt」ファイルを開く。
				//モードを'a'に設定すると、ポインタがファイル最後尾に設定されるので、上書きでなく追記が可能。
				$filename = 'kadai2-6.txt';
				$fp = fopen($filename, 'a');
				
				//時間に関するデータを取得、表示。
				$timestamp = time();
				
				//開いたファイルに入力フォームで送信されたテキストを書き込む。
				//."\r\n"は改行処理。
				fwrite($fp, $_POST["name"]."<>".$_POST["comment"]."<>".date( "Y/m/d H:i:s", $timestamp )."<>".$_POST["pass"]."<>\n");
				
				//ファイルを閉じる。
				fclose($fp);
			}
			else{ echo "コメントが未入力です。";}
		}
		else{ echo "お名前が未入力です。";}
	}
	else{ echo "パスが未入力です。";}
}


//コメント削除

//どのフォームからの送信かの判定。
if( $_POST["type"] == "delete"){
	//フォームが空かの判定。
	if( !(empty($_POST["pass"]) == TRUE )){
		if( !(empty($_POST["delete"]) == TRUE )){
			//txtファイルを配列として読み込む。
			$text_array = file('kadai2-6.txt', FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
			
			for($j = 0; $j < count($text_array); $j++){
				$data_array = explode("<>", $text_array[$j]);
				$data_2Darray[$j][0] = $data_array[0];
				$data_2Darray[$j][1] = $data_array[1];
				$data_2Darray[$j][2] = $data_array[2];
				$data_2Darray[$j][3] = $data_array[3];
			}
			
			//「kadai2-6.txt」ファイルを開く。
			$filename = 'kadai2-6.txt';
			$fp = fopen($filename, 'w');
			
			//ループを用いて配列要素を書き込み。
			for($i = 0; $i < count($text_array); $i++){
				if( $i+1 == $_POST["delete"] ){
					if( $_POST["pass"] == $data_2Darray[$i][3] ){}
					else{
						echo "パスが間違っています。";
						fwrite($fp, $data_2Darray[$i][0]."<>".$data_2Darray[$i][1]."<>".$data_2Darray[$i][2]."<>".$data_2Darray[$i][3]."<>\n");
					}
				}
				else{
					fwrite($fp, $data_2Darray[$i][0]."<>".$data_2Darray[$i][1]."<>".$data_2Darray[$i][2]."<>".$data_2Darray[$i][3]."<>\n");
				}
			}
			
			//ファイルを閉じる。
			fclose($fp);
		}
		else{ echo "番号が未入力です。";}
	}
	else{ echo "パスが未入力です。";}
}


//コメント編集

//どのフォームからの送信かの判定。
if( $_POST["type"] == "edit"){
	//フォームが空かの判定。
	if( !(empty($_POST["pass"]) == TRUE )){
		if( !(empty($_POST["edit_number"]) == TRUE )){
			if( !(empty($_POST["edit_comment"]) == TRUE )){
				//txtファイルを配列として読み込む。
				$text_array = file('kadai2-6.txt', FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
				
				for($j = 0; $j < count($text_array); $j++){
					$data_array = explode("<>", $text_array[$j]);
					$data_2Darray[$j][0] = $data_array[0];
					$data_2Darray[$j][1] = $data_array[1];
					$data_2Darray[$j][2] = $data_array[2];
					$data_2Darray[$j][3] = $data_array[3];
				}
				
				//「kadai2-6.txt」ファイルを開く。
				$filename = 'kadai2-6.txt';
				$fp = fopen($filename, 'w');
				
				//時間に関するデータを取得、表示。
				$timestamp = time();
				
				//ループを用いて配列要素を書き込み。
				for($i = 0; $i < count($text_array); $i++){
					if( $i+1 == $_POST["edit_number"] ){
						if( $_POST["pass"] == $data_2Darray[$i][3] ){
							fwrite($fp, $data_2Darray[$i][0]."<>".$_POST["edit_comment"]."（編集済）<>".date("Y/m/d H:i:s", $timestamp)."<>".$data_2Darray[$i][3]."<>\n");
						}
						else{
							echo "パスが間違っています。";
							fwrite($fp, $data_2Darray[$i][0]."<>".$data_2Darray[$i][1]."<>".$data_2Darray[$i][2]."<>".$data_2Darray[$i][3]."<>\n");
						}
					}
					else{ fwrite($fp, $data_2Darray[$i][0]."<>".$data_2Darray[$i][1]."<>".$data_2Darray[$i][2]."<>".$data_2Darray[$i][3]."<>\n");}
				}
				
				//ファイルを閉じる。
				fclose($fp);
			}
			else{ echo "編集内容が未入力です。";}
		}
		else{ echo "番号が未入力です。";}
	}
	else{ echo "パスが未入力です。";}
}


//コメント表示

	echo "<br /><br />*.｡:ﾟ+..｡*ﾟ+.｡*ﾟ+.*.｡:ﾟ+..｡*ﾟ+ コメントログ .｡*ﾟ+.*.｡:ﾟ+..｡*ﾟ+.｡*ﾟ+.*.｡:ﾟ+<br /><br />";
	
	//txtファイルを配列として読み込む。
	$comment_array = file('kadai2-6.txt', FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
	
	//ループを用いて配列要素を表示。
	for($i = 1; $i <= count($comment_array); $i++){
		$data_array = explode("<>", $comment_array[$i-1]);
		
		echo "$i  ";
		echo $data_array[0];
		echo "  ";
		echo $data_array[1];
		echo "  ";
		echo $data_array[2];
		echo "<br />";
	}
?>

</body>
</html>