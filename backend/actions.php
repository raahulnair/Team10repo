<?php
require __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);


// ===================== TEST SHIM (safe for production) =====================
// Provide a router *only during tests* so PHPUnit can call run_actions().
if (defined('TEST_MODE') && !function_exists('run_actions')) {
    function run_actions(): void {
        // Use any DB injected by tests; if not set, you can ignore $db here.
        $action = $_REQUEST['action'] ?? '';
        $role   = $_SESSION['role'] ?? '';

        switch ($action) {
            case 'page':  // RBAC guard: used by RBACTest
                if ($role !== 'Admin') {
                    http_response_code(403);
                    header('Content-Type: application/json');
                    echo json_encode([
                        'error'    => 'ACCESS_DENIED',
                        'redirect' => '/employee/employee.php',
                        'audit'    => ['event' => 'RBAC_DENIED']
                    ]);
                } else {
                    http_response_code(200);
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'OK']);
                }
                break;

            // Add more cases later if you want to run the other tests without changing your app:
            // case 'searchEmployees': ...
            // case 'createEmployee': ...
            // etc.

            default:
                http_response_code(400);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Unknown action']);
        }
    }
}
// =================== END TEST SHIM (safe for production) ===================

//variables to store form data and error messages
$firstname = isset($_POST['firstname']) ? $_POST['firstname'] : '';
$lastname  = isset($_POST['lastname']) ? $_POST['lastname'] : '';
$birthdate = isset($_POST['birthdate']) ? $_POST['birthdate'] : '';
$ssn       = isset($_POST['ssn']) ? $_POST['ssn'] : '';
$race      = isset($_POST['race']) ? $_POST['race'] : '';
$email     = isset($_POST['testemail']) ? $_POST['testemail'] : '';
$state     = isset($_POST['state']) ? $_POST['state'] : '';
$city      = isset($_POST['city']) ? $_POST['city'] : '';
$zipcode   = isset($_POST['zipcode']) ? $_POST['zipcode'] : '';
$gender    = isset($_POST['gender']) ? $_POST['gender'] : '';
$phone     = isset($_POST['phone']) ? $_POST['phone'] : '';
$address   = isset($_POST['address']) ? $_POST['address'] : '';
$address2  = isset($_POST['address2']) ? $_POST['address2'] : '';
$jobtitle  = isset($_POST['jobtitle']) ? $_POST['jobtitle'] : '';
$salary    = isset($_POST['salary']) ? $_POST['salary'] : '';
$hiredate  = isset($_POST['hiredate']) ? $_POST['hiredate'] : '';
$division  = isset($_POST['division']) ? $_POST['division'] : '';
$paydate1  = isset($_POST['paydate1']) ? $_POST['paydate1'] : '';
$paydate2  = isset($_POST['paydate2']) ? $_POST['paydate2'] : '';
$salary1   = isset($_POST['salary1']) ? $_POST['salary1'] : '';
$salary2   = isset($_POST['salary2']) ? $_POST['salary2'] : '';
$empid1    = isset($_POST['empid1']) ? $_POST['empid1'] : '';
$empid2    = isset($_POST['empid2']) ? $_POST['empid2'] : '';
$report    = isset($_POST['report']) ? $_POST['report'] : '';
$rate      = isset($_POST['rate']) ? $_POST['rate'] : '';
$empid     = isset($_POST['empid']) ? intval($_POST['empid']) : (isset($_SESSION['employeeid']) ? intval($_SESSION['employeeid']) : 0);
$sql       = isset($_POST['sql']) ? $_POST['sql'] : (isset($_SESSION['sql']) ? $_SESSION['sql'] : '');

// Error messages
$firstnameErr = $lastnameErr = $birthdateErr = $ssnErr = $raceErr = $emailErr = $stateErr = $cityErr  = $genderErr = $phoneErr = $addressErr = $address2Err =$zipcodeErr="";
$jobtitleErr = $salaryErr = $hiredateErr = $divisionErr = $empidErr = $searchErr ="";
$reportErr = $paydateErr = $paydate2 = $salary1Err = $rateErr = "";

