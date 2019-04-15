<?php
date_default_timezone_set("Africa/Nairobi");
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
  require_once 'dbconfig.php';
  require_once 'dbconnect.php';
  require_once("dbcontroller.php");
$db_handle = new DBController();

if (!isset($_SESSION['userSession'])) {
    header("Location: index.php");
}

$query = $DBcon->query("SELECT * FROM tbl_users WHERE user_id=".$_SESSION['userSession']);
$userRow=$query->fetch_array();
$DBcon->close();




?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
   

    <title>Welcome - <?php echo $userRow['email']; ?></title>

    <!-- Bootstrap core CSS -->
<script src="vendor/jquery/jquery.min.js"></script>
<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
<!-- Latest compiled and minified JavaScript -->
<script src="bootstrap/js/bootstrap.min.js"></script>
    <link href="css/image-effects.css" rel="stylesheet">
    <link href="css/custom-styles.css" rel="stylesheet">
    <link href="css/font-awesome.css" rel="stylesheet">
    <link href="css/font-awesome-ie7.css" rel="stylesheet">
    </head>

  <body>
    
      <div class="container">
     
        <div class="row">
          <div class="site-header spacing-t">
            <div class="col-md-3 ">
              <div class="site-name spacing-b">
                <h1>TALKIFY</h1>
                <h6>Convey sms,call,watsapp</h6>
              </div>
            </div>
          <div class="col-md-9">
            <nav class="navbar pull-right " role="navigation">
            <!-- Brand and toggle get grouped for better mobile display -->
                    <div class="navbar-header">
                      
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse"><h3 class="hide">Menu</h3>
            <span class="fw-icon-th-list "></span>
            
          </button>
         
        </div>
            
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                  <li class="active"><a href="#">Home</a></li>
                  <li class="dropdown ">
              <a href="#" class="dropdown-toggle active" data-toggle="dropdown">About <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="#">Events Hub</a></li>
                <li><a href="#">Events</a></li>
                <li><a href="#">Rooms</a></li>
                <li><a href="#">Us</a></li>
              </ul>
            </li>
          <li><a class="navbar-brand" href="users.php">Users</a></li>
          <li><a class="navbar-brand" href="addnew.php">Add new </a></li>
                  <li class="dropdown ">
              <a href="#" class="dropdown-toggle active" data-toggle="dropdown"><?php echo "Hello Admin ".$userRow['username']; ?><b class="caret"></b></a>
               <ul class="dropdown-menu">
            <li><a href="logout.php?logout"><span class="glyphicon glyphicon-log-out"></span>&nbsp; Logout</a></li>
            </ul>
            </li>
                  
                </ul>
            </div><!-- /.navbar-collapse -->
        </nav>
        
          </div>
          </div>
        </div>
     
    </div>
    <div class="container">
<div class="subscribe">
<div class="col-md-4">
  <div class="panel panel-default">
    
      <div class="panel-heading">
        <center><strong>COMPOSE</strong></center>
        <?php
