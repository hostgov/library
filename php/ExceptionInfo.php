<?php
const SIGNUPENUM = array(
  "0"=>"SUCCESS",
  "1"=>"DATABASE CONNECTION ERROR",
  "2"=>"DATABASE EXECUTION ERROR - Check duplicate email failed",
  "3"=>"DUPLICATE Email ERROR",
  "4"=>"EMAIL LENGTH ERROR - Email maximum length is 50",
  "5"=>"EMAIL INVALID ERROR - Please use a valid email address",
  "6"=>"NAME LENGTH ERROR - Firstname and Lastname maximum length is 20",
  "7"=>"NAME INVALID ERROR - Please use a valid name",
  "8"=>"PASSWORD LENGTH ERROR - Password length is between 5-15",
  "9"=>"PASSWORD INVALID ERROR - Please use a valid password",
  "10"=>"MULTIPLE SUBMIT ERROR - Please wait a while and submit again",
  "11"=>"DATABASE EXECUTION ERROR - Insert new user information into database failed",
  "12"=>"REQUEST ERROR",
  "13"=>"EMPTY NAME ERROR - Please input valid name",
  "14"=>"EMPTY EMAIL ERROR - Please input valid email",
  "15"=>"EMPTY PASSWORD ERROR - Please input valid password"
);

const ADDNEWBOOK = array(
    "0"=>"SUCCESS",
    "1"=>"UPLOADING IMAGE ERROR - Please try again later",
    "2"=>"UPLOADING FILE TYPE ERROR - Please upload valid image",
    "3"=>"IMAGE TYPE ERROR - Please upload valid image type",
    "4"=>"IMAGE SIZE ERROR - Image size should be smaller than ".(IMAGE_UPLOAD_MAX_SIZE/1024)."kb",
    "5"=>"SAVE IMAGE ON SERVER ERROR",
    "6"=>"BOOK DETAILS ERROR",
    "7"=>"BOOK LANGUAGE ERROR",
    "8"=>"BOOK CATEGORY ERROR",
    "9"=>"DATABASE CONNECTION ERROR",
    "10"=>"DATABASE EXECUTION ERROR - Insert new book information into database failed",
    "11"=>"MULTIPLE SUBMIT ERROR - Please wait a while and submit again",
    "12"=>"DATABASE EXECUTION ERROR , ROLLBACK SUCCESS",
    "13"=>"DATABASE EXECUTION ERROR , ROLLBACK FAILED - Please check database"
);

const GETBOOKSDATA = array(
    "0"=>"SUCCESS",
    "1"=>"DATABASE CONNECTION ERROR",
    "2"=>"DATABASE EXECUTION ERROR - Check duplicate email failed",
    "3"=>"SEARCH PARAMS ERROR",
    "4"=>"SEARCH PARAMS ERROR",
    "5"=>"DATABASE EXECUTION ERROR"
);
const LOGIN = array(
    "0"=>"SUCCESS",
    "1"=>"email or password combine does not exit in database"
);