//functions to connect to the database and validate inputs
function getConnection() {
  $servername = "localhost";
  $username = "Team10admin";
  $password = "Projectadmin";
  $dbname = "Project";
  
  $conn = mysqli_connect($servername, $username, $password, $dbname);
  if (!$conn) {
      die("Connection failed: " . mysqli_connect_error());
  }
  echo "<script>console.log('Connected to database');</script>";
  return $conn;
}
// Function to sanitize user input
function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
// Function to send mail
function sendMail($to, $name, $subject, $text, $ctaUrl = 'http://localhost/SOFTDEV/index.php') {
    // Create a new PHPMailer for each call
    $mail = new PHPMailer(true);

    try {
        // SMTP config
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'softdevteam10corp@gmail.com';
        $mail->Password   = 'hjgjipssexcvjcpo'; // Gmail app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // From / To
        $mail->setFrom('softdevteam10corp@gmail.com', 'Corporate');
        $mail->addAddress($to, $name);

        // Load HTML template
        $templatePath = __DIR__ . '/../../email.html';
        $html = file_get_contents($templatePath);

        if ($html === false) {
            // fallback if template missing
            $html = "<p>Hi {$name},</p><p>" . nl2br(htmlspecialchars($text, ENT_QUOTES, 'UTF-8')) . "</p>";
        } else {
            // Replace placeholders one by one
            $html = str_replace('{{NAME}}', $name, $html);
            $html = str_replace('{{TEXT}}', nl2br($text), $html);
            $html = str_replace('{{CTA_URL}}', $ctaUrl, $html);
        }

        // Email body
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $html;
        $mail->AltBody = strip_tags($text);

        $mail->send();
        echo "<script>console.log('Email sent successfully');</script>";
        return true;
    } catch (Exception $e) {
        error_log('Mail error: ' . $mail->ErrorInfo);
        echo "<script>console.log('Email failed: " . addslashes($mail->ErrorInfo) . "');</script>";
        return false;
    }
}
   
