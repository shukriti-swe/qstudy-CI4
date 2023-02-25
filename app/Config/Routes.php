<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
//$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index',['filter' => 'homeAuth']);
$routes->add('signup','RegisterController::showSignUp');

//login
$routes->add('loginChk','Login::loginChk');
$routes->add('logout','LogoutController::logout');

//countMessage---------
$routes->add('countMessage','CommonAccessController::countMessage');

//forgot password section
$routes->add('forgot_password','Login::forgotPassView');
$routes->add('emailCheck','Login::emailCheck');
$routes->add('phoneCheck','Login::phoneCheck');
$routes->add('passwdCheck','Login::passwdCheck');
$routes->add('pass_reset_link','Login::sendResetPassEmail');
$routes->add('reset_pass/(:any)', 'Login::resetPass/$1');

//Free trail---------------
$routes->add('select_country/(:any)/(:any)','RegisterController::selectCountry/$1/$2');
$routes->add('select_course','RegisterController::selectCourse');
$routes->add('trial','RegisterController::showSignUp');
$routes->add('sure_data_save','RegisterController::sure_data_save');
$routes->add('redirect_url','RegisterController::redirect_url');

//paypal------------
$routes->add('paypal_notify','PaypalController::paypal_notify');
$routes->add('no_debit_paypal_notify','PaypalController::no_debit_paypal_notify');

//FAQ
$routes->add('contact_us','CommonAccessController::contactUs');
$routes->add('faq/view/(:any)','CommonAccessController::viewFaq/$1');
$routes->add('faq-view-other/(:any)','CommonAccessController::viewLandPageItem/$1');
$routes->add('faq/view-other/(:any)','CommonAccessController::viewLandPageItem/$1');

//other
$routes->add('video','Home::vdHowItWorks');
$routes->add('tutor/search','CommonAccessController::searchTutor');
$routes->add('tutor/profile/(:any)','CommonAccessController::showTutorProfile/$1');

$routes->add('q-dictionary/search','CommonAccessController::searchDictionaryWord');

