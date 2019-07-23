<!DOCTYPE html>
<html>
    <head>
    <meta charset="utf-8">
    <title>掲示板</title>
    </head>
    <body>

        <?php 
// データベースへの接続  
        $dsn='データベース名';
        $user='ユーザー名';
        $password='パスワード';
        $pdo=new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

// テーブルの作成
        $sql='CREATE TABLE IF NOT EXISTS mission_5(
            id INT AUTO_INCREMENT PRIMARY KEY,
            name char(32),
            comment TEXT,
            pass char(32),
            date datetime
        )';
        $stmt = $pdo->query($sql);
        ?>

        <h1>掲示板</h1> 
        
        <hr>

        <h2>新規投稿</h2>
        <form action="/mission_5-1.php" method="POST">
        名前 <input type="text" name="name"><br>
        コメント <input type="text" name="comment"><br>
        パスワード <input type="text" name="pass"><br>
        <input type="submit" name="button" value="送信">
        
        <?php
            if (isset($_POST["button"])) {
                $name=$_POST["name"];
                $comment=$_POST["comment"];
                $pass=$_POST["pass"];
                if(empty($name)) {
                    $new_post_message[]="名前を入力してください。<br>";
                }
                if (empty($comment)) {
                    $new_post_message[]="コメントを入力してください。<br>";
                }
                if(!empty($name)&&!empty($comment)) {
                    $date=date("Y/m/d H:i:s");
                    $sql = $pdo -> prepare("INSERT INTO mission_5 (name, comment, pass, date) VALUES (:name, :comment, :pass, :date)");
                    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                    $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
                    $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                    $sql -> execute();
                    $new_post_message[]="コメントを受け付けました。";
                }
            }
            ?>
        </form>

<!-- メッセージ -->  
        <h3><?php if(isset($new_post_message)){foreach($new_post_message as $new_post_message){echo $new_post_message;}} ?></h3>

        <h2>編集</h2>
        <form action="mission_5-1.php" method="POST">
            ID <input type="text" name="edited_id"><br>
            名前 <input type="text" name="edited_name"><br>
            コメント <input type="text" name="edited_comment"><br>
            パスワード <input type="text" name="pass"><br>
            <input type="submit" name="edit_button" value="編集">
            
            <?php
                if (isset($_POST["edit_button"])) {
                    $edited_id=$_POST["edited_id"];
                    $edited_name=$_POST["edited_name"];
                    $edited_comment=$_POST["edited_comment"];
                    $pass=$_POST["pass"];
                    if(empty($edited_id)){
                        $edit_message[]="投稿番号を入力してください。<br>";
                    }
                    if (empty($edited_name)) {
                        $edit_message[]="名前を入力してください。<br>";
                    }
                    if (empty($edited_comment)) {
                        $edit_message[]="コメントを入力してください。<br>";
                    }
                    if (empty($pass)) {
                        $edit_message[]="パスワードを入力してください。<br>";
                    }
                    if(!empty($edited_id) && !empty($edited_name) && !empty($edited_comment) && !empty($pass)){
                        $sql = 'SELECT * FROM mission_5';
                        $stmt = $pdo->query($sql);
                        $results = $stmt->fetchAll();
                        foreach ($results as $row){
                            if ($row['id']==$edited_id && $row['pass']==$pass) {
                                $date=date("Y/m/d H:i:s");
                                $sql = 'update mission_5 set name=:name,comment=:comment,pass=:pass,date=:date where id=:id';
                                $stmt = $pdo->prepare($sql);
                                $stmt->bindParam(':name', $edited_name, PDO::PARAM_STR);
                                $stmt->bindParam(':comment', $edited_comment, PDO::PARAM_STR);
                                $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
                                $stmt->bindParam(':date', $date, PDO::PARAM_STR);
                                $stmt->bindParam(':id', $edited_id, PDO::PARAM_INT);
                                $stmt->execute();
                                $edit_message[]= "編集を受け付けました。";
                            }
                            elseif($row['id']==$edited_id && $row['pass']!=$pass) {
                                $edit_message[]="パスワードが違います。";
                            }
                        }
                    }
                }
                ?>

<!-- メッセージ -->  
        <h3><?php if(isset($edit_message)){foreach($edit_message as $edit_message){echo $edit_message;}} ?></h3>

        </form>
        <h2>削除</h2>
        <form action="mission_5-1.php" method="POST">
            ID <input type="text" name="deleted_id"><br>
            パスワード <input type="text" name="pass"><br>
            <input type="submit" name="delete_button" value="削除">
        </form>

        <?php 
             if (isset($_POST["delete_button"])){
                 
                $deleted_id=$_POST["deleted_id"];
                $pass=$_POST["pass"];

                if (empty($deleted_id)) {
                     $delete_message[]="投稿番号を入力してください。<br>"; 
                }
                if(empty($pass)){
                    $delete_message[]="パスワードを入力してください。<br>";
                }
                if(!empty($deleted_id) && !empty($pass)){
                    $sql = 'SELECT * FROM mission_5';
                    $stmt = $pdo->query($sql);
                    $results = $stmt->fetchAll();
                    foreach ($results as $row){
                        if ($row['pass']==$pass && $row['id']==$deleted_id) {
                            $id = $deleted_id;
                            $sql = 'delete from mission_5 where id=:id';
                            $stmt = $pdo->prepare($sql);
                            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                            $stmt->execute();
                            $delete_message[]="削除しました。";
                        }
                        elseif($row['pass']!==$pass && $row['id']==$deleted_id) {
                            $delete_message[]="パスワードが違います。";
                        }
                    }
                }
             }
        ?>

<!-- メッセージ -->  
        <h3><?php if(isset($delete_message)){foreach($delete_message as $delete_message){echo $delete_message;}} ?></h3>

        <hr>
        <h2>コメント一覧</h2>

        <?php
            $sql = 'SELECT * FROM mission_5';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            foreach ($results as $row){
//$rowの中にはテーブルのカラム名が入る
                echo $row['id'].',';
                echo $row['name'].',';
                echo $row['comment'].',';
                echo "最終更新:".$row['date']."<br>";
                echo "<hr>";
            }
        ?>

    </body>
</html>