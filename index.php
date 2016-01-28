<?php 
	//ステップ１　DB接続
	$dsn      = 'mysql:dbname=myfriends;host=localhost';
	//接続するためのユーザー情報
	$user     = 'root';
	$password = '';

	//DB接続オブジェクトを作成
	$dbh      = new PDO($dsn, $user, $password);
	$dbh->query('SET NAMES utf8');

	$sql  = 'SELECT `areas`.`area_id`, `areas`.`area_name`, COUNT(`friends`.`friend_id`) AS friend_cnt FROM `areas` 
           LEFT OUTER JOIN `friends` ON `areas`.`area_id`=`friends`.`area_id` WHERE 1 GROUP BY `areas`.`area_id`';
	$stmt = $dbh->prepare($sql);
	$stmt->execute();
	// 取得データ格納用Array
	$areas = array();

	while (1) {
		$rec = $stmt->fetch(PDO::FETCH_ASSOC);	//1レコード取り出し
		if ($rec==false) {
			break;
		}
		// データ格納
		$areas[] = $rec;
	}

  $data = '';
  $views = array();
  if(isset($_POST) && !empty($_POST)){
    $data = htmlspecialchars($_POST['data']);
    if ($data) {
      $sql  = 'SELECT `friends`.`friend_name` as friend_name, `areas`.`area_name` as area_name, `friends`.`gender` as gender, 
          `friends`.`age` as age FROM `friends` INNER JOIN `areas` ON 
          `friends`.`area_id`=`areas`.`area_id` WHERE `friend_name` LIKE "%'.$data.'%"';
      $stmt = $dbh->prepare($sql);
      $stmt->execute();
      while (1) {
        $rec = $stmt->fetch(PDO::FETCH_ASSOC);  //1レコード取り出し
        if ($rec==false) {
          break;
        }
        // データ格納
        $views[] = $rec;
      }
    }
  }

	$dbh = null;
 ?>

<!DOCTYPR HTML PUBLIC "-//W3C//DTD/ HTML 4. Transitional//EN">
<html>
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
              <a class="navbar-brand" href="index.php"><span class="strong-title"><i class="fa fa-facebook-square">
              </i> My friends</span></a>
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

      <form method="post" action="index.php">
        検索機能<br />
        <input name="data" type="text" style="width:100px"><br />
        <input type="submit" value="検索">
      </form>

      <?php
            foreach ($views as $view) { ?>
                  <div class="text-center">
                    <?php echo $view['friend_name'].' '; ?>
                    <?php echo $view['area_name'].' '; ?>
                    <?php if ($view['gender']==1) {
                            echo '男 ';
                          } else {
                            echo '女 ';
                          } ?>
                    <?php echo $view['age'].'歳<br />'; ?>
                  </div>
                </tr>
                <?php
            }
      ?>

      <legend>都道府県一覧</legend>
        <table class="table table-striped table-bordered table-hover table-condensed" border=1>
          <thead>
            <tr>
              <th><div class="text-center">id</div></th>
              <th><div class="text-center">県名</div></th>
              <th><div class="text-center">人数</div></th>
            </tr>
          </thead>

          <tbody>
    			<?php
            foreach ($areas as $area) { ?>
                <tr>
                  <td><div class="text-center"><?php echo $area['area_id']; ?></div></td>
                  <td><div class="text-center">
                    <a href="show.php?area_id=<?php echo $area['area_id']; ?>"><?php echo $area['area_name']; ?></a></div></td>
                  <td><div class="text-center"><?php echo $area['friend_cnt']; ?></div></td>
                </tr>
                <?php
            }
    			?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>

</body>
</html>