if(isset($_POST['btn-message'])){
  $header=$_POST['header'];
  $fullname=$header;
  $postMessage=$_POST['message'];
  $postDateFormat=date("y:m:d",time()); 
  $mergeDate="20";
  $postTime=date("h:i A.",time());
  $postDate=$mergeDate."".$postDateFormat;
  $stmt = $DB_con->prepare('INSERT INTO tbl_msg(sender,header,message,postTime) VALUES(:usender,:uhead, :umessage, :utime)');
      $stmt->bindParam(':usender',$fullname);
      $stmt->bindParam(':uhead',$header);
      $stmt->bindParam(':umessage',$postMessage);
      $stmt->bindParam(':utime',$postDate);
      
      if($stmt->execute())
      {  
      $LAST_ID = $DB_con->lastInsertId();
      $messageFrom=$fullname;
    $status="0";
    $stmt_select = $DB_con->prepare('SELECT * FROM tbl_contacts WHERE status=:usts');
    $stmt_select->execute(array(':usts'=>$status));
    if($stmt_select->rowCount() > 0)
    {
    while($stmt_row=$stmt_select->fetch(PDO::FETCH_ASSOC))
      {
      $number=$stmt_row['contact'];
      $messageTo=$stmt_row['name'];
      $status2="Success";

  $stmt_send = $DB_con->prepare('INSERT INTO tbl_messages(messageFrom,messageTo,header,message,messageID,status,postTime) VALUES(:ufrom, :uto,:uhead, :umm, :uid, :ust, :utm)');
      $stmt_send->bindParam(':ufrom',$messageFrom);
      $stmt_send->bindParam(':uto',$messageTo);
      $stmt_send->bindParam(':uhead',$header);
      $stmt_send->bindParam(':umm',$postMessage);
      $stmt_send->bindParam(':uid',$LAST_ID);
      $stmt_send->bindParam(':ust',$status2);
      $stmt_send->bindParam(':utm',$postTime);
      if($stmt_send->execute())
      {
        $status3="1";
        $id=$stmt_row['id'];
      $stmt_update = $DB_con->prepare('UPDATE tbl_contacts SET status=:ustat  WHERE id=:uid');
      $stmt_update->bindParam(':ustat',$status3);
      $stmt_update->bindParam(':uid',$id);
      if($stmt_update->execute()){

require_once __DIR__ . '/vendor/autoload.php';
$basic  = new \Nexmo\Client\Credentials\Basic('','');
$client = new \Nexmo\Client($basic);
try {
  $messagee=$postMessage;
    $message = $client->message()->send([
    'to' => $number,
    'from' => $messageFrom,
    'text' =>  $messagee
    ]);
    $response = $message->getResponseData();
    if($response['messages'][0]['status'] == 0) {
       $successMSG3 = "The message was sent successfully\n";
    } else {
        $errMSG3 = "The message failed with status: " . $response['messages'][0]['status'] . "\n";
    }
} catch (Exception $e) {
    $errMSG3 ="The message was not sent. Error: " . $e->getMessage() . "\n";
}
      }
      }
      }
    }
      }
      else
      {
        $errMSG = "Error while creating Message queue....";
      }
}
    if(isset($errMSG1)){
            ?>
            <div class="alert alert-danger">
            <button class='close' data-dismiss='alert'>&times;</button>
                <span class="glyphicon glyphicon-info-sign"></span> <strong><?php echo $errMSG; ?></strong>
            </div>
            <?php
    }
    else if(isset($successMSG1)){
        ?>
        <div class="alert alert-success">
        <button class='close' data-dismiss='alert'>&times;</button>
              <strong><span class="glyphicon glyphicon-info-sign"></span> <?php echo $successMSG; ?></strong>
        </div>
        <?php
    }
    ?>   
      </div>
      <div class="panel-body">
        <form class="form-signin" method="post" id="register-form">
         <div class="form-group input-group">
        <span class="input-group-addon">From</span>
        <input type="text" required="true" class="form-control" placeholder="Enter Header" name="header" />
       
        </div>
        <div class="form-group input-group">
        <span class="input-group-addon">Message</span>
        <input type="text" required="true" class="form-control" placeholder="Enter Message" name="message" />
       
        </div>
      <hr />
        
        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-round" name="btn-message" id="btn-login">
        <span class="glyphicon glyphicon-log-in"></span> &nbsp;Send
      </button>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            
        </div>  
        
        
      
      </form>
 </div>
      <div class="panel-footer">
      </div>
    </div>
  </div>
  <div class="col-md-5">
  <div class="panel panel-default">
    
      <div class="panel-heading">
        <center><strong>SAVE/VIEW</strong></center>
        
      </div>
         <?php
