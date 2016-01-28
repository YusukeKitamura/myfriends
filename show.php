<?php 
  //ステップ１　DB接続
  $dsn      = 'mysql:dbname=myfriends;host=localhost';
  //接続するためのユーザー情報
  $user     = 'root';
  $password = '';

  //DB接続オブジェクトを作成
  $dbh      = new PDO($dsn, $user, $password);
  $dbh->query('SET NAMES utf8');

  $area_id = $_GET['area_id'];

  //都道府県名を表示するための処理
  $sql = 'SELECT `area_name` FROM `areas` WHERE `area_id`='.$area_id;
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $area = $stmt->fetch(PDO::FETCH_ASSOC);

  if (isset($_GET['action']) && !empty($_GET['action']) ) {
    if ($_GET['action'] == 'delete') {
      $sql  = 'DELETE FROM `friends` WHERE `friend_id`='.$_GET['friend_id'];
      $stmt = $dbh->prepare($sql);
      $stmt->execute();
      header('location: index.php');
    }
  }

  //友達リストを表示するための処理
  $sql = 'SELECT * FROM `friends` WHERE `area_id`='.$area_id;
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $friends = array();

  $male = 0;
  $female = 0;

  while(1) {
    $rec = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rec == false) {
      break;
    }
    if ($rec['gender']==1) {
      $male++;
    } else {
      $female++;
    }
    $friends[] = $rec;
  }

  //男女の平均年齢算出
  $sql = 'SELECT `areas`.`area_id`, `friends`.`gender`, ROUND(AVG(`friends`.`age`), 2) AS friend_avg FROM `areas` LEFT OUTER JOIN `friends` ON 
          `areas`.`area_id`=`friends`.`area_id` WHERE `areas`.`area_id`= '.$area_id.' GROUP BY `friends`.`gender`';
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  
  $male_avg = '';
  $female_avg = '';
  if ($male > 0) {
    $male_rec = $stmt->fetch(PDO::FETCH_ASSOC);
    $male_avg = $male_rec['friend_avg'];
  }
  if ($female > 0) {
    $female_rec = $stmt->fetch(PDO::FETCH_ASSOC);
    $female_avg = $female_rec['friend_avg'];
  }

  $dbh = null;

 ?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>myFriends</title>

    <!-- Bootstrap -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="assets/css/form.css" rel="stylesheet">
    <link href="assets/css/timeline.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- Javascript -->
    <script type="text/javascript">
      function destroy(friend_id, area_id) {
        // alert('こんにちは');
        if (confirm('削除しますか？')) {
          //OKボタンを押したとき
          location.href = 'show.php?action=delete&friend_id='+friend_id+'&area_id='+area_id;
          return true;
        } else {
          //キャンセルボタンを押したとき
          return false;
        }
      }
    </script>

  </head>
  <body>
  <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
          <!-- Brand and toggle get grouped for better mobile display -->
          <div class="navbar-header page-scroll">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="index.php"><span class="strong-title"><i class="fa fa-facebook-square"></i> My friends</span></a>
          </div>
          <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav navbar-right">
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
  </nav>

  <div class="container">
    <div class="row">
      <div class="col-md-4 content-margin-top">
      <legend><?php echo $area['area_name']; ?>の友達</legend>
      <div class="well">男性：<?php echo $male; ?>名　女性：<?php echo $female; ?>名</div>
      <?php if($male_avg) { ?>
      <div class="well">男性の平均年齢：<?php echo $male_avg; ?>歳</div>
      <?php }
       if($female_avg) { ?>
      <div class="well">女性の平均年齢：<?php echo $female_avg; ?>歳</div>
      <?php } ?>
        <table class="table table-striped table-hover table-condensed">
          <thead>
            <tr>
              <th><div class="text-center">名前</div></th>
              <th><div class="text-center"></div></th>
            </tr>
          </thead>
          <tbody>
            <!-- id, 県名を表示 -->
          <?php
            foreach ($friends as $friend) {
            ?>
            <tr>
              <td><div class="text-center"><?php echo $friend['friend_name'];?></div></td>
              <td>
                <div class="text-center">
                  <a href="edit.php?friend_id=<?php echo $friend['friend_id'];?>"><i class="fa fa-pencil"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;
                  <!-- <a href="javascript:void(0);" onclick="destroy();"><i class="fa fa-trash"></i></a> -->
                  <a href="#" onclick="destroy(<?php echo $friend['friend_id'].','.$_GET['area_id'] ?>);"><i class="fa fa-trash"></i></a>
                </div>
              </td>
            </tr>

          <?php
           }
          ?>

          </tbody>
        </table>

        <input type="button" class="btn btn-default" value="新規作成" onClick="location.href='new.php'">
      </div>
    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
  </body>
</html>