$routes->group('',['filter' => 'auth'], function ($routes) {
//Admin------------------------------
$routes->add('admin','AdminController::index');

//Student---------------
$routes->add('student','StudentController::index');
$routes->add('student/organization','StudentController::organization');
$routes->add('student/studyType/(:any)','StudentController::studyType/$1');
//$routes->add('st_answer_matching_without_form_workout_two','StudentController::st_answer_matching_without_form_workout_two');
$routes->add('all_tutors_by_type/(:any)/(:any)','StudentController::all_tutors_by_type/$1/$2');
$routes->add('all_tutors_by_type/(:any)/(:any)/(:any)','StudentController::all_tutors_by_type/$1/$2/$3');
$routes->add('studentsModuleByQStudyNew','StudentController::studentsModuleByQStudyNew');
$routes->add('studentsModuleByQStudy','StudentController::studentsModuleByQStudy');
$routes->add('get_permission','StudentController::get_permission');
$routes->add('get_tutor_tutorial_module/(:any)/(:any)','StudentController::get_tutor_tutorial_module/$1/$2');
$routes->add('renderedChapters/(:any)','StudentController::renderedChapters/$1');
$routes->add('assign_subject_by_course_student','ModuleController::assign_subject_by_course_student');
$routes->add('preview_memorization_pattern_one_try','StudentController::preview_memorization_pattern_one_try');

//upper_level student----------------------------
$routes->add('upper_level','UpperLevelController::index');
$routes->add('student/view_course','UpperLevelController::view_course');


//dashboard Load------------------
$routes->add('dashboard','Dashboard::index');

//qstudy----------------------
$routes->add('qstudy','QstudyController::index');

//q-dictionary
$routes->add('q-dictionary/search','CommonAccessController::searchDictionaryWord');

//school--------------------
$routes->add('school','SchoolController::index');
$routes->add('school_setting','SchoolController::school_setting');
$routes->add('school_info_details','SchoolController::school_info_details');
$routes->add('update_school_details','SchoolController::update_school_details');
$routes->add('school_logo','SchoolController::school_logo');
$routes->add('school_logo_upload','SchoolController::school_logo_upload');
$routes->add('school_form','RegisterController::school_form');
$routes->add('save_school','RegisterController::save_school');
$routes->add('sure_school_data_save','RegisterController::sure_school_data_save');
	

//student----------------------
$routes->add('student_form','RegisterController::student_form');
$routes->add('save_student','RegisterController::save_student');

//upper_level_student-----------
$routes->add('upper_level_student_form','RegisterController::upper_level_student_form');
$routes->add('save_upper_student','RegisterController::save_upper_student');
$routes->add('sure_upper_student_data_save','RegisterController::sure_upper_student_data_save');



//tutor--------------------------------
$routes->add('tutor/studyType','TutorController::studyType');
$routes->add('tutor-progress-type','TutorController::tutor_progress_type');
$routes->add('tutor_student_progress','StudentProgress::viewTutorStudentProgress');
$routes->add('TutorStProgTableDataStd','StudentProgress::TutorStProgTableDataStd');
$routes->add('tutor/view_course','TutorController::view_course');
$routes->add('tutor','TutorController::index');
$routes->add('tutor_form','RegisterController::tutor_form');
$routes->add('save_tutor','RegisterController::save_tutor');
$routes->add('sure_save_tutor','RegisterController::sure_save_tutor');
$routes->add('student_progress','StudentProgress::viewStudentProgress');
$routes->add('student_progress/(:any)','StudentProgress::viewStudentProgress/$1');
$routes->add('Student_Progress/studentByClass','StudentProgress::studentByClass');
$routes->add('student_progress_course/(:any)','StudentProgress::student_progress_course/$1');
$routes->add('StProgTableDataStd','StudentProgress::StProgTableDataStd');
$routes->add('addMarks','StudentProgress::addMarks');
$routes->add('delete_progress','StudentProgress::delete_progress');
$routes->add('check_student_copy/(:any)/(:any)/(:any)/(:any)','StudentCopyController::check_student_copy/$1/$2/$3/$4');
$routes->add('module/tutor_list/(:any)','ModuleController::tutorList/$1');
$routes->add('assign-module/(:any)','TutorController::assignModule/$1');
$routes->add('moduleSearchFromReorder','TutorController::moduleSearchFromReorder');
$routes->add('assignModuleStudent','TutorController::assignModuleStudent');


// add new by AS--------------
$routes->add('tutor_bank_details','TutorController::tutor_bank_details');
$routes->add('bank_details_submit_form','TutorController::bank_details_submit_form');

//tutor setting------------------
$routes->add('tutor_setting','TutorController::tutor_setting');
$routes->add('tutor_details','TutorController::tutor_details');
$routes->add('update_tutor_details','TutorController::update_tutor_details');
$routes->add('tutor_upload_photo','TutorController::tutor_upload_photo');
$routes->add('tutor_file-upload','TutorController::tutor_file_upload');

//find tutor,show,update profile
$routes->add('tutor/profile_update','TutorController::updateProfile');

//corporate----------------------
$routes->add('corporate','CorporateController::index');
$routes->add('corporate_setting','CorporateController::corporate_setting');
$routes->add('corporate_info_details','CorporateController::corporate_info_details');
$routes->add('update_corporate_details','CorporateController::update_corporate_details');
$routes->add('corporate_logo','CorporateController::corporate_logo');
$routes->add('corporate_logo_upload','CorporateController::corporate_logo_upload');
$routes->add('corporate_form','RegisterController::corporate_form');
$routes->add('save_corporate','RegisterController::save_corporate');
$routes->add('sure_corporate_data_save','RegisterController::sure_corporate_data_save');

//mail-temlete_tutor
$routes->add('tutor_trial_mail','RegisterController::tutor_trial_mail');

//mail-temlete_school
$routes->add('school_mail','RegisterController::school_mail');

//mail-temlete_parent------------
$routes->add('parent_trial_mail','RegisterController::parent_trial_mail');

//mail-temlete_upper_student
$routes->add('upper_student_trial_mail','RegisterController::upper_student_trial_mail');

//mail-temlete_corporate
$routes->add('corporate_mail','RegisterController::corporate_mail');
	
//after-registration
$routes->add('home_page','RegisterController::home_page');


//parent-setting
$routes->add('parents','ParentsController::index');
$routes->add('cancel_subscription','ParentsController::cancel_subscription');
$routes->add('parent_setting','ParentsController::parent_setting');
$routes->add('my_details','ParentsController::my_details');
$routes->add('upload_photo','ParentsController::upload_photo');
$routes->add('update_my_details','ParentsController::update_my_details');
$routes->add('file-upload','ParentsController::parent_dropzone_file');
$routes->add('parent_password_check','Login::parent_password_check');
$routes->add('both_subscription_cancel','Dashboard::subscription_cancel');
$routes->add('subscription_renew','Dashboard::subscription_renew');

	

/* For Paypal Payment */
$routes->add('paypal','RegisterController::show_paypal_form');
$routes->add('subscription/cancel','PaypalController::cancelSubscription');
$routes->add('paypal_success','PaypalController::paypal_success');
$routes->add('go_paypal','RegisterController::go_paypal');



//student-setting--------------
$routes->add('student_progress_step','StudentController::student_progress_step');
$routes->add('student_setting','StudentController::student_setting');
$routes->add('student_details','StudentController::student_details');
$routes->add('student_progress_step_7','StudentController::student_progress_step_7');
$routes->add('update_student_details','StudentController::update_student_details');
$routes->add('student_upload_photo','StudentController::student_upload_photo');
$routes->add('sure_student_photo_upload','StudentController::sure_student_photo_upload');
$routes->add('my_enrollment','StudentController::my_enrollment');
$routes->add('profile_update','StudentController::profile_update');
$routes->add('add_tutor_like','StudentController::add_tutor_like');
$routes->add('get_ref_link','StudentController::get_ref_link');
$routes->add('save_ref_link','StudentController::save_ref_link');
$routes->add('removeRefLink','StudentController::removeRefLink');
$routes->add('AssignModuleTutuorTutorial','StudentController::AssignModuleTutuorTutorial');
$routes->add('AssignModuleSchoolTutuorTutorial','StudentController::AssignModuleSchoolTutuorTutorial');
$routes->add('q_study_course','StudentController::q_study_course');
$routes->add('tutor_course','StudentController::tutor_course');
	
	
//messaging--------------------
$routes->add('message/type','MessageController::showAllTopics');
$routes->add('message/topics','MessageController::showAllTopics');
$routes->add('message/topics/add','MessageController::addMessageTopic');
$routes->add('message/topics/delete/(:any)','MessageController::DeleteMessageTopic/$1');
$routes->add('message/set','MessageController::setMessage');
$routes->add('show_all_message/(:any)','MessageController::show_all_message/$1');
$routes->add('edit_message/(:any)','MessageController::edit_message/$1');
$routes->add('add_message/(:any)','MessageController::add_message/$1');
//$routes->add('proceed_email','MessageController::proceed_email');
$routes->add('message/delete/(:any)','MessageController::delete_message/$1');
$routes->add('assign-subject','ModuleController::assign_subject');
$routes->add('save_assign_subject','ModuleController::save_assign_subject');
$routes->add('edit_assign_subject','ModuleController::edit_assign_subject');
$routes->add('update_assign_subject','ModuleController::update_assign_subject');
$routes->add('delete_assign_subject','ModuleController::delete_assign_subject');

//Cron Massaging---------------
$routes->add('proceed_email','CronController::proceed_email');
	
// added AS------------------------
$routes->add('direct_deposit','CardController::direct_deposit');

//groupboard------------------------
$routes->add('direct-request','CardController::derect_request');

//************   Module Section  ***********
$routes->add('view-course','Dashboard::view_course');
$routes->add('all-module','ModuleController::all_module');
$routes->add('create-module','ModuleController::createModule');
$routes->add('create-module/(:any)','ModuleController::createModule/$1');
$routes->add('course/country','QstudyController::courseCountrySelect');
$routes->add('view_course','QstudyController::view_course');
$routes->add('save_module_info','ModuleController::save_module_info');
$routes->add('getIndividualStudent','ModuleController::getIndividualStudent');
$routes->add('addNewSubject','ModuleController::addNewSubject');
$routes->add('addNewChapter','ModuleController::addNewChapter');
$routes->add('save_new_module_question','ModuleController::saveNewModuleQuestion');
$routes->add('question-list/(:any)','TutorController::question_list/$1');
$routes->add('question-list/(:any)/(:any)','TutorController::question_list/$1/$2');
$routes->add('question-list/(:any)/(:any)/(:any)','TutorCont```roller::question_list/$1/$2/$3');
$routes->add('delete_new_module/(:any)','ModuleController::deleteNewModule/$1');
$routes->add('module_preview/(:any)/(:any)',"ModuleController::module_preview/$1/$2");
$routes->add('edit-module/(:any)','ModuleController::editModule/$1');
$routes->add('delete_edit_module_question/(:any)/(:any)','ModuleController::deleteEditModuleQuestion/$1/$1');
$routes->add('duplicate_module_question','ModuleController::duplicateModuleQuestion');
$routes->add('assign_serial_to_module','ModuleController::assign_serial_to_module');
$routes->add('module/search','ModuleController::searchModule');
$routes->add('new_module_duplicate',"ModuleController::newModuleDuplicate");



//module question
$routes->add('module_question_delete/(:any)','ModuleController::moduleQuestionDelete/$1');
$routes->add('module_question_duplicate','ModuleController::moduleDuplicateQuestion');
$routes->add('module_question_sorting','ModuleController::moduleQuestionSorting');
$routes->add('edit_module_question_sorting','ModuleController::edit_module_question_sorting');


// Whiteboard section------------- 
$routes->add('whiteboard-items','TutorController::whiteboard_items');
$routes->add('download_question_store/(:any)','StudentController::download_question_store/$1');
$routes->add('std-whiteboard-items','StudentController::whiteboard_items');
$routes->add('std-question-store','StudentController::std_question_store');
$routes->add('get_question_store_data','StudentController::get_question_store_data');
$routes->add('search_question_store','StudentController::search_question_store');
$routes->add('WhiteBoardTutor','TutorController::WhiteBoardTutor');
$routes->add('insertClass','TutorController::insertClass');
$routes->add('yourClassRoomTutor/(:any)','TutorController::yourClassRoom/$1');
$routes->add('tutor-question-store','TutorController::tutor_question_store');
$routes->add('tutor_search_question_store','TutorController::search_question_store');
$routes->add('download_tutor_question_store/(:any)','TutorController::download_tutor_question_store/$1');



//************  Question Section ***********
$routes->add('question-list','TutorController::question_list');
$routes->add('question-store','TutorController::question_store');
$routes->add('save_question_store_data','TutorController::save_question_store_data');
$routes->add('create-question/(:any)','TutorController::create_question/$1');
$routes->add('get_chapter_name','TutorController::get_chapter_name');
$routes->add('save_question_data','TutorController::save_question_data');
$routes->add('imageUpload','CommonAccessController::imageUpload');
$routes->add('get-vocabulary-word-data','TutorController::get_vocabulary_word_data');
$routes->add('question_delete/(:any)','QuestionController::deleteQuestion/$1');
$routes->add('subject/all','SubjectController::showAllSubject');
$routes->add('update-subject-name','SubjectController::update_subject_name');
$routes->add('chapter/delete/(:any)','SubjectController::deleteChapter/$1');
$routes->add('subject/delete/(:any)','SubjectController::deleteSubject/$1');
$routes->add('store_subject_chapter','TutorController::store_subject_chapter');
$routes->add('get_store_subject_amount','TutorController::get_store_subject_amount');
$routes->add('get_store_chapter_name','TutorController::get_store_chapter_name');
$routes->add('get_pdf_serial','TutorController::get_pdf_serial');
$routes->add('search_store_view','TutorController::search_store_view');

$routes->add('comprehension_image_upload','QuestionController::comprehension_image_upload');
$routes->add('glossary_image_upload','QuestionController::glossary_image_upload');
$routes->add('type_one_box_one_image_upload','QuestionController::type_one_box_one_image_upload');
$routes->add('type_one_box_one_hint_image_upload','QuestionController::type_one_box_one_hint_image_upload');
$routes->add('type_three_box_one_image_upload','QuestionController::type_three_box_one_image_upload');
$routes->add('type_three_box_one_image_upload/(:any)','QuestionController::type_three_box_one_image_upload/$1');


//************           Question Edit Section         ***********
$routes->add('question_edit/(:any)/(:any)','TutorController::question_edit/$1/$2');
$routes->add('update_question_data','TutorController::update_question_data');
$routes->add('input_tutor','TutorController::input_tutor');

$routes->add('st_answer_matching','StudentController::st_answer_matching');
$routes->add('st_answer_matching_true_false','StudentController::st_answer_matching_true_false');
$routes->add('st_answer_matching_vocabolary','StudentController::st_answer_matching_vocabolary');
$routes->add('st_answer_matching_multiple_choice','StudentController::st_answer_matching_multiple_choice');
$routes->add('st_answer_skip','StudentController::st_answer_skip');
$routes->add('st_answer_times_table','StudentController::st_answer_times_table');
$routes->add('st_answer_algorithm','StudentController::st_answer_algorithm');
$routes->add('st_answer_matching_without_form_workout_two_new','StudentController::st_answer_matching_without_form_workout_two');
$routes->add('st_answer_matching_workout_two','StudentController::st_answer_matching_workout_two');
$routes->add('stu_preview_memorization_pattern_one_matching','StudentController::stu_preview_memorization_pattern_one_matching');
$routes->add('st_preview_memorization_pattern_one_ans_matching','StudentController::st_preview_memorization_pattern_one_ans_matching');
$routes->add('st_preview_memorization_pattern_one_ok','StudentController::st_preview_memorization_pattern_one_ok');
$routes->add('st_creative_ans_save','StudentController::st_creative_ans_save');
$routes->add('st_answer_sentence_matching','StudentController::st_answer_sentence_matching');
$routes->add('st_answer_word_memorization','StudentController::st_answer_word_memorization');
$routes->add('st_answer_comprehension','StudentController::st_answer_comprehension');
$routes->add('st_answer_grammer','StudentController::st_answer_grammer');
$routes->add('st_answer_glossary','StudentController::st_answer_glossary');
$routes->add('st_answer_image_quiz','StudentController::st_answer_image_quiz');


$routes->add('st_preview_memorization_pattern_one_try','StudentController::st_preview_memorization_pattern_one_try');

$routes->add('st_show_tutorial_result/(:any)','StudentController::st_show_tutorial_result/$1');
$routes->add('finish_all_module_question/(:any)/(:any)','StudentController::finish_all_module_question/$1/$2');


//************      Preview Section     *********** 
$routes->add('question_preview/(:any)/(:any)','PreviewController::question_preview/$1/$2');
$routes->add('get_preview_idea_info','PreviewController::get_preview_idea_info');
$routes->add('preview_answer_matching','PreviewController::answer_matching');
$routes->add('preview_answer_matching_multiple_choice','PreviewController::preview_answer_matching_multiple_choice');
$routes->add('preview_answer_matching_true_false','PreviewController::preview_answer_matching_true_false');
$routes->add('preview_answer_multiple_matching','PreviewController::preview_answer_multiple_matching');
$routes->add('preview_answer_matching_vocabolary','PreviewController::preview_answer_matching_vocabolary');
$routes->add('preview_answer_times_table','PreviewController::preview_answer_times_table');
$routes->add('preview_answer_algorithm','PreviewController::preview_answer_algorithm');
$routes->add('preview_answer_matching_workout_two','PreviewController::preview_answer_matching_workout_two');
$routes->add('tutorial_check_order_module_next','ModuleController::tutorial_check_order_module_next');
$routes->add('tutorial_check_order_module_prev','ModuleController::tutorial_check_order_module_prev');
$routes->add('preview_memorization_pattern_four_ok','PreviewController::preview_memorization_pattern_four_ok');
$routes->add('preview_memorization_pattern_four_matching','PreviewController::preview_memorization_pattern_four_matching');
$routes->add('module_preview_memorization_pattern_four_try','PreviewController::module_preview_memorization_pattern_four_try');
$routes->add('module_preview_memorization_pattern_four_ans_matching','PreviewController::module_preview_memorization_pattern_four_ans_matching');
$routes->add('module_preview_memorization_pattern_one_ok','PreviewController::module_preview_memorization_pattern_one_ok');
$routes->add('module_preview_memorization_pattern_two_try','PreviewController::module_preview_memorization_pattern_two_try');
$routes->add('module_preview_memorization_pattern_one_matching','PreviewController::module_preview_memorization_pattern_one_matching');
$routes->add('preview_memorization_pattern_one_try','PreviewController::preview_memorization_pattern_one_try');
$routes->add('module_preview_memorization_pattern_one_ans_matching','PreviewController::module_preview_memorization_pattern_one_ans_matching');
$routes->add('preview_memorization_p_two_start_memorization','PreviewController::preview_memorization_p_two_start_memorization');
$routes->add('module_preview_memorization_p_two_ans_matching','PreviewController::module_preview_memorization_p_two_ans_matching');
$routes->add('preview_memorization_pattern_two_take_decesion','PreviewController::preview_memorization_pattern_two_take_decesion');
$routes->add('module_preview_memorization_p_three_start_memorization','PreviewController::module_preview_memorization_p_three_start_memorization');
$routes->add('module_preview_memorization_pattern_three_ans_matching','PreviewController::module_preview_memorization_pattern_three_ans_matching');
$routes->add('preview_memorization_pattern_three_take_decesion','PreviewController::preview_memorization_pattern_three_take_decesion');
$routes->add('preview_memorization_pattern_three_try_again','PreviewController::preview_memorization_pattern_three_try_again');
$routes->add('preview_memorization_pattern_three_ok','PreviewController::preview_memorization_pattern_three_ok');
$routes->add('module_creative_quiz_ans_matching','PreviewController::module_creative_quiz_ans_matching');
$routes->add('mudule_answer_sentence_matching','PreviewController::mudule_answer_sentence_matching');
$routes->add('module_answer_word_memorization','PreviewController::module_answer_word_memorization');
$routes->add('module_preview_answer_glossary','PreviewController::module_preview_answer_glossary');
$routes->add('module_answer_multiple_matching','PreviewController::module_answer_multiple_matching');
$routes->add('preview_save_answer_idea','PreviewController::preview_save_answer_idea');

$routes->add('st_answer_skip','PreviewController::st_answer_skip');

$routes->add('show_tutorial_result/(:any)','PreviewController::show_tutorial_result/$1');

// ===== Question Preview =============
$routes->add('question_answer_matching_comprehension','PreviewController::question_answer_matching_comprehension');
$routes->add('question_answer_matching_grammer','PreviewController::question_answer_matching_grammer');
$routes->add('question_answer_matching_glossary','PreviewController::question_answer_matching_glossary');
$routes->add('question_answer_matching_image_quiz','PreviewController::question_answer_matching_image_quiz');

//**********     Module Preview Section   *********
$routes->add('module_answer_matching_comprehension','PreviewController::module_answer_matching_comprehension');
$routes->add('module_answer_matching_grammer','PreviewController::module_answer_matching_grammer');

 

//**********     Admin Section   *********
$routes->add('all_area','AdminController::all_area');
$routes->add('admin/notification','AdminController::notification');
$routes->add('admin/direct_deposit_list','AdminController::direct_deposit_list');
$routes->add('idea_create_student_report','AdminController::idea_create_student_report');
$routes->add('dicItemCreatorToPay','AdminController::dicItemCreatorToPay');


$routes->add('save_idea','QuestionController::save_idea');
$routes->add('get_idea','QuestionController::get_idea');
$routes->add('check_idea_short_question','QuestionController::check_idea_short_question');
$routes->add('search_idea','QuestionController::search_idea');
$routes->add('search_image_idea','QuestionController::search_image_idea');


//User Info---------------
$routes->add('user_list','AdminController::user_list');
$routes->add('edit_user/(:any)','AdminController::edit_user/$1');
$routes->add('user_add','AdminController::userAdd');
$routes->add('suspendUser/(:any)','AdminController::suspendUser/$1');
$routes->add('unsuspendUser/(:any)','AdminController::unsuspendUser/$1');
$routes->add('deleteuser','AdminController::deleteuser');
$routes->add('extendTrialPeriod','AdminController::extendTrialPeriod');
$routes->add('packageNotTaken','AdminController::packageNotTaken');
$routes->add('usersCurrentPackages','AdminController::usersCurrentPackages');
$routes->add('addPackages','AdminController::addPackages');
$routes->add('trail_list','AdminController::trail_list');
$routes->add('signup_users','AdminController::signup_users');
$routes->add('next_trial_list','AdminController::next_trial_list');
$routes->add('next_singup_list','AdminController::next_singup_list');
$routes->add('inactive_users','AdminController::inactive_users');
$routes->add('next_inactive_users_list','AdminController::next_inactive_users_list');
$routes->add('schoolTutorNext','AdminController::schoolTutorNext');
$routes->add('suspend_users','AdminController::suspend_users');
$routes->add('next_suspend_users_list','AdminController::next_suspend_users_list');
$routes->add('guest_users','AdminController::guest_users');
$routes->add('parent_users','AdminController::parent_users');
$routes->add('student_users','AdminController::student_users');
$routes->add('upper_level_users','AdminController::upper_level_users');
$routes->add('tutor_users','AdminController::tutor_users');
$routes->add('corporateList','AdminController::corporateList');
$routes->add('schoolList','AdminController::schoolList');
$routes->add('student_prize_list','AdminController::student_prize_list');
$routes->add('next_deposit_users_list','AdminController::next_deposit_users_list');
$routes->add('depositeResources','AdminController::depositeResources');
$routes->add('groupboardResources','AdminController::groupboardResources');
$routes->add('groupboardTrialList','AdminController::groupboardTrialList');
$routes->add('groupboardSignup','AdminController::groupboardSignup');
$routes->add('tutorCommisionLists','AdminController::tutorCommisionLists');
$routes->add('vocabularyCommisionLists','AdminController::vocabularyCommisionLists');
$routes->add('ninteyPercentageMark','AdminController::ninteyPercentageMark');
$routes->add('userEmailList','AdminController::userEmailList');
$routes->add('creativeUserList','AdminController::creativeUserList');
$routes->add('new_idea_create_student','AdminController::new_idea_create_student');
$routes->add('new_idea_create_tutor','AdminController::new_idea_create_tutor');
$routes->add('country_users_list/(:any)','AdminController::country_users_list/$1');
$routes->add('next_aus_users_list','AdminController::next_aus_users_list');
$routes->add('idea_create_tutor_list/(:any)','AdminController::idea_create_tutor_list/$1');
$routes->add('idea_create_tutor_setting/(:any)/(:any)','AdminController::idea_create_tutor_setting/$1/$2');

//Country Info-------------
$routes->add('country_list','AdminController::country_list');
$routes->add('coursesByCountry/(:any)','AdminController::coursesByCountry/$1');
$routes->add('save_country','AdminController::save_country');
$routes->add('update_country','AdminController::update_country');
$routes->add('delete_country/(:any)','AdminController::delete_country/$1');


//Country Wise Course----------
$routes->add('country_wise','AdminController::country_wise');
$routes->add('course_schedule/(:any)','AdminController::course_schedule/$1');
$routes->add('directDepositSetting/(:any)','AdminController::directDepositSetting/$1');
$routes->add('add_course_schedule','AdminController::add_course_schedule');
$routes->add('save_course_schedule','AdminController::save_course_schedule');
$routes->add('duplicateCountry','AdminController::duplicateCountry');
$routes->add('duplicateGrade','AdminController::duplicateGrade');
$routes->add('emailBankDetails','AdminController::emailBankDetails');
$routes->add('inboxBankDetails','AdminController::inboxBankDetails');

//product list---------
$routes->add('product_list','AdminController::product_list');
$routes->add('add_product','AdminController::product_add');
$routes->add('edit_product/(:any)','AdminController::edit_product/$1');
$routes->add('edit_product_submit','AdminController::edit_product_submit');
$routes->add('add_product_submit','AdminController::add_product_submit');
$routes->add('delete_product/(:any)','AdminController::delete_product/$1');
$routes->add('product_point_admin','AdminController::product_point_admin');
$routes->add('update_product_point','AdminController::update_product_point');

//Admin Email-------------
$routes->add('contact-mail','AdminController::contact_mail');
$routes->add('deleteContactMessage','AdminController::deleteContactMessage');
$routes->add('contact-info','AdminController::contact_info');


//q-dictionary---------------
$routes->add('q-dictionary/wordlist','AdminController::dictionaryWordList');
$routes->add('q-dictionary/payment','AdminController::dictionaryPayment');
$routes->add('wordForDataTable','AdminController::wordForDataTable');
$routes->add('wordApprove/(:any)','AdminController::wordApprove/$1');
$routes->add('wordReject/(:any)','AdminController::wordReject/$1');
$routes->add('payTutor','AdminController::payTutor');


//faqs---------------
$routes->add('faq/add','FaqController::addFaq');
$routes->add('faq/all','FaqController::allFaq');
$routes->add('faq/edit/(:any)','FaqController::editFaq/$1');
$routes->add('faq/delete/(:any)','FaqController::deleteFaq/$1');
$routes->add('faq/serialize/(:any)/(:any)/(:any)','FaqController::serialize/$1/$2/$3');
$routes->add('faq/add/other','FaqController::addLandPageItems');
$routes->add('getItemInfo','FaqController::getItemInfo');
$routes->add('faq/video/upload','FaqController::landPagevideoUpload');
$routes->add('faq/video','FaqController::video');
$routes->add('faq/videoEdit/(:any)','FaqController::videoEdit/$1');
$routes->add('faq/videoupdate','FaqController::videoupdate');
$routes->add('faq/video/list','FaqController::landPagevideoList');
$routes->add('removeFile/(:any)/(:any)','FaqController::removeFile/$1/$2');
$routes->add('deleteVideo/(:any)','FaqController::deleteVideo/$1');
$routes->add('ShowVideo/(:any)','FaqController::ShowVideo/$1');

// route-------------------
$routes->add('student_report/(:any)/(:any)/(:any)/(:any)','StudentController::student_progress_report/$1/$2/$3/$4');

//find tutor----------------

$routes->add('sms_api/add','AdminController::smsApiAdd');
$routes->add('sms_message/add','AdminController::sms_message');




$routes->add('sms_templetes','AdminController::sms_templetes');
$routes->add('edit-templete/(:any)','AdminController::edit_templete/$1');
$routes->add('sms_templetes_status','AdminController::sms_templetes_status');
$routes->add('trial_period','AdminController::trial_period');

//groupboard------------
$routes->add('add-groupboard','AdminController::add_groupboard');
$routes->add('store-groupboard','AdminController::store_groupboard');
$routes->add('all-groupboard','AdminController::all_groupboard');
$routes->add('edit-groupboard/(:any)','AdminController::edit_groupboard/$1');
$routes->add('deleteGroupboard/(:any)','AdminController::deleteGroupboard/$1');
$routes->add('update-groupboard','AdminController::update_groupboard');
$routes->add('assignGroupBoard/(:any)','AdminController::assignGroupBoard/$1');
$routes->add('storeGroupBoard','AdminController::storeGroupBoard');


//Payment Setting--------------
$routes->add('qStudyStripeSetting','AdminController::qStudyStripeSetting');
$routes->add('stripeDetailsUpdate','AdminController::stripeDetailsUpdate');
$routes->add('qStudyPaypalSetting','AdminController::qStudyPaypalSetting');
$routes->add('paypalDetailsUpdate','AdminController::paypalDetailsUpdate');
$routes->add('payment_log','AdminController::payment_log');
	
//dialogue
$routes->add('dialogue/add','AdminController::addDialogue');
$routes->add('dialogue/all','AdminController::allDialogue');
$routes->add('dialogue/delete/(:any)','AdminController::deleteDialogue/$1');
$routes->add('add-auto-repeat','AdminController::add_auto_repeat');

//Qstudy Password----------------
$routes->add('qStudyPassword','AdminController::qStudyPassword');
$routes->add('qStudyPassword_update','AdminController::qStudyPassword_update');

// Delete question 
$routes->add('qstudyPassword/(:any)','TutorController::qstudyPassword/$1');
$routes->add('question_duplicate','QuestionController::question_duplicate');

//faqs-------------------------

$routes->add('feedbackfileUpload','CommonAccessController::feedbackfileUpload');
$routes->add('send_feedback','CommonAccessController::send_feedback');




//**********************  Answer Macthnig ***************************/
$routes->add('answer_matching','AnswerMatchingController::answer_matching');
$routes->add('answer_matching_true_false','AnswerMatchingController::answer_matching_true_false');
$routes->add('answer_matching_vocabolary','AnswerMatchingController::answer_matching_vocabolary');
$routes->add('answer_matching_multiple_choice','AnswerMatchingController::answer_matching_multiple_choice');
$routes->add('check_Skip_boxAnswer','AnswerMatchingController::check_Skip_boxAnswer');
$routes->add('answer_times_table','AnswerMatchingController::answer_times_table');
$routes->add('answer_algorithm','AnswerMatchingController::answer_algorithm');
$routes->add('st_answer_matching_without_form_workout_two','AnswerMatchingController::st_answer_matching_without_form_workout_two');
$routes->add('answer_matching_workout_two','AnswerMatchingController::answer_matching_workout_two');
$routes->add('preview_memorization_pattern_one_matching','AnswerMatchingController::preview_memorization_pattern_one_matching');
$routes->add('preview_memorization_pattern_one_ans_matching','AnswerMatchingController::preview_memorization_pattern_one_ans_matching');
$routes->add('preview_memorization_pattern_one_ok','AnswerMatchingController::preview_memorization_pattern_one_ok');
$routes->add('preview_memorization_pattern_two_matching','AnswerMatchingController::preview_memorization_pattern_two_matching');
$routes->add('preview_memorization_pattern_two_ans_matching','AnswerMatchingController::preview_memorization_pattern_two_ans_matching');
$routes->add('preview_memorization_pattern_two_try','AnswerMatchingController::preview_memorization_pattern_two_try');
$routes->add('preview_memorization_pattern_four_try','AnswerMatchingController::preview_memorization_pattern_four_try');
$routes->add('preview_memorization_pattern_four_ans_matching','AnswerMatchingController::preview_memorization_pattern_four_ans_matching');
$routes->add('preview_memorization_p_two_start_memorization','AnswerMatchingController::preview_memorization_p_two_start_memorization');
$routes->add('preview_memorization_p_two_ans_matching','AnswerMatchingController::preview_memorization_p_two_ans_matching');
$routes->add('preview_memorization_pattern_two_try_again','AnswerMatchingController::preview_memorization_pattern_two_try_again');
$routes->add('preview_memorization_p_three_start_memorization','AnswerMatchingController::preview_memorization_p_three_start_memorization');
$routes->add('preview_memorization_pattern_three_ans_matching','AnswerMatchingController::preview_memorization_pattern_three_ans_matching');



//************************ Module *************************
$routes->add('get_draw_image','ModuleController::get_draw_image');
$routes->add('new-edit-module/(:any)','ModuleController::newEditModule/$1');
$routes->add('editNewSubject','ModuleController::editNewSubject');
$routes->add('edit_module_info','ModuleController::edit_module_info');
$routes->add('deleteSubjectByModule','ModuleController::deleteSubjectByModule');
$routes->add('deleteChapterByModule','ModuleController::deleteChapterByModule');
$routes->add('editNewChapter','ModuleController::editNewChapter');
$routes->add('update_new_module_question','ModuleController::updateNewModuleQuestion');

$routes->add('update_serial_module_question','ModuleController::updateSerialModuleQuestion');
$routes->add('searchModuleByOptions','ModuleController::searchModuleByOptions');



/* for card payment*/
$routes->add('card_form_submit','CardController::card_form_submit');

$routes->add('html_text_to_array','QuestionController::html_text_to_array');
});
/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
