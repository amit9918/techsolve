<!DOCTYPE html>
<html>

<head>
    <title>Contact Form</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">

    <link rel="stylesheet" href="css/login.css" text="text/css">

    <script src="https://kit.fontawesome.com/0c1e2b8cf4.js" crossorigin="anonymous"></script>
</head>

<body>
    <div>
        <?php
            require 'conn.php';
            session_start();
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                $errors = [];
                $name= mysqli_real_escape_string($conn,$_POST['name']);
                $email=mysqli_real_escape_string($conn,$_POST['email']);
                $contact=$_POST['contact'];
                $subject=mysqli_real_escape_string($conn,$_POST['subject']);
                $message=mysqli_real_escape_string($conn,$_POST['message']);
                $user_ip = $_SERVER["REMOTE_ADDR"]; // for storing user ip address

                $duplicate_user_query="select id from contact_form where email='$email'";  // checking for duplicate users
                $duplicate_user_result=mysqli_query($conn,$duplicate_user_query) or die(mysqli_error($conn));
            
                $rows_fetched=mysqli_num_rows($duplicate_user_result);
            
                // Validate name
                if($name =="") {
                    $errorMsg=  "Error : Please Enter Your name.";
                    $code= "1" ;
                }elseif($contact == "") {
                    $errorMsg1=  "Error : Please Enter Your number.";
                    $code= "2";
                }
                //check if the number field is numeric
                elseif(is_numeric(trim($contact)) == false){
                    $errorMsg1=  "Error : Please Enter Numeric Value.";
                    $code= "2";
                }elseif(strlen($contact)<10){
                    $errorMsg1=  "Error : Number should be 10 digits only.";
                    $code= "2";
                }elseif($email == ""){ //check if email field is empty
                    $errorMsg2=  "Error : Please Enter a Email.";
                    $code= "3";
                }else if($rows_fetched > 0){//check for valid email 
                    $errorMsg2=  "Error : User Already Exists..";
                    $code= "2";
                }elseif(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $email)){
                $errorMsg2= 'Error : You did not enter a valid email.';
                $code= "3";
                }elseif($subject =="") {
                    $errorMsg3=  "Error : Please Enter Any Subject Name.";
                    $code= "1" ;
                }elseif($message =="") {
                    $errorMsg4=  "Error : Please Enter a Message.";
                    $code= "1" ;
                }else{
                    
                    $user_registration_query="insert into contact_form(name,phone_number,email,subject,message,user_ip) values ('$name','$contact','$email','$subject','$message','$user_ip');";
                
                    $user_registration_result=mysqli_query($conn,$user_registration_query) or die(mysqli_error($conn));
                    
            
                    $html="<table><tr><td>Name</td><td>$name</td></tr><tr><td>Email</td><td>$email</td></tr><tr><td>Mobile</td><td>$contact</td></tr><tr><td>Subject</td><td>$subject</td></tr><tr><td>Message</td><td>$message</td></tr></table>";
                
                    include('smtp/PHPMailerAutoload.php');
                    $mail=new PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host="smtp.gmail.com";
                    $mail->Port=587;
                    $mail->SMTPSecure="tls";
                    $mail->SMTPAuth=true;
                    $mail->Username="test@techsolvitservice.com";
                    $mail->Password="Enter_Your_Email_id_Password";
                    $mail->SetFrom("test@techsolvitservice.com");
                    $mail->addAddress("test@techsolvitservice.com");
                    $mail->IsHTML(true);
                    $mail->Subject="New User Successfully registered";
                    $mail->Body=$html;
                    $mail->SMTPOptions=array('ssl'=>array(
                        'verify_peer'=>false,
                        'verify_peer_name'=>false,
                        'allow_self_signed'=>false
                    ));
            
                    // if($mail->send()){
                    //     echo "Mail send";
                    // }else{
                    //     echo "Error occur";
                    // }

                    header("Location: index.php?msg=User Successfully Registered");
                   
                }
            }
              if(isset($_GET["msg"])){
                $successMsg=$_GET["msg"];
            }else{
                $successMsg="";
            };
        ?>
        <div class="container mt-5 center-box">

            <div class="row">
                <div class="col-lg-5 contact-shadow">
                    <form method="post" action="">
                        <?php if (isset($successMsg)) { echo "<p class='message'>" .$successMsg. "</p>" ;} ?>
                        <div class="row mb-5">
                            <h3><span style="color:red">*</span> These are mandatory field.</h3>
                        </div>

                        <div class="row">
                            <div class="col-lg-12 mb-4">
                                <div>Full Name: <span style="color:red">*</span></div>
                                <div><input type="text" placeholder="Enter Your Full Name" class="form-control"
                                        name="name" value="<?php if(isset($name)){echo $name;} ?>"
                                        <?php if(isset($code) && $code == 1){echo "class=errorMsg" ;} ?>>
                                </div>
                                <?php if (isset($errorMsg)) { echo "<p class='message'>" .$errorMsg. "</p>" ;} ?>
                            </div>

                            <div class="col-lg-12 mb-4">
                                <div>Phone Number: <span style="color:red">*</span></div>
                                <div><input type="text" name="contact" class="form-control" placeholder="Phone Number"
                                        value="<?php if(isset($contact)){echo $contact;} ?>"
                                        <?php if(isset($code) && $code == 2){echo "class=errorMsg" ;}?>>
                                </div>
                                <?php if (isset($errorMsg1)) { echo "<p class='message'>" .$errorMsg1. "</p>" ;} ?>
                            </div>

                            <div class="col-lg-12 mb-4">
                                <div>Email: <span style="color:red">*</span></div>
                                <div><input type="email" placeholder="Enter Your Email" class="form-control"
                                        name="email" value="<?php if(isset($email)){echo $email; }?>"
                                        <?php if(isset($code) && $code == 3){echo "class=errorMsg" ;}?>>
                                </div>
                                <?php if (isset($errorMsg2)) { echo "<p class='message'>" .$errorMsg2. "</p>" ;} ?>
                            </div>

                            <div class="col-lg-12 mb-4">
                                <div>Subject: <span style="color:red">*</span></div>
                                <div><input type="text" name="subject" class="form-control" placeholder="Subject"
                                        value="<?php if(isset($subject)){echo $subject; }?>"
                                        <?php if(isset($code) && $code == 3){echo "class=errorMsg" ;}?>>
                                </div>
                                <?php if (isset($errorMsg3)) { echo "<p class='message'>" .$errorMsg3. "</p>" ;} ?>
                            </div>

                            <div class="col-lg-12 mb-4">
                                <div>Message: <span style="color:red">*</span></div>
                                <div><input type="text" name="message" class="form-control" placeholder="Message"
                                        value="<?php if(isset($message)){echo $message; }?>"
                                        <?php if(isset($code) && $code == 3){echo "class=errorMsg" ;}?>>
                                </div>
                                <?php if (isset($errorMsg4)) { echo "<p class='message'>" .$errorMsg4. "</p>" ;} ?>
                            </div>

                        </div>

                        <div class="button" style="">
                            <input type="submit" class="btn btn-danger" value="Submit">
                        </div>
                    </form>
                </div>
            </div>

        </div>

</body>

</html>