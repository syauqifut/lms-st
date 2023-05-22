<?php

use Illuminate\Routing\RouteGroup;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Auth::routes();
// Auth

//show all routes
Route::get('routes', function () {
    $routeCollection = Route::getRoutes();

    echo "<table style='width:100%'>";
    echo "<tr>";
    echo "<td width='10%'><h4>HTTP Method</h4></td>";
    echo "<td width='10%'><h4>Route</h4></td>";
    echo "<td width='10%'><h4>Name</h4></td>";
    echo "<td width='70%'><h4>Corresponding Action</h4></td>";
    echo "</tr>";
    foreach ($routeCollection as $value) {
        echo "<tr>";
        echo "<td>" . $value->methods()[0] . "</td>";
        echo "<td>" . $value->uri() . "</td>";
        echo "<td>" . $value->getName() . "</td>";
        echo "<td>" . $value->getActionName() . "</td>";
        echo "</tr>";
    }
    echo "</table>";
});


Route::get('kodepos')->name('kodepos')->uses('UsersController@kodepos');
Route::get('login')->name('login')->uses('Auth\LoginController@showLoginForm')->middleware('guest');
Route::post('login')->name('login.attempt')->uses('Auth\LoginController@login')->middleware('guest');
Route::post('logout')->name('logout')->uses('Auth\LoginController@logout');

Route::get('reset-password', 'Auth\PasswordController@showLinkRequestForm')->name('password.request_link');
Route::post('reset-password', 'Auth\PasswordController@resetPassword')->name('password.update_password');
Route::post('reset-password/send-email', 'Auth\PasswordController@sendResetLinkEmail')->name('password.send_email');
Route::get('reset-password/{token}', 'Auth\PasswordController@showResetForm')->name('password.reset_form');


