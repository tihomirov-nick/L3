<?php
    require_once 'DBConnection.php';
    session_start();
    
?>


<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>My Chatbox</title>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
<link rel = "stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css">
<script scr="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.2.1/css/fontawesome.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-4">
        <?php 
        $curUser=$_SESSION["curUser"];
        $user = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM `users` WHERE `Id` = '$curUser' "));
        $chatId=$_SESSION["chatId"];
        $chat = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM `chatrooms` WHERE `Id` = '$chatId' "));
        ?>
        <div class="p-5 mb-4 bg-light rounded-3">
          <div class="container-fluid py-5">
            <h1 class="display-5 fw-bold">Приветствую, <?= $user["User"]; ?>!</h1>
            <form action='index.php?userName=<?=$curUser?>' method='POST'>
                <button type="submit" class="btn btn-primary btn-lg" type="button">Назад</button>
            </form>
          </div>
        </div>

        <div class="row align-items-md-stretch">
          <div class="col-md-6">
            <div class="h-100 p-5 bg-light border rounded-3">
              <div class="row">
                  <div class="col-md-4">
                      <input type="text" id="fromUser" value=<?= $user['Id']; ?>  hidden />
                      <input type="text" id="toChat" value=<?= $chat['Id']; ?>  hidden />
                  </div>
              <h2>Вы в чате - <?= $chat["Chat"]; ?></h2>
              <p>
                <?php
                    $msgs = mysqli_query($connect, "SELECT * FROM `chatrooms`");
                            while($msg = mysqli_fetch_assoc($msgs))
                            {
                                echo '<li><a href="?toChat='.$msg['Id'].'&curUser='.$curUser.'">'.$msg['Chat'].'</a></li>';
                            }
                ?>
              </p>
              <form action='index.php?userName=<?=$curUser?>' method='POST'>
                <button type="submit" class="btn btn-outline-secondary" type="button">Назад</button>
              </form>
            </div>
          </div>
        </div>
          <div class="col-md-6">


          <div class="container-fluid">
              <?php 
              if(isset($_GET['curUser'])){
                  $curUser = $_GET['curUser'];
              }else {
                  $curUser=$_SESSION["curUser"];
              }
              
              
              $user = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM `users` WHERE `Id` = '$curUser' "));
              
              if(isset($_GET['toChat'])){
                  $chatId = $_GET['toChat'];
              }else {
                  $chatId=$_SESSION["chatId"];
              }
              $chat = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM `chatrooms` WHERE `Id` = '$chatId' "));

              ?>
                      <div class="modal-dialog">
                          <div class="modal-content">
                               <div class="modal-header">
                                  <h4>
                                      <?php
                                      if(isset($_GET["toChat"]))
                                      {
                                          $toChat=$_GET["toChat"];
                                          $chatName = mysqli_query($connect, "SELECT * FROM `chatrooms` WHERE `Id` = '$toChat' ");
                                          $cName = mysqli_fetch_assoc($chatName);
                                          echo '<input type="text" value='.$toChat.' id="toChat" hidden/>';
                                          echo $cName["Chat"];  
                                      }
                                      else
                                      {
                                          $chatName = mysqli_query($connect, "SELECT * FROM `chatrooms`");
                                          $cName = mysqli_fetch_assoc($chatName);
                                          $_SESSION['toChat'] = $cName['Id'];
                                          $toChat = $cName['Id'];
                                          echo '<input type="text" value='.$toChat.' id="toChat" hidden/>';
                                          echo $cName['Chat'];
                                      }
                                      ?>
                                  </h4>
                              </div>
                               <div class="modal-body" id="msgBody" style="height:400px; overflow-y: scroll; overflow-x: hidden;">
                                  <?php
                                      if(isset($_GET["toChat"]))
                                          $chats = mysqli_query($connect, "SELECT * FROM messages WHERE ToChat = '".$_GET["toChat"]."'")
                                          or die("Failed to query database".mysql_error());
                                          
                                      else
                                          $chats = mysqli_query($connect, "SELECT * FROM messages WHERE ToChat = '".$_SESSION["toChat"]."'")
                                          or die("Failed to query database".mysql_error());
                                          
                                          while($chat = mysqli_fetch_assoc($chats))
                                          {
                                             if($chat["FromUser"] == $curUser)
                                                  echo "<div style='text-align:right;'>
                                                      <p style='background-color:lightblue; word-wrap:break-word; display:inline-block; padding:5px; border-radius:10px; max width:70%;'>".$chat["Message"]."
                                                      </p>
                                                      </div>";
                                              else
                                                  echo "<div style='text-align:left;'>
                                                      <p style='background-color:lightblue; word-wrap:break-word; display:inline-block; padding:5px; border-radius:10px; max width:70%;'>".$chat["Message"]."
                                                      </p>
                                                      </div>";
                                          }
                                          
                                  ?>
                              </div>
                              <div class="modal-footer">
                                  <textarea id="message" class="form-control" style="height:70px;"></textarea>
                                  <button id="send" class="btn btn-primary" style="height:70%;">Отправить</button>
                              </div>
                          </div>

                  </div>
              </div>
          </div>
        </div>
      </div>
      </body>
      <script type="text/javascript">
          document.getElementById("send").addEventListener('click', function() {
                  
                      
                  let msg = $("#message").val();
                  
                  $.ajax({
                      url:"insertMessage.php",
                      method:"POST",
                      data:{
                          fromUser:$("#fromUser").val(),
                          toChat:$("#toChat").val(),
                          "message": msg
                      },
                      
                      dataType:"text",
                      success:function(data)
                      {
                          $("#message").val("");
                      }
                  });
              

              
          });

          setInterval(function(){
                  $.ajax({
                      url:"realTimeChat.php",
                      method:"POST",
                      data:{
                          fromUser: $("#fromUser").val(),
                          toChat: $("#toChat").val(),
                          /* "message": msg */
                      },
                      dataType:"text",
                      success:function(data)
                      {
                          $("#msgBody").html(data);
                      }
                  });
              }, 100);

      </script>
      </html>