<?php
//Filename:  judgeTESTgradeDecember2012.php
//Author:  S. Weick
//Date:  December 2012
//Grades Judges test 
//current input file: 2013Answers.txt
//called from: JudgeTEST2013.shtml

//create constant for input file
define('ANSWERS_FILE', 'JudgeTEST2013Answers.txt');

//check if form was submitted
if (isset($_POST['Submit']))
{	//include functions file
	require_once("JudgeTESTphpFunctions2013.php");

	//set up new html page 
	htmlHeader();

	$today = date("U");
	$count = 0; //used to increment email file
	
	//open file to be emailed by goDaddy
	//this file name was specifically named in 'gdform.php'
	$emailFile = $_SERVER['DOCUMENT_ROOT'] . "/../data/gdform_" . $today; 
	//$emailFile = "gdform_" . $today . ".xml";  //use this file to test on localhost
	$fp = fopen($emailFile,"w");
 
	//get user's information & write it to email file
	$name = htmlentities($_POST['FullName']) ;
			
	$count++;
	$emailLtr = emailLetter($count);
	fwrite($fp,"<GDFORM_VARIABLE NAME={$emailLtr}.Name START>\n");
	fwrite($fp, $name . "\n");
	fwrite($fp,"<GDFORM_VARIABLE NAME={$emailLtr}.Name END>\n");

	$count++;
	$emailLtr = emailLetter($count);
	$mbrNum = htmlentities($_POST['HRC_Member_Number']);
	fwrite($fp,"<GDFORM_VARIABLE NAME={$emailLtr}.HRC_Member_Number START>\n");
	fwrite($fp, $mbrNum . "\n");
	fwrite($fp,"<GDFORM_VARIABLE NAME={$emailLtr}.HRC_Member_Number END>\n");

	$count++;
	$emailLtr = emailLetter($count);
	$address1 = htmlentities($_POST['StreetAddress']) . ",  " . 
				htmlentities($_POST['Address2']);
	fwrite($fp,"<GDFORM_VARIABLE NAME={$emailLtr}.Street_Address START>\n");
	fwrite($fp, $address1 . "\n");
	fwrite($fp,"<GDFORM_VARIABLE NAME={$emailLtr}.Street_Address END>\n");

	$count++;
	$emailLtr = emailLetter($count);
	$address2 = htmlentities($_POST['City']) . ",  " . 
				htmlentities($_POST['State']) .  "  "  . 
				htmlentities($_POST['ZipCode']) . "  " .
				htmlentities($_POST['Country']);
	fwrite($fp,"<GDFORM_VARIABLE NAME={$emailLtr}.Address_Continued START>\n");
	fwrite($fp, $address2 . "\n");
	fwrite($fp,"<GDFORM_VARIABLE NAME={$emailLtr}.Address_Continued END>\n");
			
	$count++;
	$emailLtr = emailLetter($count);
	$homeClub = htmlentities($_POST['HomeClub']);
	fwrite($fp,"<GDFORM_VARIABLE NAME={$emailLtr}.HomeClub START>\n");
	fwrite($fp, $homeClub . "\n");
	fwrite($fp,"<GDFORM_VARIABLE NAME={$emailLtr}.HomeClub END>\n");

	$count++;
	$emailLtr = emailLetter($count);
	$phone = htmlentities($_POST['HomePhone']);
	fwrite($fp,"<GDFORM_VARIABLE NAME={$emailLtr}.HomePhone START>\n");
	fwrite($fp, $phone . "\n");
	fwrite($fp,"<GDFORM_VARIABLE NAME={$emailLtr}.HomePhone END>\n");

	$count++;
	$emailLtr = emailLetter($count);
	$email = htmlentities($_POST['Email']);
	fwrite($fp,"<GDFORM_VARIABLE NAME={$emailLtr}.Email START>\n");
	fwrite($fp, $email . "\n");
	fwrite($fp,"<GDFORM_VARIABLE NAME={$emailLtr}.Email END>\n");	
	
	$count++;
	$emailLtr = emailLetter($count);
	fwrite($fp,"<GDFORM_VARIABLE NAME={$emailLtr}.Date_Test_Taken START>\n");
	fwrite($fp, date("F j, Y") . "\n");
	fwrite($fp,"<GDFORM_VARIABLE NAME={$emailLtr}.Date_Test_Taken END>\n");
	
	//read answer file into array until eof
	$answersHandle = fopen(ANSWERS_FILE, 'r');//read only
	//if file opened w/o errors, read all answers into an array
	if ($answersHandle)
	{	$answers = array();
		$i = 0;
        $answers[$i] = fgets($answersHandle);
        while(!feof($answersHandle))
        {   $i++;
            $answers[$i] = fgets($answersHandle);
        }       
        fclose($answersHandle);
    }
	else //error opening answers file 
	{	//email HRC rep: add error stmt to email file
		fwrite($fp,"<GDFORM_VARIABLE NAME=**ERROR** START>\n");
		fwrite($fp,"Error: 'gradeTestDecember2012.php' could not open answers file.\n");
		fwrite($fp,"<GDFORM_VARIABLE NAME=**ERROR** END>\n");
					
		//tell user that this webpage is not working
		//provide link back to main page
		echo '<p class="style1">Error:  this page is not working.</p><br />';
		echo '<p class="style1">HRC Representative has been notified</p><br />';
        echo '<p><a href="http://huntingretrieverclub.org">Return to Main Page</a></p>';
        echo '</body></html>';
        //write user's attempt to log file
		logUser($name, $mbrNum, $email, 0, "Error opening answers file");
        exit;
    }// end else for error opening answers file

	//count number of correct answers
	$numCorrect = 0;
	//keep track of wrong answers
	$wrongAnswers = array();
	$questionNumber = 1;  //first 'select' name on form
	foreach ($answers as $correctAnswer)
	{	//get user's answer from form
		$usersAnswer = $_POST[$questionNumber];
		//update numbering for email file
		$count++;
		$emailLtr = emailLetter($count);
		//write user's answer to email file
		fwrite($fp,"<GDFORM_VARIABLE NAME={$emailLtr}.User_Answer_For_Q{$questionNumber} START>\n");
		fwrite($fp, $usersAnswer . "\n");
		fwrite($fp,"<GDFORM_VARIABLE NAME={$emailLtr}.User_Answer_For_Q{$questionNumber} END>\n");
        if (trim($usersAnswer) == trim($correctAnswer))
		{   $numCorrect++;  }
		else
		{	$wrongAnswers[] = $questionNumber;  }
		//increment to next user's answer
		$questionNumber++;
    }
	
	//add user's test score to email file to be sent to HRC Representative
	//update numbering for email file
	$count++;
	$emailLtr = emailLetter($count);
	fwrite($fp,"<GDFORM_VARIABLE NAME={$emailLtr}.User's_FINAL_TEST_SCORE START>\n");
	fwrite($fp, $numCorrect . "\n");
	fwrite($fp,"<GDFORM_VARIABLE NAME={$emailLtr}.User's_FINAL_TEST_SCORE END>\n");
	
	//show results to user & tell HRC rep if user passed the test
    echo '<h2><span style="#008000"><b>Results:</b></span></h2>';
	$count++;
	$emailLtr = emailLetter($count);
	fwrite($fp,"<GDFORM_VARIABLE NAME={$emailLtr}.Result START>\n");
	if ($numCorrect > 14) //15 out of 20 is passing
    {   echo "<h2>Congratulations {$name}, you passed the test! </h2>";  
		fwrite($fp, "PASS\n");
		//write user to log file
		logUser($name, $mbrNum, $email, "PASS", "no errors in processing");
	}
    else
    {   echo "<h3>At this time, {$name} did not pass this Judge's test.</h3>";  
		fwrite($fp, "FAIL\n");
		//write user to log file
		logUser($name, $mbrNum, $email, "FAIL", "no errors in processing");
	}
	fwrite($fp,"<GDFORM_VARIABLE NAME={$emailLtr}.Result END>\n");
	
    echo "<h3>Your score is {$numCorrect} (out of 20)";
    echo " and it has been emailed to an HRC Representative.</h3>"; 
 	//for those who passed, let them know which ones they missed
	$numWrong = count($wrongAnswers);
	if ($numCorrect > 14 && $numWrong > 0 ) 
	{	echo "<h4>You missed the following question(s):  ";
		for ($i = $numWrong;  $i > 0;  $i--)
		{	echo "{$wrongAnswers[$numWrong - $i]}";
			if ($i > 1)
			{	echo ", ";  }
		}
		echo "</h4>";
	}
			
    echo '<p><a href="http://huntingretrieverclub.org">
        Return to HRC Home Page</a><br /><br />
		<a href="javascript:window.print()">Click here to Print This Page</a></p>';
    //echo '</body></html>';
	htmlFooter();
	
	//close email file
	fclose($fp);
}//end if statement for submit button
?>