Route::middleware(['auth', 'privillege'])->group(function () {
    // Access
    //  Route::get('access')->name('access')->uses('AccessController@index')->middleware('remember', 'auth');  //default
    Route::get('access/list')->name('access.list')->uses('AccessController@list')->middleware('remember', 'auth');
    Route::get('access/create')->name('access.create')->uses('AccessController@create')->middleware('auth');
    Route::post('access')->name('access.store')->uses('AccessController@store')->middleware('auth');
    Route::get('access/{access}/edit')->name('access.edit')->uses('AccessController@edit')->middleware('auth');
    Route::put('access/{access}')->name('access.update')->uses('AccessController@update')->middleware('auth');
    Route::delete('access/{access}')->name('access.destroy')->uses('AccessController@destroy')->middleware('auth');
    // Route::put('access/{access}/restore')->name('access.restore')->uses('AccessController@restore')->middleware('auth');
    Route::get('access/{access}/user')->name('access.user')->uses('AccessController@user')->middleware('auth');
    Route::post('access/user')->name('access.storeuser')->uses('AccessController@storeUser')->middleware('auth');
    Route::delete('access/user/{useraccess}')->name('access.destroyuser')->uses('AccessController@destroyUser')->middleware('auth');

    // Dashboard
    // Route::get('/')->name('dashboard')->uses('DashboardController'); // default
    Route::get('/')->name('access')->uses('AccessController@index');
    Route::get('dashboard')->name('dashboard')->uses('DashboardController');
    Route::get('/settings')->name('settings')->uses('SettingsController');

    // Head Masters
    Route::resource('headmasters', 'HeadMasterController')->names('headmasters');
    Route::put('headmasters/{headmaster}/restore', 'HeadMasterController@restore')->name('headmasters.restore');

    // Date Rapor
    Route::resource('daterapors', 'DateRaporController')->names('daterapors');
    Route::put('daterapors/{daterapor}/restore', 'DateRaporController@restore')->name('daterapors.restore');

    // Users
    Route::get('users')->name('users')->uses('UsersController@index')->middleware('remember');
    Route::get('users/create/{role_id?}/{representative?}')->name('users.create')->uses('UsersController@create');
    Route::post('users')->name('users.store')->uses('UsersController@store');
    Route::get('users/{user}/edit')->name('users.edit')->uses('UsersController@edit');
    Route::post('users/{user}/change_password')->name('users.changePassword')->uses('UsersController@changePassword');
    Route::put('users/{user}')->name('users.update')->uses('UsersController@update');
    Route::delete('users/{user}')->name('users.destroy')->uses('UsersController@destroy');
    Route::put('users/{user}/restore')->name('users.restore')->uses('UsersController@restore');
    Route::get('import-users')->name('import-users')->uses('UsersController@import')->middleware('remember');
    Route::get('users/template')->name('users.template')->uses('UsersController@template');
    Route::post('users/template')->name('users.template')->uses('UsersController@processImport');
    Route::get('users/profiles/{user}')->name('users.profiles')->uses('UsersController@profiles');
    Route::post('users/profiles/{user}')->name('users.profiles')->uses('UsersController@store_profiles');
    Route::get('import-user-parent')->name('import-user-parent')->uses('UsersController@importparent')->middleware('remember');
    Route::get('users/templateparent')->name('users.templateparent')->uses('UsersController@templateparent');
    Route::post('users/templateparent')->name('users.templateparent')->uses('UsersController@processImportParent');
    Route::get('users/{user}/profile')->name('users.userprofile')->uses('UsersController@userprofile');
    Route::get('users/{user}/profilepassword')->name('users.userprofilepassword')->uses('UsersController@userprofilepassword');
    Route::get('users/exportusers/{user}', 'UsersController@exportExcel')->name('users.exportexcell');
    Route::get('users/blocks', 'UsersController@Block')->name('users.blocks');
    Route::delete('user/blocks/{user}')->name('users.destroyblock')->uses('UsersController@destroyblock');
    Route::get('users/templateuserblock')->name('users.templateuserblock')->uses('UsersController@templateuserblock');
    Route::post('users/templateuserblock')->name('users.templateuserblock')->uses('UsersController@processimportuserblock');

    // Images
    Route::get('/img/{path}', 'ImagesController@show')->where('path', '.*');

    // Organizations
    Route::get('organizations')->name('organizations')->uses('OrganizationsController@index')->middleware('remember');
    Route::get('organizations/create')->name('organizations.create')->uses('OrganizationsController@create');
    Route::post('organizations')->name('organizations.store')->uses('OrganizationsController@store');
    Route::get('organizations/{organization}/edit')->name('organizations.edit')->uses('OrganizationsController@edit');
    Route::put('organizations/{organization}')->name('organizations.update')->uses('OrganizationsController@update');
    Route::delete('organizations/{organization}')->name('organizations.destroy')->uses('OrganizationsController@destroy');
    Route::put('organizations/{organization}/restore')->name('organizations.restore')->uses('OrganizationsController@restore');

    // Groups
    Route::get('groups')->name('groups')->uses('GroupsController@index')->middleware('remember');
    Route::get('groups-nonActive')->name('groups.groups_non_active')->uses('GroupsController@groups_non_active')->middleware('remember');
    Route::get('groups/create')->name('groups.create')->uses('GroupsController@create');
    Route::post('groups')->name('groups.store')->uses('GroupsController@store');
    Route::get('groups/{group}/edit')->name('groups.edit')->uses('GroupsController@edit');
    Route::put('groups/{group}')->name('groups.update')->uses('GroupsController@update');
    Route::delete('groups/{group}')->name('groups.destroy')->uses('GroupsController@destroy');
    Route::put('groups/{group}/restore')->name('groups.restore')->uses('GroupsController@restore');
    Route::get('courses-modules/detail/{courses_module_id?}/')->name('detail.course.modules')->uses('CourseModuleController@detail');
    Route::get('import-groups')->name('import-groups')->uses('GroupsController@import')->middleware('remember');
    Route::get('export-groups')->name('export-groups')->uses('GroupsController@export')->middleware('remember');
    Route::get('groups/template')->name('groups.template')->uses('GroupsController@template');
    Route::post('groups/template')->name('groups.template')->uses('GroupsController@processImport');
    Route::get('export-master')->name('export-master')->uses('GroupsController@exportmaster')->middleware('remember');

    // Menus
    Route::get('menus')->name('menus')->uses('MenuController@index')->middleware('remember', 'auth');
    Route::get('menus/create')->name('menus.create')->uses('MenuController@create')->middleware('auth');
    Route::post('menus')->name('menus.store')->uses('MenuController@store')->middleware('auth');
    Route::get('menus/{menu}/edit')->name('menus.edit')->uses('MenuController@edit')->middleware('auth');
    Route::put('menus/{menu}')->name('menus.update')->uses('MenuController@update')->middleware('auth');
    Route::delete('menus/{menu}')->name('menus.destroy')->uses('MenuController@destroy')->middleware('auth');
    Route::put('menus/{menu}/restore')->name('menus.restore')->uses('MenuController@restore')->middleware('auth');

    // Contacts
    Route::get('contacts')->name('contacts')->uses('ContactsController@index')->middleware('remember');
    Route::get('contacts/create')->name('contacts.create')->uses('ContactsController@create');
    Route::post('contacts')->name('contacts.store')->uses('ContactsController@store');
    Route::get('contacts/{contact}/edit')->name('contacts.edit')->uses('ContactsController@edit');
    Route::put('contacts/{contact}')->name('contacts.update')->uses('ContactsController@update');
    Route::delete('contacts/{contact}')->name('contacts.destroy')->uses('ContactsController@destroy');
    Route::put('contacts/{contact}/restore')->name('contacts.restore')->uses('ContactsController@restore');

    // Subjects
    Route::get('subjects', 'SubjectsController@index')->name('subjects')->middleware('remember');
    Route::get('subjects/create', 'SubjectsController@create')->name('subjects.create');
    Route::post('subjects', 'SubjectsController@store')->name('subjects.store');
    Route::get('subjects/{subject}/edit', 'SubjectsController@edit')->name('subjects.edit');
    Route::put('subjects/{subject}', 'SubjectsController@update')->name('subjects.update');
    Route::delete('subjects/{subject}', 'SubjectsController@destroy')->name('subjects.destroy');
    Route::put('subjects/{subject}/restore', 'SubjectsController@restore')->name('subjects.restore');
    Route::get('import-subjects')->name('import-subjects')->uses('SubjectsController@import');
    Route::get('subjects/template')->name('subjects.template')->uses('SubjectsController@template');
    Route::post('subjects/template')->name('subjects.template')->uses('SubjectsController@processImport');

    // Subject Modules
    Route::get('subject-modules', 'SubjectModulesController@index')->name('subject_modules')->middleware('remember');
    Route::get('subject-modules/create', 'SubjectModulesController@create')->name('subject_modules.create');
    Route::post('subject-modules', 'SubjectModulesController@store')->name('subject_modules.store');
    Route::get('subject-modules/{subjectModule}/edit', 'SubjectModulesController@edit')->name('subject_modules.edit');
    Route::put('subject-modules/{subjectModule}', 'SubjectModulesController@update')->name('subject_modules.update');
    Route::delete('subject-modules/{subjectModule}', 'SubjectModulesController@destroy')->name('subject_modules.destroy');
    Route::put('subject-modules/{subjectModule}/restore', 'SubjectModulesController@restore')->name('subject_modules.restore');

    // Categories
    Route::resource('categories', 'CategoryController');
    Route::put('categories/{category}/restore', 'CategoryController@restore')->name('categories.restore');

    // Levels
    Route::resource('levels', 'LevelController');
    Route::put('levels/{level}/restore', 'LevelController@restore')->name('levels.restore');

    // Course
    Route::resource('courses', 'CourseController');
    Route::get('courses_non_active', 'CourseController@courses_non_active')->name('courses.courses_non_active');
    Route::put('courses/{course}/restore', 'CourseController@restore')->name('courses.restore');
    Route::get('course_users', 'CourseController@course_users')->name('course_users');
    Route::get('show_course_users/{course}', 'CourseController@show')->name('course_users_show');
    Route::post('course_users_store', 'CourseController@course_users_store')->name('course_users_store');
    Route::get('course_users_delete/{course}/{user?}', 'CourseController@course_users_delete')->name('course_users_delete');
    Route::get('course_users_activated/{course}/{user?}', 'CourseController@course_users_activated')->name('course_users_activated');
    Route::get('import-courses')->name('import-courses')->uses('CourseController@import')->middleware('remember');
    Route::get('export-courses')->name('export-courses')->uses('CourseController@export')->middleware('remember');
    Route::get('coursess/templates')->name('coursess.template')->uses('CourseController@template');
    Route::post('courses/template')->name('courses.template')->uses('CourseController@processImport');
    Route::get('import-course-modules/{course}')->name('import-course-modules')->uses('CourseController@import_course_modules')->middleware('remember');
    Route::get('course-modules/template')->name('course-modules.template')->uses('CourseController@template_course_modules');
    Route::post('course-modules/template')->name('course-modules.template')->uses('CourseController@processImport_course_modules');


    // course module
    Route::resource('course-modules', 'CourseModuleController', ['except' => ['create']]);
    Route::get('courses-modules/{course}', 'CourseModuleController@getModuleByCourse')->name('course-modules.get_by_course');

    Route::get('courses-unit_not_active/{course}', 'CourseModuleController@getCourseUnitNotActive')->name('course-modules.getCourseUnitNotActive');

    Route::get('courses-modules/create/{courses_id}/')->name('create.course.modules')->uses('CourseModuleController@create');
    Route::get('courses-modules/detail/{courses_module_id?}/')->name('detail.course.modules')->uses('CourseModuleController@detail');
    // Route::post('courses-modules')->name('course-modules.store')->uses('CourseModuleController@store');

    Route::get('course-unit/create/{course_module_id?}/')->name('create.course.unit')->uses('CourseUnitController@create');
    Route::put('course-modules/{course_module}/restore', 'CourseModuleController@restore')->name('course-modules.restore');

    Route::get('download/{pdf}', 'CourseUnitController@downloadFile')->name('course_units.download');

    //Route::post('courses-modules')->name('course-modules.store')->uses('CourseModuleController@store');


    Route::resource('course-units', 'CourseUnitController');
    Route::get('course-unit/create/{course_module_id?}/')->name('create.course.unit')->uses('CourseUnitController@create');
    Route::get('course-unit/see/{course_unit_id?}/')->name('course-unit.see')->uses('CourseUnitController@see');
    Route::get('course-unit/complete/{course_unit_id?}/')->name('course-unit.complete')->uses('CourseUnitController@complete');
    Route::get('course-unit/cancel-complete/{course_unit_id?}/')->name('course-unit.cancel_complete')->uses('CourseUnitController@cancel_complete');
    Route::get('download/{pdf}', 'CourseUnitController@downloadFile')->name('course_units.download');
    Route::delete('course-unit/{course_unit_id}')->name('course-unit.destroy')->uses('CourseUnitController@destroy');

    //// Reviews
    // Role Student
    Route::get('reviews/create/{course}')->name('reviews.create')->uses('ReviewController@create');
    Route::get('reviews')->name('reviews.index')->uses('ReviewController@index');
    Route::post('reviews')->name('reviews.store')->uses('ReviewController@store');
    Route::get('reviews/{review}/edit')->name('reviews.edit')->uses('ReviewController@edit');
    Route::put('reviews/{review}')->name('reviews.update')->uses('ReviewController@update');
    Route::delete('reviews/{review}')->name('reviews.destroy')->uses('ReviewController@destroy');

    // Role Teacher
    Route::get('list_approve_reviews/{course}')->name('reviews.list_approve')->uses('ReviewController@list_approve_reviews');
    Route::get('approve_reviews/{review}')->name('reviews.approve')->uses('ReviewController@approve_reviews');
    Route::get('decline_reviews/{review}')->name('reviews.decline')->uses('ReviewController@decline_reviews');



    ///// presences
    Route::get('presences/show/{course_module_id?}/')->name('presences-show')->uses('PresencesController@show');
    Route::post('presences/start/')->name('presences.start')->uses('PresencesController@start');
    Route::get('presences/stop/{course_module_id?}/')->name('presences.stop')->uses('PresencesController@stop');
    Route::post('presences/update-status/')->name('presences.update-status')->uses('PresencesController@update_status');
    Route::post('presences/present/')->name('presences.present')->uses('PresencesController@present');


    // Discussions
    // Route::resource('discussions', 'DiscussionController');
    Route::get('discussions/{course_module_id}', 'DiscussionController@index')->name('discussions.index');
    Route::get('discussion-details/{course_module_id}/{discussion_id}', 'DiscussionController@show')->name('discussions.show');
    Route::get('discussions/{course_module_id}/create', 'DiscussionController@create')->name('discussions.create');
    Route::post('discussions', 'DiscussionController@store')->name('discussions.store');
    Route::post('discussion-comments', 'DiscussionController@storeCommentDiscussion')->name('discussion_comments.store');
    Route::get('change-discussion-status/{discussion_id}', 'DiscussionController@toggleStatusActive')->name('discussions.change_status');
    Route::get('download-file-discussion/{discussion_id}', 'DiscussionController@downloadFile')->name('discussion.download_file');
    Route::delete('discussions/{discussion_id}', 'DiscussionController@destroy')->name('discussions.destroy');

    // UserTypes
    Route::get('usertypes')->name('usertypes')->uses('UserTypesController@index')->middleware('remember');
    Route::get('usertypes/create')->name('usertypes.create')->uses('UserTypesController@create');
    Route::post('usertypes')->name('usertypes.store')->uses('UserTypesController@store');
    Route::post('usertypes-store-menu')->name('usertypes.store_menu')->uses('UserTypesController@storeMenu');
    Route::get('usertypes/{usertypes}/edit')->name('usertypes.edit')->uses('UserTypesController@edit');
    Route::put('usertypes/{usertypes}')->name('usertypes.update')->uses('UserTypesController@update');
    Route::delete('usertypes/{usertypes}')->name('usertypes.destroy')->uses('UserTypesController@destroy');
    Route::put('usertypes/{usertypes}/restore')->name('usertypes.restore')->uses('UserTypesController@restore');

    // Group Courses
    Route::get('group-courses', 'GroupCoursesController@index')->name('group_courses')->middleware('remember');
    Route::get('group-courses/create', 'GroupCoursesController@create')->name('group_courses.create');
    Route::post('group-courses', 'GroupCoursesController@store')->name('group_courses.store');
    Route::get('group-courses/{groupCourses}/edit', 'GroupCoursesController@edit')->name('group_courses.edit');
    Route::put('group-courses/{groupCourses}', 'GroupCoursesController@update')->name('group_courses.update');
    Route::delete('group-coursess/{groupCourses}', 'GroupCoursesController@destroy')->name('group_courses.destroy');
    Route::put('group-courses/{groupCourses}/restore', 'GroupCoursesController@restore')->name('group_courses.restore');

    // Group Users
    Route::get('group-users', 'GroupUsersController@index')->name('group_users')->middleware('remember');
    Route::post('group-users', 'GroupUsersController@store')->name('group_users.store');
    Route::get('group-users/{group}/show')->name('group_users.show')->uses('GroupUsersController@show');
    Route::delete('group-users/{groupUsers}', 'GroupUsersController@destroy')->name('group_users.destroy');
    Route::get('import-group-users')->name('import-group-users')->uses('GroupUsersController@import')->middleware('remember');
    Route::get('group-users/template')->name('group-users.template')->uses('GroupUsersController@template');
    Route::post('group-users/template')->name('group-users.template')->uses('GroupUsersController@processImport');
    Route::get('export-group-users/{groupId}', 'GroupUsersController@exportExcel')->name('group-users.export');

    /// Murid
    Route::get('group-users-student')->name('groups-users-student')->uses('GroupUsersController@list_group')->middleware('remember');
    Route::get('student-groups')->name('student-groups')->uses('GroupUsersController@student_groups')->middleware('remember');
    Route::get('student-groups-exit/{group}')->name('student-groups-exit')->uses('GroupUsersController@student_groups_exit')->middleware('remember');
    Route::post('students-join-group')->name('students.join-group')->uses('GroupUsersController@join_group')->middleware('remember');

    Route::get('course-users-student')->name('course-users-student')->uses('CourseController@list_courses')->middleware('remember');
    Route::post('students_join_course')->name('students_join_course')->uses('CourseController@students_join_course')->middleware('remember');
    Route::get('import-course-users')->name('import-course-users')->uses('CourseController@import_course_users')->middleware('remember');
    Route::get('course-users/template')->name('course-users.template')->uses('CourseController@template_course_users');
    Route::post('course-users/template')->name('course-users.template')->uses('CourseController@processImport_course_users');

    /// Guru
    Route::get('list-approve-student')->name('list-approve-student')->uses('GroupUsersController@list_approve')->middleware('remember');
    Route::get('approve-group-student/{group}')->name('approve-group-student')->uses('GroupUsersController@approve_group_student')->middleware('remember');
    Route::get('decline-group-student/{group}')->name('decline-group-student')->uses('GroupUsersController@decline_group_student')->middleware('remember');

    Route::get('list_approve_course_student')->name('list_approve_course_student')->uses('CourseController@list_approve_course_student')->middleware('remember');
    Route::get('approve-course-student/{course}')->name('approve-course-student')->uses('CourseController@approve_course_student')->middleware('remember');
    Route::get('decline-course-student/{course}')->name('decline-course-student')->uses('CourseController@decline_course_student')->middleware('remember');

    // JADWAL
    Route::get('schedules', 'ScheduleController@index')->name('schedules.index');
    Route::get('day_schedules/{date}', 'ScheduleController@day_schedules')->name('schedules.day');

    // SERTIFIKAT
    Route::get('certificates/user/{user_id}', 'CertificateController@index')->name('certificates.index');
    Route::post('certificates', 'CertificateController@store')->name('certificates.store');
    Route::get('certificates/create/{user_id}', 'CertificateController@create')->name('certificates.create');
    Route::get('certificates/{certificate}', 'CertificateController@edit')->name('certificates.edit');
    Route::put('certificates/{certificate}', 'CertificateController@update')->name('certificates.update');
    Route::delete('certificates/{certificate}', 'CertificateController@destroy')->name('certificates.destroy');
    Route::get('download-certificate/{certificate}', 'CertificateController@downloadFile')->name('certificates.download_file');

    // TASK
    Route::get('tasks/{course_module_id}', 'TaskController@index')->name('tasks.index');
    Route::get('tasks/{course_module_id}/create', 'TaskController@create')->name('tasks.create');
    Route::post('tasks', 'TaskController@store')->name('tasks.store');
    Route::get('tasks/{task_id}/edit', 'TaskController@edit')->name('tasks.edit');
    Route::put('tasks/{task_id}', 'TaskController@update')->name('tasks.update');
    Route::delete('tasks/{task_id}', 'TaskController@destroy')->name('tasks.destroy');
    Route::get('download-file-task/{task_id}', 'TaskController@downloadFile')->name('tasks.download_file');
    Route::get('download-uploaded-task/{task_file_id}', 'TaskController@downloadUploadedTask')->name('tasks.download_uploaded_task');
    Route::get('tasks/{task_id}/student-upload', 'TaskController@studentUploadForm')->name('tasks.student_upload_form');
    Route::post('tasks/student-upload', 'TaskController@studentUploadFile')->name('tasks.student_upload');
    Route::get('show-all-uploaded-task/{task_id}', 'TaskController@showAllUploadedTask')->name('tasks.get_student_upload');
    Route::post('mark-student-task', 'TaskController@markStudentTask')->name('tasks.mark_student_task');
    Route::get('download-zip-task-student/{task_id}', 'TaskController@downloadAsZip')->name('tasks.download_zip');
    Route::get('finish-assignment/{task_id}', 'TaskController@finishAssignment')->name('tasks.finish_assignment');
    Route::get('export-task/{task_id}')->name('export-task-mark')->uses('TaskController@export')->middleware('remember');
    Route::get('import-task-mark')->name('import-task-mark')->uses('TaskController@import')->middleware('remember');
    Route::post('tasks/template')->name('tasks.template')->uses('TaskController@processImport');
    Route::get('tasks/{task_id}/student-mark-task-link', 'TaskController@studetMarkTaskLink')->name('tasks.student_mark_task_link');
    Route::get('tasks/preview/{task_id}', 'TaskController@preview')->name('tasks.preview');
    Route::get('lihat-tugas/{task_file_id}','TaskController@lihatTugas')->name('lihattugas');

    // Tartibs
    Route::get('tartibs', 'TartibsController@index')->name('tartibs')->middleware('remember');
    Route::get('tartibs/create', 'TartibsController@create')->name('tartibs.create');
    Route::post('tartibs', 'TartibsController@store')->name('tartibs.store');
    Route::get('tartibs/{tartib}/edit', 'TartibsController@edit')->name('tartibs.edit');
    Route::put('tartibs/{tartib}', 'TartibsController@update')->name('tartibs.update');
    Route::delete('tartibs/{tartib}', 'TartibsController@destroy')->name('tartibs.destroy');
    Route::put('tartibs/{tartib}/restore', 'TartibsController@restore')->name('tartibs.restore');
    Route::get('import-tartibs')->name('import-tartibs')->uses('TartibsController@import');
    Route::get('tartibs/template')->name('tartibs.template')->uses('TartibsController@template');
    Route::post('tartibs/template')->name('tartibs.template')->uses('TartibsController@processImport');

    //TARTIB USER
    Route::get('tartibneg-user/{id}/show')->name('tartibneg_users.show')->uses('TartibNegController@show');
    Route::get('tartibneg-user/{group_user_id}/create', 'TartibNegController@create')->name('tartibneg_users.create');
    Route::post('tartibneg-user', 'TartibNegController@store')->name('tartibneg_users.store');

    Route::get('tartibpos-user/{group_user_id}', 'TartibPosController@show')->name('tartibpos_users.show');
    Route::get('tartibpos-user/{group_user_id}/create', 'TartibPosController@create')->name('tartibpos_users.create');
    Route::post('tartibpos-user', 'TartibPosController@store')->name('tartibpos_users.store');

    // LAPORAN TARTIB
    Route::get('laporan-tartib/murid', 'TartibReportController@laporanTartibMurid')->name('laporan_tartib.murid');
    Route::get('laporan-tartib/orang-tua', 'TartibReportController@laporanTartibOrtu')->name('laporan_tartib.ortu');
    Route::get('laporan-tartib/guru', 'TartibReportController@laporanTartibGuru')->name('laporan_tartib.guru');
    Route::get('laporan-tartib/kelas', 'TartibReportController@laporanTartibKelas')->name('laporan_tartib.kelas');
    Route::get('laporan-tartib/export', 'TartibReportController@export')->name('laporan_tartib.export');
    Route::get('laporan-tartib/exportsiswa', 'TartibReportController@exportsiswa')->name('laporan_tartib.exportsisswa');

    //absensi
    Route::get('absensi/murid', 'AbsensiController@absensiMurid')->name('absensi.absen_murid');
    Route::get('absensi/orang-tua', 'AbsensiController@absensiOrtu')->name('absensi.absen_ortu');
    Route::get('absensi/guru', 'AbsensiController@absensiGuru')->name('absensi.absen_guru');
    Route::get('absensi/export-excel', 'AbsensiController@exportExcel')->name('absensi.export_excel');
    Route::get('absensi/rekap', 'AbsensiController@rekap')->name('absensi.rekap');
    Route::get('absensi/downloadrekap', 'AbsensiController@downloadrekap')->name('absensi-downloadrekap');
    Route::get('absensi/rekapguru', 'AbsensiController@rekapguru')->name('absensi.rekapguru');
    Route::post('absensi/downloadrekapguru', 'AbsensiController@downloadrekapguru')->name('absensi-downloadrekapguru');
    Route::get('absensi/downloadrekapguruonly', 'AbsensiController@downloadrekapguruonly')->name('absensi-downloadrekapguruonly');
    Route::get('absensi/downloadrekapguruexcel', 'AbsensiController@downloadrekapguruexcel')->name('absensi-downloadrekapguruexcel');
    
    // Reports

    //Interval
    Route::get('interval/template')->name('interval.template')->uses('IntervalController@template');
    Route::post('interval/template')->name('interval.template')->uses('IntervalController@processImport');
    Route::resource('interval', 'IntervalController');
    Route::get('interval_users', 'IntervalController@interval_users')->name('interval_users');
    Route::get('show_interval_users/{interval}', 'IntervalController@show')->name('interval_users_show');
    Route::get('import-interval')->name('import-interval')->uses('IntervalController@import')->middleware('remember');
    Route::get('export-interval')->name('export-interval')->uses('IntervalController@export')->middleware('remember');


    //Rapor
    Route::get('rapor', 'RaporController@index')->name('rapor.index')->middleware('remember');
    Route::get('rapor/create', 'RaporController@create')->name('rapor.create');
    Route::post('rapor/store', 'RaporController@store')->name('rapor.store');
    Route::get('rapor/create/{rapor}/edit', 'RaporController@editrapor')->name('rapor.edit');
    Route::get('rapor/{rapor}/{course}/edit', 'RaporController@edit')->name('rapor.editrapor');
    Route::get('rapor/{subject}/{kelas}/generate-all', 'RaporController@generateall')->name('rapor.generateall');
    Route::put('rapor/create/{rapor}')->name('rapor.updaterapor')->uses('RaporController@updaterapor');
    Route::delete('rapor/create/{rapor}')->name('rapor.destroy')->uses('RaporController@destroy');


    //reports
    Route::get('reports')->name('reports')->uses('ReportsController');
    Route::get('reports/guru', 'ReportsController@reportGuru')->name('report.guru'); // berdasarkan subject
    Route::get('reports/murid', 'ReportsController@reportMurid')->name('report.murid'); // berdasarkan murid
    Route::get('reports/siswa', 'ReportsController@reportSiswa')->name('report.siswa'); // login murid
    Route::get('reports/orang-tua', 'ReportsController@reportOrtu')->name('report.ortu'); //login ortu
    Route::get('report-subject')->name('report-subject')->uses('ReportsController@exportsubject')->middleware('remember');
    Route::get('report-siswa')->name('report-siswa')->uses('ReportsController@exportsiswa')->middleware('remember');
    Route::get('report-siswa-pdf')->name('report-siswa-pdf')->uses('ReportsController@exportsiswapdf')->middleware('remember');
    Route::get('report-siswa-pdf-tes')->name('report-siswa-pdf-tes')->uses('ReportsController@exportsiswapdftes')->middleware('remember'); //tes view report pdf

    //persentase rapot
    Route::get('persentase', 'PersentaseController@index')->name('persentase');
    Route::get('persentase/edit/{persentase}', 'PersentaseController@edit')->name('persentase.edit');
    Route::get('persentase/create', 'PersentaseController@create')->name('persentase.create');
    Route::post('persentase/store', 'PersentaseController@store')->name('persentase.store');
    Route::put('persentase/{persentase}')->name('persentase.update')->uses('PersentaseController@update');
    Route::delete('persentase/{persentase}')->name('persentase.destroy')->uses('PersentaseController@destroy');
});
