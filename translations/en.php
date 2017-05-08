<?php

/**
 * To add new languages simply copy this file, and create a language
 * switch in your root file (i.e., index.php)
 *
 * Note: unencoded characters like ö, é etc could be used as the html5
 * doctype uses utf8 encoding.
 */

// website
define('WEBPAGE_TITLE', 'Java Unit Testing Study');

// database
define('MESSAGE_DATABASE_ERROR', 'Database connection problem');

// home
define('HOME_TITLE', 'Java Unit Testing Study');
define('HOME_SUB_TITLE', 'Please insert your \'Prolific Academic ID\' and click on \'Start »\' button to begin the survey.');
define('USER_ID_LABEL', 'Prolific Academic ID');
define('USER_ID_PLACE_HOLDER', 'Write here your Prolific Academic ID (e.g., A3IZSXSSGW80FN)');

// app
define('START', 'Start »');
define('NEXT', 'Next »');
define('PREVIOUS', '« Previous');
define('SUBMIT', 'Submit your data »');
define('MESSAGE_ERROR', 'Error:');
define('MESSAGE_SUCCESS', '');
define('PROGRESS_BAR_INFO', 'Complete');
define('THANKS_MESSAGE', 'Thank you very much for your time!');
define('TOKEN_MESSAGE', 'To claim your payment on \'Prolific Academic\', please copy the following token:');

// snippets
define('SNIPPET', 'Snippet');
define('TEST', 'Test');
define('TEST_CASE', 'Test Case');
define('TEST_A', 'Test A');
define('TEST_B', 'Test B');
define('TEST_CASE_A', 'Test Case A');
define('TEST_CASE_B', 'Test Case B');
define('SOURCE', 'Source Code');

define('EXPLORER', 'Source Explorer');
define('PASS', 'Pass');
define('FAIL', 'Fail');
define('TAGS', 'Tags');
define('TAGS_BOX_PLACE_HOLDER', 'Drag & Drop tags, or type tag names');
define('JUSTIFICATION_BOX_PLACE_HOLDER', 'Please justify your choice of rating and tags');

// competency
define('COMPETENCY_TITLE', 'Java Competency Test');
define('COMPETENCY_SUB_TITLE', 'Please click on \'Start »\' button to begin the test.');
define('COMPETENCY_SUBMIT_MESSAGE', 'You have finished your test, please submit your answers.');
define('INCOMPLETE_COMPETENCY', 'You have to answer all your questions!');
define('COMPETENCY_RESULT_SUCESS', '');
define('ALREADY_DONE_COMPETENCY', 'User with ID: \'$user_id\', already have done the competency test.');
define('COMPETENCY_RESULT_FAIL', 'You have failed the \'Java Competency Test\' with a score of $score%.');

// survey (study)
define('SURVEY_TITLE', 'Java Unit Testing Survey');
define('SURVEY_SUB_TITLE', 'Please click on \'Start »\' button to begin the survey.');
define('SURVEY_SUBMIT_MESSAGE', 'You have finished your survey, please submit your answers.');
define('INCOMPLETE_STUDY', 'Please answer all questions before submitting.');
define('ALREADY_DONE_STUDY', 'User with ID \'$user_id\', already have done this study.');
define('STUDY_NOT_AVAILABLE', 'We will be in touch with you soon to let you know whether you are eligible to take part in our unit testing study.');
define('STUDY_NOT_AVAILABLE_SCORE', 'You have failed the \'Java Competency Test\' with a score of $score%. We are sorry but without qualification you cannot proceed to take the survey.');

?>
