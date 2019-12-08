<?php
/*
・データベース名：************
・MySQLホスト名：********
・ユーザ名：**********
・パスワード：**********
*/
//MySQLに接続する
$dsn = 'データベース名';
$user = 'ユーザネーム名';
$mypwd = 'パスワード';
$pdo = new PDO($dsn, $user, $mypwd, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
//接続完了
?>
<?php
//諸々の変数を定義する
$date = date('Y-m-d H:i:s');
$get_num = "";
$get_name = "";
$get_comment = "";
$get_pwd = "";
?>
<?php
//削除処理(入力したデータをdeleteで削除)
if(isset($_POST["delete"]) && $_POST["delete"] !== ""){ 
    //$pwd_deの定義
    $pwd_de = $_POST['pwd_de'];
    //削除番号獲得
    $delete = $_POST['delete'];
    //データ内の投稿番号を取得
    $sql = 'SELECT * FROM akiodb';
    $stmt = $pdo->query($sql);
    $lines = $stmt->fetchAll();
    foreach($lines as $value){
        if($delete == $value['id']){ 
            //データ内のパスワードを取得
	    $get_pwd = $value['pwd'];
	    if($get_pwd == $pwd_de){
	        //削除する
	        $id = $value['id'];
                $sql = 'delete from akiodb where id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                echo "削除されました。".'<br/>';
                $get_pwd = "";
	    }else{
	        echo "パスワードが違います。".'<br/>';
	        $get_pwd = "";
	    }
	}
    }
}
?>
<?php
//編集フォーム処理
if(isset($_POST['choose']) && $_POST['choose'] !== ""){ 
    //$pwd_upの定義
    $pwd_up = $_POST['pwd_up'];
    //編集番号獲得
    $choose = $_POST['choose'];
    //データ内の投稿番号取得
    $sql = 'SELECT * FROM akiodb';
    $stmt = $pdo->query($sql);
    $lines = $stmt->fetchAll();
    foreach($lines as $value){
        if($choose == $value['id']){ 
            //データ内のパスワード取得
	    $get_pwd = $value['pwd'];
	    if($get_pwd == $pwd_up){
  	        //その投稿の「編集認識用番号」, 「名前」, 「コメント」を取得
	        $get_num = $value['id'];
	        $get_name = $value['name'];
	        $get_comment = $value['comment'];
	        $get_date = "";
	    }else{
	        echo "パスワードが違います。".'<br/>';
	        $get_pwd = "";
	    }
	}
    }
}
//編集送信処理(入力したデータをupdateで編集)※編集認識用番号の有無で条件分岐
if(isset($_POST['update']) && $_POST['update'] !== ""){
    echo "編集完了しました。".'<br/>';
    //空でないときは、ファイルの中身を取り出し
    $update_name = $_POST['name'];
    $update_comment = $_POST['comment'];
    $update_num = $_POST['update'];
    $update_pwd = $_POST['pwd_new'];
    $sql = 'SELECT * FROM akiodb';
    $stmt = $pdo->query($sql);
    $lines = $stmt->fetchAll();
    foreach($lines as $value){
        if($update_num == $value['id']){ 
 	    //一致した時のみ、値を入れ替える
 	    $id = $update_num;
 	    $name = $update_name;
 	    $comment = $update_comment;
 	    //$value[3] = $formtime;
 	    $pwd = $update_pwd;
 	    $sql = 'update akiodb set name=:name,comment=:comment,pwd=:pwd_new where id=:id';
	    $stmt = $pdo->prepare($sql);
	    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
	    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);			
	    $stmt->bindParam(':pwd_new', $pwd, PDO::PARAM_STR);
	    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
	    $stmt->execute();
 	}
    }
}else{
//編集でなければ新規投稿処理(作成したテーブルにinsertを用いてデータを入力)
    if(isset($_POST['name'], $_POST['comment']) && $_POST['name'] !=="" && $_POST['comment'] !==""){
        $sql = $pdo -> prepare("INSERT INTO akiodb (name, comment, pwd) VALUES (:name, :comment, :pwd_new)");
	$sql -> bindParam(':name', $name, PDO::PARAM_STR);
	$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
	$sql -> bindParam(':pwd_new', $pwd, PDO::PARAM_STR);
	$name = $_POST['name'];
	$comment = $_POST['comment'];
	$pwd = $_POST['pwd_new'];
	$sql -> execute();
	echo "新規投稿を受け付けました。".'<br/>';
    }
}
?>
<!DOCTYPE html>
<head>
<meta charset="utf-8">
</head>
<body>
  <form action="" method="post">
  名前:<input type="text" name="name" value=<?php echo $get_name;?> ><br>
  コメント:<input type="text" name="comment" value=<?php echo $get_comment;?> ><br>
  パスワード:<input type="text" name="pwd_new" value=<?php echo $get_pwd;?> >
  <input type="hidden" name="update" value=<?php echo $get_num;?> >
  <input type="submit" value="送信"><br>
  削除番号:<input type="text" name="delete" value=""><br>
  パスワード:<input type="text" name="pwd_de">
  <input type="submit" value="削除"><br>
  編集対象番号:<input type="text" name="choose" value=""><br>
  パスワード:<input type="text" name="pwd_up">
  <input type="submit" value="編集"><br>
  </form>
</body>
</html>

<?php
//結果をブラウザ上に表示する
$sql = 'SELECT * FROM akiodb';
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll();
foreach($results as $row){
    echo $row['id'].',';
    echo $row['name'].',';
    echo $row['comment'];
    echo "<hr>";
}
?>

