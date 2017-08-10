<?php

/**
 * To add new languages simply copy this file, and create a language
 * switch in your root file (i.e., index.php)
 *
 * Note: unencoded characters like ö, é etc could be used as the html5
 * doctype uses utf8 encoding.
 */

// website
define('WEBPAGE_TITLE', 'Java Unit Testing Survey');

// database
define('MESSAGE_DATABASE_ERROR', 'Database connection problem');

// home
define('HOME_TITLE', 'Java Unit Testing Survey');
define('HOME_SUB_TITLE', 'Please insert your \'Prolific Academic ID\' and click on \'Start »\' button to begin the survey.');
define('USER_ID_LABEL', 'Prolific Academic ID');
define('USER_ID_PLACE_HOLDER', 'Write here your Prolific Academic ID (e.g., A3IZSXSSGW80FN)');

// app
define('START', 'Start »');
define('NEXT', 'Next »');
define('PREVIOUS', '« Previous');
define('SUBMIT', 'Submit your data »');
define('DONT_KNOW', "Skip");
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
define('TAGS_BOX_PLACE_HOLDER', 'Drag & drop tags, or type tag names');
define('DONT_KNOW_BOX_PLACE_HOLDER', 'Please explain why you are skipping this question and then select the \'Next\' button.');
define('COMMENTS_BOX_PLACE_HOLDER', 'Use this text area to provide any additional comments.');

// competency
define('COMPETENCY_TITLE', 'Java Competency Test');
define('COMPETENCY_SUB_TITLE', 'Please click on \'Start »\' button to begin the test.');
define('COMPETENCY_SUBMIT_MESSAGE', 'You have finished your test, please submit your answers.');
define('INCOMPLETE_COMPETENCY', 'You have to answer all your questions!');
define('COMPETENCY_RESULT_SUCCESS', '');
define('ALREADY_DONE_COMPETENCY', 'User with ID: \'$user_id\', already have done the competency test.');
define('COMPETENCY_RESULT_FAIL', 'You have failed the \'Java Competency Test\' with a score of $score%.');

// survey
define('SURVEY_TITLE', 'Java Unit Testing Survey');
define('SURVEY_SUB_TITLE', 'Please click on \'Start »\' button to begin the survey.');
define('SURVEY_SUBMIT_MESSAGE', 'You have finished your survey, please submit your answers.');
define('RATE_SURVEY_QUESTION', 'Please rate the following test case by how much you like it: 1 star, you do not like it at all; 5 stars, you like it very much. <br/ > Then, justify your rating by selecting the appropriated tags. <br/ > <br/ > Select the \'Skip\' button below if you are not able to assess how much you like this test case. <br/ > <br/ >');
define('FORCED_CHOICE_SURVEY_QUESTION', 'For the following pair of test cases, please select the one that you like most by selecting \'Test A\' or \'Test B\'. <br/ > Then, justify your choice by selecting the appropriated tags, at least one per test case. <br/ > <br/ > If you are not able to chose one test case, please justify why by selecting the \'Skip\' button below. <br/ > <br/ >');
define('INCOMPLETE_SURVEY', 'Please answer all questions before submitting.');
define('INCOMPLETE_ANSWER', 'Please answer the question before continuing.'); # haven't answered anything at all
define('INCOMPLETE_SURVEY_RATE_MISSING_RATE', 'Your answer is incomplete. Please rate the following test case.');
define('INCOMPLETE_SURVEY_RATE_MISSING_TAGS', 'Your answer is incomplete. Please justify your rating by selecting the most appropriated tags.');
define('INCOMPLETE_SURVEY_FORCED_CHOICE_MISSING_SELECTION', 'Your answer is incomplete. Please select the test case that you like most by selecting \'Test A\' or \'Test B\'.');
define('INCOMPLETE_SURVEY_FORCED_CHOICE_MISSING_TAGS_OF_A', 'Your answer is incomplete. Please justify your choice by selecting the appropriated tags, at least one per test case.');
define('INCOMPLETE_SURVEY_FORCED_CHOICE_MISSING_TAGS_OF_B', 'Your answer is incomplete. Please justify your choice by selecting the appropriated tags, at least one per test case.');
define('ALREADY_DONE_SURVEY', 'User with ID \'$user_id\', already have done this survey.');
define('SURVEY_NOT_AVAILABLE', 'We will be in touch with you soon to let you know whether you are eligible to take part in our unit testing survey.');
define('SURVEY_NOT_AVAILABLE_SCORE', 'You have failed the \'Java Competency Test\' with a score of $score%. We are sorry but without qualification you cannot proceed to take the survey.');

?>