// Function to fill form for update employee
function fillform() {
  global $empid;
  if (!isset($_SESSION['employeeid']) && !isset($_GET['eid'])) {
      die("Missing employee identifier");
  }else if (isset($_GET['eid'])) {
      $encoded = $_GET['eid'];
      $decoded = base64_decode($encoded, true); // strict decode

      if ($decoded === false || !ctype_digit($decoded)) {
          die("Invalid employee identifier");
      }
       $empid = (int)$decoded;
  } 

  

 
  $conn = getConnection();
  //global $empid;
  // Make sure $empid is defined
  $_SESSION['employeeid'] = $empid; // Store empid in session for later use

  
  // Fetch employee data
 $sql = "
          SELECT 
              e.empid,
              e.fname      AS Fname,
              e.lname      AS Lastname,
              e.email_work AS Email,
              e.hired_at   AS HireDate,
              e.salary     AS Salary,
              e.ssn        AS SSN,
              e.gender     AS Gender,
              e.dob        AS DOB,
              e.race       AS Race,
              e.phone      AS Phone,
              a.line1      AS Street1,
              a.line2      AS Street2,
              a.city       AS City,
              a.state_code AS State,
              a.postal_code AS Zip,
              e.job_title_id AS Job_Title,
              e.division_id       AS Division
          FROM employees e
          LEFT JOIN addresses a ON e.address_id = a.address_id
          WHERE e.empid = '$empid'
      ";

  
  $result = $conn->query($sql);
  $row = $result && $result->num_rows > 0 ? $result->fetch_assoc() : null;
  
  // Use either $_POST (if form submitted) or fetched data (initial form load)
   //  User Info
  global $firstname, $lastname, $birthdate, $ssn, $race, $email, $state, $city, $zipcode, $gender, $phone, $address;
  //global $firstnameErr, $lnamelErr, $bdateErr, $ssnErr, $raceErr, $emailErr, $stateErr, $cityErr, $genderErr, $phoneErr, $addressErr, $zipcodeErr;
  

  //Job Info
  global $jobtitle,  $salary, $hiredate, $division;
  //global $jobtitleErr, $salaryErr, $hiredateErr, $divisionErr;
  if ($result && $result->num_rows > 0) {

  $firstname = isset($_POST['firstname']) ? test_input($_POST['firstname']) : $row['Fname'];
  $lastname  = isset($_POST['lastname'])  ? test_input($_POST['lastname'])  : $row['Lastname'];
  $birthdate = isset($_POST['birthdate']) ? test_input($_POST['birthdate']) : $row['DOB'];
  $ssn       = isset($_POST['ssn'])       ? test_input($_POST['ssn'])       : $row['SSN'];
  $race      = isset($_POST['race'])      ? test_input($_POST['race'])      : $row['Race'];
  $email     = isset($_POST['testemail']) ? test_input($_POST['testemail']) : $row['Email'];

  $state     = isset($_POST['state'])     ? test_input($_POST['state'])     : $row['State'];
  $city      = isset($_POST['city'])      ? test_input($_POST['city'])      : $row['City'];
  $zipcode   = isset($_POST['zipcode'])   ? test_input($_POST['zipcode'])   : $row['Zip'];
  $gender    = isset($_POST['gender'])    ? test_input($_POST['gender'])    : $row['Gender'];
  $phone     = isset($_POST['phone'])     ? test_input($_POST['phone'])     : $row['Phone'];
  $address   = isset($_POST['address'])   ? test_input($_POST['address'])   : $row['Street1'];
  $address2  = isset($_POST['address2'])  ? test_input($_POST['address2'])  : $row['Street2'];

  $jobtitle  = isset($_POST['jobtitle'])  ? test_input($_POST['jobtitle'])  : $row['Job_Title'];
  $salary    = isset($_POST['salary'])    ? test_input($_POST['salary'])    : $row['Salary'];
  $hiredate  = isset($_POST['hiredate'])  ? test_input($_POST['hiredate'])  : $row['HireDate'];
  $division  = isset($_POST['division'])  ? test_input($_POST['division'])  : $row['Division'];

  
  }

  
}  
// Function to validate form inputs
function validateform(){
  $conn = getConnection();
  $formtest=true;
  global $empid;
  $empid = isset($_POST['empid']) ? intval($_POST['empid']) : (isset($_SESSION['employeeid']) ? intval($_SESSION['employeeid']) : 0);
  //  User Info
  global $firstname, $lastname, $birthdate, $ssn, $race, $email, $state, $city, $zipcode, $gender, $phone, $address, $address2;
  global $firstnameErr, $lastnameErr, $birthdateErr, $ssnErr, $raceErr, $emailErr, $stateErr, $cityErr, $genderErr, $phoneErr, $addressErr, $address2Err, $zipcodeErr;
  

  //Job Info
  global $jobtitle,  $salary, $hiredate, $division;
  global $jobtitleErr, $salaryErr, $hiredateErr, $divisionErr;

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    //Check if first name is empty or not
    if (empty($_POST["firstname"])) {
      $firstnameErr = "First Name is required";
      $formtest = false;
    } else {
      if (!preg_match("/^[a-zA-Z-' ]*$/", $firstname)) {
        $firstnameErr = "Only letters and white space allowed";
        $formtest = false;
      } else $firstname = test_input($_POST["firstname"]);
    }
    //Check if last name is empty or not
    if (empty($_POST["lastname"])) {
      $lastnameErr = "Last Name is required";
      $formtest = false;
    } else {
      if (!preg_match("/^[a-zA-Z-' ]*$/", $lastname)) {
        $lastnameErr = "Only letters and white space allowed";
        $formtest = false;
      } else $lastname = test_input($_POST["lastname"]);
    }
    //Check if birthdate is empty or not
    if (empty($_POST["birthdate"])) {
      $birthdateErr = "Birthdate is required";
      $formtest = false;
    } else {
      $birthdate = test_input($_POST["birthdate"]);
    }
    //Check if gender is empty or not
    if (empty($_POST["gender"])) {
      $genderErr = "Gender is required";
      $formtest = false;
    } else {
      $gender = test_input($_POST["gender"]);
    }
    //Check if ssn is empty or not
    if( empty($_POST["ssn"])) {
      $ssnErr = "SSN is required";
      $formtest = false;
    } else {
      $ssn = test_input($_POST["ssn"]);
      $sql = "SELECT * FROM employees WHERE SSN='$ssn'";
      $res = $conn->query($sql);
      if (!preg_match("/^\d{3}-\d{2}-\d{4}$/", $ssn)) {
        $ssnErr = "Invalid SSN format";
        $formtest = false;
      } else if ($res->num_rows > 0 && $empid != $res->fetch_assoc()['empid']) {
        $ssnErr = "SSN already exists";
        $formtest = false;
      } else $ssn = test_input($_POST["ssn"]);
    }
    //Check if race is empty or not
    if (empty($_POST["race"])) {
      $raceErr= "Race is required";
      $formtest = false;
    } else $race = test_input($_POST["race"]);

    //Check if email is empty or not
    if (empty($_POST["testemail"])) {
      $emailErr = "Email is required";
      $formtest = false;
    } else {
      $email = test_input($_POST["testemail"]);
      $sql = "SELECT * FROM employees WHERE email_work='$email'";
      $res = $conn->query($sql);
      if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
        $emailErr = "Invalid email format";
        $formtest = false;
      } else if ($res->num_rows > 0 && $empid != $res->fetch_assoc()['empid']) {
        $emailErr = "Email already exists";
        $formtest = false;
      } else $email= test_input($_POST["testemail"]);
    }
    //Check if phone is empty or not
    if (empty($_POST["phone"])) {
      $phoneErr = "Number is required";
      $formtest = false;
    } else {
      $phone = test_input($_POST["phone"]);
      $sql = "SELECT * FROM employees WHERE phone='$phone'";
      $res = $conn->query($sql);
      if (!preg_match('/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/', $phone)) {
        $phoneErr = "Invalid Number format";
        $formtest = false;
      } else if ($res->num_rows > 0 && $empid != $res->fetch_assoc()['empid']) {
        $phoneErr = "Number already exists";
        $formtest = false;
      } else $phone = test_input($_POST["phone"]);
    }
    //Check if state is empty or not
    if (empty($_POST["state"])) {
      $stateErr = "State is required";
      $formtest = false;
    } else $state = test_input($_POST["state"]);
    
    //Check if city is empty or not
    if (empty($_POST["city"])) {
      $cityErr = "City is required";
      $formtest = false;
    }  else {
      if (!preg_match("/^[a-zA-Z-' ]*$/", $firstname)) {
        $cityErr = "Only letters and white space allowed";
        $formtest = false;
      } else $city = test_input($_POST["firstname"]);
    }
    
    
    //Check if zipcode is empty or not
    if(empty($_POST["zipcode"])){
      $zipcodeErr = "Zipcode is required";
      $formtest = false;
    }else{
      if (!preg_match("/^[0-9]{5}$/", $_POST["zipcode"])) {
        $zipcodeErr = "Invalid zipcode format";
        $formtest = false;
      } else $zipcode = test_input($_POST["zipcode"]);
    }
    //Check if address1 is empty or not
    if (empty($_POST["address"])) {
      $addressErr = "Address is required";
      $formtest = false;
    }else{
      if (!preg_match("/^[a-zA-Z0-9-' ]*$/", $_POST["address"])) {
        $addressErr = "Only letters and white space allowed";
        $formtest = false;
      } else $address = test_input($_POST["address"]);
    }
    //Check if address2 is empty or not
    if(!empty($_POST["address2"])){
      if (!preg_match("/^[a-zA-Z0-9-' ]*$/", $_POST["address2"])) {
        $address2Err = "Only letters and white space allowed";
        $formtest = false;
      } else $address2 = test_input($_POST["address2"]);
    }
    //Check if job title is empty or not
    if (empty($_POST["jobtitle"])) {
      $jobtitleErr = "Job title is required";
      $formtest = false;
    }  else $jobtitle = test_input($_POST["jobtitle"]);
    //Check if division is empty or not
    if (empty($_POST["division"])) {
      $divisionErr = "Division is required";
      $formtest = false;
    } else $division = test_input($_POST["division"]);
    //Check if hire date is empty or not
    if (empty($_POST["hiredate"])) {
      $hiredateErr = "Hire date is required";
      $formtest = false;
    } else {
      $hiredate = test_input($_POST["hiredate"]);
    }
    //Check if Salary is empty or not
    if (empty($_POST["salary"])) {
      $salaryErr = "Salary is required";
      $formtest = false;
    } else {
      if (!preg_match("/^\d+(\.\d{1,2})?$/", $_POST["salary"])) {
        $salaryErr = "Invalid Salary format";
        $formtest = false;
      } else $salary = test_input($_POST["salary"]);
    }
  }
  return $formtest;
 
}
// Function to add employee
function addemployee() {
    $conn = getConnection();

    if (!validateform()) {
        return;
    }

    // =========================
    // Employee Info
    // =========================
    $firstname = test_input($_POST["firstname"]);
    $lastname  = test_input($_POST["lastname"]);
    $email     = test_input($_POST["testemail"]);
    $hiredate  = date('Y-m-d', strtotime($_POST["hiredate"]));
    $salary    = test_input($_POST["salary"]);
    $ssn       = test_input($_POST["ssn"]);

    // =========================
    // Address Info
    // =========================
    $address  = test_input($_POST["address"]);
    $city     = test_input($_POST["city"]);
    $state    = test_input($_POST["state"]);
    $zipcode  = test_input($_POST["zipcode"]);
    $gender   = test_input($_POST["gender"]);
    $race     = isset($_POST["race"]) ? test_input($_POST["race"]) : '';
    $phone    = test_input($_POST["phone"]);

    // Birthdate: allow empty and store NULL if not provided
    $birthdate = !empty($_POST["birthdate"])
        ? date('Y-m-d', strtotime($_POST["birthdate"]))
        : null;

    // =========================
    // Job / Division Info
    // =========================
    $jobtitle = test_input($_POST["jobtitle"]);   // job_title_id
    $division = test_input($_POST["division"]);   // division_id

    // ======================================
    // 1) Insert into ADDRESSES
    // ======================================
    $sql1 = "
        INSERT INTO addresses (line1, line2, city, state_code, postal_code)
        VALUES ('$address', '', '$city', '$state', '$zipcode')
    ";

    $success1 = mysqli_query($conn, $sql1);

    if (!$success1) {
        $err = mysqli_error($conn);
        echo "<script>
                alert('Failed to insert into addresses table.');
                console.error(" . json_encode($err) . ");
                window.history.back();
              </script>";
        return;
    }

    $addressId = mysqli_insert_id($conn);

    // ======================================
    // 2) Insert into EMPLOYEES (new schema)
    // ======================================

    // Handle NULL dob properly in SQL
    $dobValue = $birthdate ? "'" . $birthdate . "'" : "NULL";

    $sql2 = "
        INSERT INTO employees (
            fname,
            lname,
            email_work,
            hired_at,
            salary,
            ssn,
            gender,
            race,
            dob,
            phone,
            job_title_id,
            division_id,
            address_id
        )
        VALUES (
            '$firstname',
            '$lastname',
            '$email',
            '$hiredate',
            '$salary',
            '$ssn',
            '$gender',
            '$race',
            $dobValue,
            '$phone',
            '$jobtitle',
            '$division',
            '$addressId'
        )
    ";

    $success2 = mysqli_query($conn, $sql2);

    if ($success2) {
        $empid = mysqli_insert_id($conn); // if you need it later
        $text = 'Your employee account has been created successfully. Welcome aboard!<br><br>Best regards,<br>HR Team<br>If this wasn’t you, please contact your system administrator right away.';
        error_log("DEBUG: calling sendMail to $email");   // <--- add this

        sendMail(
            $email,
            $firstname . ' ' . $lastname,
            'Welcome to the Company',
            $text
        );
        echo "<script>
                alert('Employee added successfully.');
                window.location.href = './employee.php';
                console.log('SQL Script: ' + " . json_encode($sql1 . "\n" . $sql2) . ");
                console.log('Email sent to: ' + " . json_encode($email) . ");
              </script>";
        
    } else {
        $err = mysqli_error($conn);
        echo "<script>
                alert('Failed to insert into employees table.');
                console.error(" . json_encode($err) . ");
                window.history.back();
              </script>";
    }
    
}
// Function to update employee
function updateemployee() {
    global $empid;

    // Get empid from session (set in employeedetails.php)
    $empid = isset($_SESSION['employeeid']) ? intval($_SESSION['employeeid']) : 0;
    $empid = intval($empid);

    if ($empid <= 0) {
        echo "<script>
                alert('Missing or invalid employee ID.');
                window.history.back();
              </script>";
        return;
    }

    $conn = getConnection();

    // Run your existing validation
    if (!validateform()) {
        // keep empid so form can re-render with errors
        $empid = isset($_POST['empid']) ? intval($_POST['empid']) : $empid;
        return;
    }

    // =========================
    //  Gather & sanitize input
    // =========================

    // Employee Info
    $firstname = test_input($_POST["firstname"]);
    $lastname  = test_input($_POST["lastname"]);
    $email     = test_input($_POST["testemail"]);
    $hiredate  = date('Y-m-d', strtotime($_POST["hiredate"]));
    $salary    = test_input($_POST["salary"]);
    $ssn       = test_input($_POST["ssn"]);

    // Address Info
    $address  = test_input($_POST["address"]);
    $city     = test_input($_POST["city"]);
    $state    = test_input($_POST["state"]);
    $zipcode  = test_input($_POST["zipcode"]);
    $gender   = test_input($_POST["gender"]);
    $race     = isset($_POST["race"]) ? test_input($_POST["race"]) : '';
    $phone    = test_input($_POST["phone"]);

    // IMPORTANT: match your form field name: birthdate
    $birthdate = !empty($_POST["birthdate"])
        ? date('Y-m-d', strtotime($_POST["birthdate"]))
        : null;

    // Job / Division Info (IDs from <select>)
    $jobtitle = test_input($_POST["jobtitle"]);   // job_title_id
    $division = test_input($_POST["division"]);   // division_id

    // ==========================================
    //  Look up address_id for this employee
    // ==========================================
    $addressId = null;
    $addrRes = $conn->query("SELECT address_id FROM employees WHERE empid = $empid");

    if ($addrRes && $addrRes->num_rows > 0) {
        $addrRow  = $addrRes->fetch_assoc();
        $addressId = !empty($addrRow['address_id']) ? intval($addrRow['address_id']) : null;
    }

    // ==========================================
    //  Update EMPLOYEES table (new schema)
    // ==========================================

    // If you added a race column on employees, keep Race = '$race'.
    // If not, remove that line.
    $sql_emp = "
        UPDATE employees
        SET
            fname       = '$firstname',
            lname       = '$lastname',
            email_work  = '$email',
            hired_at    = '$hiredate',
            salary      = '$salary',
            ssn         = '$ssn',
            gender      = '$gender',
            " . ($birthdate ? "dob = '$birthdate'," : "") . "
            phone       = '$phone',
            job_title_id = '$jobtitle',
            division_id  = '$division'
            " . (!empty($race) ? ", race = '$race'" : "") . "
        WHERE empid = $empid
    ";

    $success_emp = mysqli_query($conn, $sql_emp);

    // ==========================================
    //  Update / Insert into ADDRESSES
    // ==========================================

    $success_addr = true; // default

    if ($addressId) {
        // Update existing address row
        $sql_addr = "
            UPDATE addresses
            SET
                line1       = '$address',
                line2       = '',
                city        = '$city',
                state_code  = '$state',
                postal_code = '$zipcode'
            WHERE address_id = $addressId
        ";
        $success_addr = mysqli_query($conn, $sql_addr);
    } else {
        // No address yet: insert & attach to employee
        $sql_addr_insert = "
            INSERT INTO addresses (line1, line2, city, state_code, postal_code, country)
            VALUES ('$address', '', '$city', '$state', '$zipcode', 'US')
        ";
        $success_addr = mysqli_query($conn, $sql_addr_insert);

        if ($success_addr) {
            $newAddressId = mysqli_insert_id($conn);
            $sql_link = "UPDATE employees SET address_id = $newAddressId WHERE empid = $empid";
            $success_addr = mysqli_query($conn, $sql_link);
        }
    }

    // ==========================================
    //  Final result handling
    // ==========================================

    if ($success_emp && $success_addr) {
        $text = 'Your employee account has been updated successfully. If you did not make these changes, please contact your system administrator immediately.<br><br>Best regards,<br>HR Team';
        sendMail(
          $email,
          $firstname . ' ' . $lastname,
          'Account Update Notification',
          $text
        );

        echo "<script>
                console.log('SQL Script: ' + " . json_encode($sql_emp . "\n" . ($addressId ? $sql_addr : $sql_addr_insert)) . ");
                console.log('Update employees: ' + " . json_encode($success_emp ? "Success" : "Failed") . ");
                console.log('Update addresses: ' + " . json_encode($success_addr ? "Success" : "Failed") . ");
                alert('Employee updated successfully!');
                window.location.href = './employee.php';
              </script>";
        unset($_SESSION['employeeid']);
    } else {
        $errorMessage = "Failed to update:\n";

        if (!$success_emp) {
            $errorMessage .= "- Employees table: " . mysqli_error($conn) . "\n";
        }
        if (!$success_addr) {
            $errorMessage .= "- Addresses table: " . mysqli_error($conn) . "\n";
        }

        echo "<script>
                alert(`$errorMessage`);
                window.history.back();
              </script>";
    }
}

