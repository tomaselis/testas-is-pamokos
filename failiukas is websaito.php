<?php


// error handlingas

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once('../scripts/lcoa.php');

use PDO;
use PDOException;

Class ResumeMain
{


    // connects to the server

    function connect()
    {
        {
            $host = '127.0.0.1';
            $db = 'applicants';
            $user = 'username';
            $password = 'password';

            try {
                $pdo = new PDO("mysql:host=$host; dbname=$db; charset=utf8", $user, $password);
            } catch (PDOException $e) {
                echo 'Connection failed: ' . $e->getMessage();
                return false;
            }
            return $pdo;
        }
    }

    //Writes the information to the database


    public function addNewResume($target, $name, $email, $phone, $jobId, $jobTitle, $coverLetter, $resume)
    {

        if (move_uploaded_file($_FILES['resume']['tmp_name'], $target)) {
            $insertSQL = "INSERT INTO applicants (name, email, phone, jobid, jobtitle, coverletter, resume)
                      VALUES (:name, :email, :phone, :jobid, :jobtitle, :coverletter, :resume)";
            $stmt = connect()->prepare($insertSQL);
            $stmt->bindValue(':name', $name, PDO::PARAM_STR_CHAR);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR_CHAR);
            $stmt->bindValue(':phone', $phone, PDO::PARAM_STR);
            $stmt->bindValue(':jobid', $jobId, PDO::PARAM_STR_CHAR);
            $stmt->bindValue(':jobtitle', $jobTitle, PDO::PARAM_STR_CHAR);
            $stmt->bindValue(':coverletter', $coverLetter, PDO::PARAM_STR_CHAR);
            $stmt->bindValue(':resume', $resume, PDO::PARAM_STR_CHAR);
            $stmt->execute();
        }
        echo "Your form uploaded succesfully";
    }
}


// Class for working with Database


class ResumeModel
{
    private $name;
    private $email;
    private $phone;
    private $jobId;
    private $jobTitle;
    private $cover;

    public function getName()
    {
        return $this->name;
    }

    public function setToken($name)
    {
        $this->name = $name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    public function getJobId()
    {
        return $this->jobId;
    }

    public function setJobId($jobId)
    {
        $this->jobId = $jobId;
    }

    public function getJobTitle()
    {
        return $this->jobTitle;
    }

    public function setJobTitle($jobTitle)
    {
        $this->jobTitle = $jobTitle;
    }

    public function getCover()
    {
        return $this->cover;
    }

    public function setCover($cover)
    {
        $this->cover = $cover;
    }


    public function checkExtension()
    {
        if (isset ($resume)) {
            if (!empty ($resume)) {
                $extension = array('application/doc', 'application/docx', 'application/txt', 'application/pdf');
                $size = ($_FILES['resume']['size']);
                $maxSize = 3145728;
                foreach ($_FILES['application'] as $file) {
                    if (!in_array($file['application'], $extension) && $size <= $maxSize)
                        return true;
                    else {
                        return 'Error message wrong file extension';
                    }
                }
            }
        }
    }

    public function sendMessage($target)
    {

        //Sends Email
        $sendTo = "recipient";
        $subject = "Submitted Job Application";
        $headers = "Content-Type: text/html;charset=utf-8 \r\n";
        $headers .= "From: " . strip_tags($this->getEmail()) . "\r\n";
        $headers .= "Reply-To: " . strip_tags($this->getEmail()) . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html;charset=utf-8 \r\n";
        $msg = "<html><body style='font-family:Arial,sans-serif;'>";
        $msg .= "<h2 style='font-weight:bold;border-bottom:1px dotted #ccc;'>Job Application Submitted</h2>\r\n";
        $msg .= "<p><strong>Applied for:</strong> " . $this->getJobTitle() . "</p>\r\n";
        $msg .= "<p><strong>Job ID:</strong> " . $this->getJobId() . "</p>\r\n";
        $msg .= "<p><strong>Applicant Name:</strong> " . $this->getName() . "</p>\r\n";
        $msg .= "<p><strong>Email:</strong> " . $this->getEmail() . "</p>\r\n";
        $msg .= "<p><strong>Phone:</strong> " . $this->getPhone() . "</p>\r\n";
        $msg .= "<p><strong>Cover Letter:</strong> " . $this->getCover() . "</p>\r\n";
        $msg .= "<a href='http://domain.com/" . $target . "'>Download Resume</a>\r\n";
        $msg .= "</body></html>";
        if (@mail($sendTo, $subject, $msg, $headers)) {
            echo "";
        } else {
            echo "false";
        }
        //Tells you if its all ok
        echo "<div id='confirm-app'>
                    <p>Thank you for submitting your application. 
                         Resumes submitted will be reviewed to determine 
                         qualifications that match our hiring needs.<br />
                         If you are selected you will be contacted by a member of our recruiting team.
                    </p>
                    <br /><a href='../careers/job-postings.php'>Return to current opportunities</a>
                  </div>";
    }
}


//class for handling errors


class ErrorHelper
{

    public function resumerequired()
    {
        if (empty ($resume)) {
            $error['resume'] = "<p class='error'>Resume Required </p>";
        }
    }

    public function jobIdRequired()
    {
        if (isset($_GET['jobid'])) {
            $jobid = $_GET['jobid'];
        }
        if (isset($_GET['jobtitle'])) {
            $jobtitle = $_GET['jobtitle'];
        }
    }

    public function nameRequired()
    {
        if (isset($name)) {
            if (empty ($name)) {
                $error['name'] = "<p class='error'>Required </p>";
            }
        }
    }

    public function emailRequired()
    {
        if (isset($email)) {
            if (empty ($email)) {
                $error['email'] = "<p class='error'>Required </p>";
            }
        }
    }

    public function phoneRequired()
    {
        if (isset($phone)) {
            if (empty ($phone)) {
                $error['phone'] = "<p class='error'>Required </p>";
            }
        }
    }


    public function coverRequired()
    {
        if (isset($cover)) {
            if (empty ($cover)) {
                $error['coverletter'] = "<p class='error'>Required </p>";
            }
        }
    }
}