if(isset($_POST['btn-contact'])){
  $name=$_POST['name'];
  $contact=$_POST['contact'];
  $postDateFormat=date("y:m:d",time()); 
  $mergeDate="20";
  $postTime=date("h:i A.",time());
  $postDate=$mergeDate."".$postDateFormat;
  $stmt = $DB_con->prepare('INSERT INTO tbl_contacts(name,contact,postDate) VALUES(:uname, :ucont, :udate)');
      $stmt->bindParam(':uname',$name);
      $stmt->bindParam(':ucont',$contact);
      $stmt->bindParam(':udate',$postDate);
      
      if($stmt->execute())
      {  
       $successMSG1 = "Contact saved....";
      }
      else
      {
        $errMSG1 = "Error while saving contact....";
      }
}
    if(isset($errMSG1)){
            ?>
            <div class="alert alert-danger">
            <button class='close' data-dismiss='alert'>&times;</button>
                <span class="glyphicon glyphicon-info-sign"></span> <strong><?php echo $errMSG1; ?></strong>
            </div>
            <?php
    }
    else if(isset($successMSG1)){
        ?>
        <div class="alert alert-success">
        <button class='close' data-dismiss='alert'>&times;</button>
              <strong><span class="glyphicon glyphicon-info-sign"></span> <?php echo $successMSG1; ?></strong>
        </div>
        <?php
    }
    ?> 
      <div class="panel-body">
      <div class="col-md-8">
         <form class="form-signin" method="post" id="register-form">
        <div class="form-group input-group">
        <span class="input-group-addon">Name</span>
        <input type="text" required="true" class="form-control" placeholder="Enter Name" name="name" />
        </div>
        <div class="form-group input-group">
        <span class="input-group-addon">Contact</span>
        <input type="text" required="true" class="form-control" placeholder="Enter Contact" name="contact" />
        </div>
      <hr />
        
        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-round" name="btn-contact" id="btn-login">
        <span class="glyphicon glyphicon-log-in"></span> &nbsp;Save
      </button>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            
        </div>  
      </form> 
      </div>
      <div class="col-md-4">
      <div class="list-group-item" >
       <!--  <?php
  
  $stmt = $DB_con->prepare('SELECT name FROM tbl_contacts ORDER BY id DESC');
  $stmt->execute();
  
  if($stmt->rowCount() > 0)
  {
    while($row=$stmt->fetch(PDO::FETCH_ASSOC))
    {
      extract($row);
      ?>
     <dd style="font-size: 6"><?php echo $row['name']; ?></dd>
      <?php
    }
  }
  ?> -->
      </div>
      </div>
      </div>
      <div class="panel-footer">
      </div>
    </div>
  </div>
   <div class="col-md-3">
  <div class="panel panel-default">
    
      <div class="panel-heading">
        <center><strong>MESSAGES</strong></center>
       <?php
       if(isset($errMSG3)){
            ?>
            <div class="alert alert-danger">
            <button class='close' data-dismiss='alert'>&times;</button>
                <span class="glyphicon glyphicon-info-sign"></span> <strong><?php echo $errMSG3; ?></strong>
            </div>
            <?php
    }
    else if(isset($successMSG3)){
        ?>
        <div class="alert alert-success">
        <button class='close' data-dismiss='alert'>&times;</button>
              <strong><span class="glyphicon glyphicon-info-sign"></span> <?php echo $successMSG3; ?></strong>
        </div>
        <?php
    }
    ?>  
      </div>

      
      <div class="panel-body">
    
 </div>
      <div class="panel-footer">
      </div>
    </div>
  </div>
</div>
    </div>
      <br>
    
<div class="modal fade" id="productView" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog">
    <div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="H4" style="color: orange;"><center>Product Details</center></h4>
    </div>
    <div class="productViewDisplay">
   
  </div>
  
            
        </div>
    </div>
</div>
    <script type="text/javascript">
      
    $('#productView').on('show.bs.modal', function (event) {
          var button = $(event.relatedTarget) // Button that triggered the modal17
          var recipient = button.data('whatever4') // Extract info from data-* attributes
          
          var modal4 = $(this);
          var dataString4 = 'id=' + recipient;
          var dataString2 = 'idd=' + "2";
       
            $.ajax({
                type: "GET",
                url: "productView.php",
                data: dataString4,
                cache: false,
                beforeSend: function (data) {
                    console.log('Retrieving Data....');
                },
                success: function (data) {
                    console.log(data);
                    modal4.find('.productViewDisplay').html(data);
                },
                error: function(err) {
                    console.log(err);
                }
            });

    })
</script>
      
   <script src="bootstrap/js/bootstrap.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="js/html5shiv.js"></script>
      <script src="js/respond.min.js"></script>
  <script src="js/jquery-1.9.1.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/modernizr-2.6.2-respond-1.1.0.min.js"></script>

       
  </body>
</html>