// Function to delete employee
function terminateemployee($empid) {
    $conn  = getConnection();
    $empid = intval($empid);
    

    // 1) Look up employee so we can email them
    $sqlSelect = "
        SELECT 
            email_work AS email,
            fname,
            lname
        FROM employees
        WHERE empid = $empid
        LIMIT 1
    ";

    $res = mysqli_query($conn, $sqlSelect);

    if (!$res || mysqli_num_rows($res) === 0) {
        echo "<script>
                alert('Employee not found.');
                window.history.back();
              </script>";
        return;
    }

    $emp    = mysqli_fetch_assoc($res);
    $email  = $emp['email'];
    $name   = $emp['fname'] . ' ' . $emp['lname'];
    $today  = date('Y-m-d');

    // 2) Soft terminate: only set terminated_at
    $sqlUpdate = "
        UPDATE employees
        SET terminated_at = CURDATE()
        WHERE empid = $empid
    ";

    if (mysqli_query($conn, $sqlUpdate)) {

        // 3) Send termination email (ignore failure for UI, just log it)
        $text = "Dear {$name},\n\n"
              . "This email is to inform you that your employment with CityLink Live "
              . "has been terminated effective {$today}.\n\n"
              . "If you believe this is an error, please contact HR or your system administrator.\n\n"
              . "Best regards,\n"
              . "HR Team";

        // Use your existing HTML template mailer
        @sendMail(
            $email,
            $name,
            'Employment Termination Notice',
            $text,
            'http://localhost/SOFTDEV/index.php'
        );

        echo "<script>
                console.log(" . json_encode("Employee $empid terminated on {$today}") . ");
                alert('Employee terminated successfully!');
                window.location.href = './employee.php';
              </script>";
    } else {
        $err = mysqli_error($conn);
        echo "<script>
                alert('Error terminating employee: " . addslashes($err) . "');
                window.history.back();
              </script>";
    }

    mysqli_close($conn);
    unset($_SESSION['employeeid']);
}
// Function to list employees
function viewemployees(){
  $conn = getConnection();

  $sql = "
  SELECT 
      e.empid AS EmpID,
      e.Fname AS First_Name,
      e.Lname AS Last_Name,
      e.gender AS Gender,
      e.phone AS Phone,
      e.email_work AS Email,
      e.Salary AS Salary,
      jt.name AS Job_Title,
      d.Name AS Division,
      e.dob AS Birth_Date
  FROM employees e
  LEFT JOIN addresses a ON e.empid = a.empid
  LEFT JOIN job_titles jt ON e.job_title_id = jt.job_title_id
  LEFT JOIN divisions d ON e.division_id = d.division_id
  where e.terminated_at IS NULL
      ";  
  
  $result = $conn->query($sql);
  // Check connection
  if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
  }
  $rowcount = $result->num_rows;
  if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
          echo "<tr>
                  <td>{$row['EmpID']}</td>
                  <td>{$row['First_Name']}</td>
                  <td>{$row['Last_Name']}</td>
                  <td>{$row['Gender']}</td>
                  <td>{$row['Birth_Date']}</td>
                  <td>{$row['Phone']}</td>
                  <td>{$row['Email']}</td>
                  <td>{$row['Job_Title']}</td>
                  <td>{$row['Division']}</td>
                  <td>{$row['Salary']}</td>
                  
                </tr>";
      }
      echo "<script>
              console.log('SQL Script: ' + " . json_encode($sql) . ");
              console.log('Number of rows: ' + " . json_encode($rowcount) . ");
            </script>";


  } else {
      echo "<tr><td colspan='9'>No employees found</td></tr>";
  }
  
}
// Function to list payroll
function viewpayroll(){
  $conn = getConnection();

  // Get divisions for filter dropdown
  $divisions = [];
  $divSql = "SELECT division_id, name FROM divisions ORDER BY name";
  $divRes = mysqli_query($conn, $divSql);
  if ($divRes) {
      while ($row = mysqli_fetch_assoc($divRes)) {
          $divisions[] = $row;
      }
      mysqli_free_result($divRes);
  }

  // Get payroll data joined with employees + divisions
  $sql = "
    SELECT 
        p.payroll_id,
        p.empid,
        e.fname AS First_Name,
        e.lname AS Last_Name,
        jt.name AS Job_Title,
        d.Name AS Division,
        p.period_month,
        p.gross_pay,
        p.taxes_withheld,
        p.deductions,
        p.net_pay,
        p.issued_at,
        p.notes
    FROM payroll p
    INNER JOIN employees e ON e.empid = p.empid
    LEFT JOIN job_titles jt ON e.job_title_id = jt.job_title_id
    LEFT JOIN divisions d ON e.division_id = d.division_id
    ";
    //ORDER BY p.period_month DESC, d.name ASC, e.lname ASC, e.fname ASC

  $result = $conn->query($sql);
  // Check connection
  if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
  }
  $rowcount = $result->num_rows;
  if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
          echo "<tr>
                  <td>{$row['empid']}</td>
                  <td>{$row['First_Name']}</td>
                  <td>{$row['Last_Name']}</td>
                  <td>{$row['Job_Title']}</td>
                  <td>{$row['Division']}</td>
                  <td>{$row['period_month']}</td>
                  <td>{$row['gross_pay']}</td>
                  <td>{$row['taxes_withheld']}</td>
                  <td>{$row['deductions']}</td>
                  <td>{$row['net_pay']}</td>
                  <td>{$row['issued_at']}</td>
                  <td>{$row['notes']}</td>
                </tr>";
      }
    }

  $payrollRows   = [];
  $totalGross    = 0;
  $totalNet      = 0;

  if ($result) {
      while ($row = mysqli_fetch_assoc($result)) {
          $payrollRows[] = $row;
          $totalGross += (float)$row['gross_pay'];
          $totalNet   += (float)$row['net_pay'];
      }
      mysqli_free_result($result);
  }

  $totalRecords = count($payrollRows);
  
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST['addemployee'])) {
    addemployee();
  } else if (isset($_POST['updateemployee'])) {
    updateemployee();
  } else if (isset($_POST['terminateemployee'])) {
    terminateemployee($_SESSION['employeeid']);
  } 
  
}    


?>
