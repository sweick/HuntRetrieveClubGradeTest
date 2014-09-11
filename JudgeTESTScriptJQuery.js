//Filename:  JudgeTESTScriptJQuery.js
//Author:  Susan Weick
//Date:  December 2012
//Validates (loosely) the form fields
//1.  makes sure required fields are not empty
//2.  makes sure both email fields are identical
//Uses jQuery .ready function;  javascript for the remaining code

$(document).ready(function(){
	$("#frmContact").submit(validateFields);
}); //end $(document).ready

//handle the form submit event
function validateFields()
{   var inputTags = document.getElementsByTagName("input");
    var emptyTags = false;
    //make sure all the required input tags have text entered
    for (var i = 0; i < inputTags.length; i++)
    {   if (inputTags[i].className == "reqd")
        {   if (inputTags[i].value == "")
            {   emptyTags = true;}
            if (inputTags[i].name == "Email")
            {   var email1 = inputTags[i].value;}
            if (inputTags[i].name == "Email2")
            {   var email2 = inputTags[i].value;}      
        }
    }
    if (emptyTags)
    {	alert("Please fill in required (*) fields"); 
        return false;
    }
    //make sure both email fields match
    if  (email1 != email2)
    {	alert ("Email fields must be the same");
        return false;
    }
    return true;
}
