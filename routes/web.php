<?php

use App\Http\Controllers\Officials\AccountController;
use App\Http\Controllers\Officials\Auth\OfficialsLoginController;
use App\Http\Controllers\Officials\CourseController;
use App\Http\Controllers\Officials\DepartmentsController;
use App\Http\Controllers\Officials\FacultiesController;
use App\Http\Controllers\Officials\OfficialsDashboardController;
use App\Http\Controllers\Officials\OnlineTransactionsController;
use App\Http\Controllers\Officials\PaymentsController;
use App\Http\Controllers\Officials\ProfileController;
use App\Http\Controllers\Officials\RegistrationsController;
use App\Http\Controllers\Officials\SchemeSettingsController;
use App\Http\Controllers\Officials\SessionController;
use App\Http\Controllers\Officials\StudentController;
use App\Http\Controllers\Officials\TokensController;
use App\Http\Controllers\Officials\UsersController;
use App\Http\Controllers\Officials\VendorsController;
use App\Http\Controllers\Officials\VenturesController;
use App\Http\Controllers\Officials\VerificationOfficersController;
use App\Http\Controllers\Officials\VerificationsController;
use App\Http\Controllers\Registration\RegistrationPaymentsController;
use App\Http\Controllers\Shared\DepartmentsController as SharedDepartmentsController;
use App\Http\Controllers\Student\Auth\StudentLoginController;
use App\Http\Controllers\Student\Auth\StudentRegistrationController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Transaction\PaymentTransactionsController;
use App\Http\Controllers\Vendor\Auth\VendorLoginController;
use App\Http\Controllers\Vendor\Auth\VendorRegistrationController;
use App\Http\Controllers\Vendor\ProfileController as VendorProfileController;
use App\Http\Controllers\Vendor\StudentController as VendorStudentController;
use App\Http\Controllers\Vendor\VendorDashboardController;
use App\Http\Controllers\Vendor\VendorStudentRegistrationController;
use App\Http\Controllers\Vendor\VendorTokenController;
use App\Services\DataImport;
use App\Models\Account;
use App\Models\Course;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Level;
use App\Models\OnlineTransaction;
use App\Models\Registration;
use App\Models\Role;
use App\Models\Scheme;
use App\Models\Session;
use App\Models\StudentProfile;
use App\Models\TokenTransaction;
use App\Models\User;
use App\Services\ExcelExport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;

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


$officialsRoles = 'edc-admin,edc-director,edc-verification-officer,superadmin,gss-admin,gss-director,vc-unical';

Route::get('/upload-csv', fn() => view('upload') );

Route::get('student-profiles', function(){
   return Excel::download(new ExcelExport(), 'student-profiles.xlsx');
});

Route::middleware(['guest:student'])->group(function(){

    Route::get('/student/register', [ StudentRegistrationController::class , 'index'])->name('student.auth.registration.index');
        // Route::get('/register', [ StudentRegistrationController::class , 'create'])->name('student.auth.registration.create');
    Route::get('/student/verify-fees', [ StudentRegistrationController::class , 'verifyFees'])->name('student.auth.registration.verify');
    Route::get('/student/login', [ StudentLoginController::class , 'index'])->name('student.auth.login.index');

    Route::post('/student/register', [ StudentRegistrationController::class , 'store'])->name('student.auth.registration.store');
    Route::post('/student/login', [ StudentLoginController::class , 'login'])->name('student.auth.login.store');

});

Route::middleware(['guest:vendor'])->group(function(){

    Route::get('/vendor/register', [ VendorRegistrationController::class , 'index'])->name('vendor.auth.register');
    Route::post('/vendor/register', [ VendorRegistrationController::class , 'store'])->name('vendor.auth.register.store');
    Route::get('/vendor/login', [ VendorLoginController::class , 'index'])->name('vendor.auth.login');
    Route::post('/vendor/login', [ VendorLoginController::class , 'login'])->name('vendor.auth.login.store');

});

Route::middleware(['guest:'.$officialsRoles])->group(function(){

    Route::get('{role}/login/', [ OfficialsLoginController::class , 'index'])->name('officials.auth.index');
    Route::post('{role}/login/', [ OfficialsLoginController::class , 'login'])->name('officials.auth.login');

});



//Student Routes
Route::middleware(['auth:student', 'has.role:student'])->group(function(){

    Route::post('/student/logout', [ StudentLoginController::class , 'logout'])->name('student.auth.logout');
    Route::get('/student/registrations/invoice/{registration:invoice_number}', [ DashboardController::class, 'invoice'])->name('student.registration.invoice');

    Route::get('/student/dashboard', [ DashboardController::class , 'index'])->name('student.dashboard.index');
    Route::get('/student/edit/password', fn() => view('student.profile.password'))->name('student.profile.edit.password');
    
    Route::post('/student/edit/password', function(){

        $user = auth()->guard('student')->user();
        
        $password = request()->validate(['password' => 'required']);

        $user->update([ 'password' => Hash::make( $password['password'] ) ] );
        
        alert('Success','Password Succesfully Changed');
            
        return redirect(route('student.dashboard.index'));

    })->name('student.profile.update.password');  
});


//Vendor Routes
Route::middleware(['auth:vendor', 'status:vendor', 'has.role:vendor'])->group(function(){

    Route::post('/vendor/logout', [ VendorLoginController::class , 'logout'])->name('vendor.auth.logout');

    Route::get('/vendor/dashboard', [ VendorDashboardController::class , 'index'])->name('vendor.dashboard.index');

    Route::get('/vendor/profile/password', [ VendorProfileController::class, 'changePassword'])->name('vendor.profile.password');
    Route::post('/vendor/profile/update', [ VendorProfileController::class, 'updatePassword'])->name('vendor.profile.password.update');

    Route::get('/vendor/student/registrations', [ VendorStudentRegistrationController::class, 'index'])->name('vendor.student.registration.index');
    Route::get('/vendor/student/registrations/search', [ VendorStudentRegistrationController::class, 'searchRegistration'])->name('vendors.registration.search');
    
    Route::get('/vendor/student/registrations/invoice/{registration:invoice_number}', [ VendorStudentRegistrationController::class, 'invoice'])->name('vendor.student.registration.invoice');
    Route::get('/vendor/student/registration/verify', [ VendorStudentRegistrationController::class, 'verify'])->name('vendor.student.registration.verify');
    Route::get('/vendor/student/registration/create', [ VendorStudentRegistrationController::class, 'verifyStudentCode'])->name('vendor.student.registration.create');
    Route::post('/vendor/student/registration/create', [ VendorStudentRegistrationController::class, 'store'])->name('vendor.student.registration.store');
    
    Route::get('/vendor/student/registration/{registration:invoice_number}/edit', [ VendorStudentRegistrationController::class, 'edit'])->name('vendor.student.registration.edit');
    Route::patch('/vendor/student/registration/update/{registration:invoice_number}', [ VendorStudentRegistrationController::class, 'update'])->name('vendor.student.registration.update');

    Route::get('/vendor/tokens', [VendorTokenController::class, 'index'])->name('vendor.token.index');
    Route::get('/vendor/tokens/search', [VendorTokenController::class, 'search'])->name('vendors.token.search');
    
    Route::get('/vendor/tokens/purchase', [VendorTokenController::class, 'create'])->name('vendor.token.create');
    Route::get('/vendor/tokens/purchase/success', [VendorTokenController::class, 'store'])->name('vendor.token.success');
    Route::post('/vendor/tokens/purchase', [PaymentTransactionsController::class, 'buyToken'])->name('vendor.token.store');

    Route::get('/vendor/students', [ VendorStudentController::class, 'index' ])->name('vendors.students.index');
    Route::get('/vendor/students/search', [ VendorStudentController::class, 'searchStudent' ])->name('vendors.students.search');

    Route::get('/vendors/password/edit/{student}', [ VendorStudentController::class, 'editPassword' ])->name('vendors.students.edit.password');
    Route::get('/vendors/profile/edit/{student}', [ VendorStudentController::class, 'editStudent' ])->name('vendors.students.edit.profile')->middleware('can:edit-student-data');
    Route::post('/vendors/password/update/{student}', [ VendorStudentController::class, 'updatePassword' ])->name('vendors.students.update.password');
    Route::post('/vendors/profile/update/{student}', [ VendorStudentController::class, 'updateStudent' ])->name('vendors.students.update.profile')->middleware('can:edit-student-data');
    
});




//Officials Route

Route::middleware(['auth:'.$officialsRoles, 'status:'.$officialsRoles, 'has.role:'.$officialsRoles])->group(function(){

    Route::post('{role:name}/logout/', [ OfficialsLoginController::class , 'logout'])->name('officials.auth.logout');
    Route::get('/{role:name}/dashboard/', [ OfficialsDashboardController::class , 'index'])->name('officials.dashboard.index');

    Route::get('/{role:name}/profile/password', [ ProfileController::class, 'changePassword'])->name('officials.profile.password');
    Route::post('/{role:name}/profile/update', [ ProfileController::class, 'updatePassword'])->name('officials.profile.password.update');

    //Registrations Page
    Route::middleware('can:view-registrations')->group(function(){
        Route::get('/{role:name}/registrations/{scheme:name}', [ RegistrationsController::class , 'index' ])->name('officials.registrations.index');
    });
    Route::get('/{role:name}/registration/{registration:invoice_number}', [ RegistrationsController::class, 'show' ])->name('officials.registrations.show');
    Route::get('/{role:name}/registration/edit/{registration}', [ RegistrationsController::class, 'edit' ])->name('officials.registrations.edit');
    Route::post('/{role:name}/registration/update/{registration}', [ RegistrationsController::class, 'update' ])->name('officials.registrations.update');
    Route::get('/{role:name}/registration/confirm-payment/{registration}', [ RegistrationsController::class, 'confirmPayment' ])->name('officials.registrations.confirm.payment');
    Route::get('/{role:name}/search/{searchType}/{scheme:name}', [ RegistrationsController::class , 'searchForRegistration'])->name('officials.registrations.search');
    
    //Verifications Page
    Route::get('/{role:name}/verifications/', [ VerificationsController::class , 'index'])->name('officials.verifications.index');

    //For EDC-Verification-Officers
    Route::get('/{role:name}/verification/verify/{registration}', [ VerificationsController::class , 'verify'])->name('officials.verifications.verify');
    
    
    //Tokens Page
    Route::middleware('can:view-token-transactions')->group(function(){
        Route::get('/{role:name}/tokens/purchases', [ TokensController::class , 'purchases'])->name('officials.tokens.purchases');
        Route::get('/{role:name}/tokens/registrations', [ TokensController::class , 'registrations'])->name('officials.tokens.registrations');
        Route::get('/{role:name}/tokens/purchases/get', [ TokensController::class , 'getTokenTransactions'])->name('officials.token.transactions.get');
        Route::get('/{role:name}/tokens/registrations/get', [ TokensController::class , 'getTokenRegistrations'])->name('officials.token.registrations.get');
    });
    
    //Vendors Page
    Route::middleware('can:view-vendors')->group(function(){
        Route::get('/{role:name}/vendors/', [ VendorsController::class , 'index'])->name('officials.vendors.index');
        Route::get('/{role:name}/vendors/{vendor}/tokens', [ VendorsController::class , 'tokens'])->name('officials.vendors.tokens');
        Route::get('/{role:name}/vendors/{vendor}/registrations', [ VendorsController::class , 'registrations'])->name('officials.vendors.registrations');
        Route::get('/{role:name}/{vendor}/activate', [ VendorsController::class , 'activate'])->name('officials.vendors.activate');
        Route::get('/{role:name}/toggle/{vendor}/{toggle?}', [ VendorsController::class , 'toggleRole'])->name('officials.vendors.role.toggle');
        Route::get('/{role:name}/editor/{vendor}', [ VendorsController::class , 'toggleEditStudentData'])->name('officials.vendors.role.editor');
        Route::get('/{role:name}/get-vendors', [ VendorsController::class , 'getVendors'])->name('officials.vendors.get');
        
        Route::post('/{role:name}/activate/vendors', [ VendorsController::class , 'activateVendors'])->name('officials.vendors.bulkactivate');
        Route::post('/{role:name}/deactivate/vendors', [ VendorsController::class , 'deactivateVendors'])->name('officials.vendors.bulkdeactivate');
    });
    
    //Payments Page
    Route::middleware('can:view-payments')->group(function(){
        Route::get('/{role:name}/payments/bank', [ PaymentsController::class , 'bank' ])->name('officials.payments.bank');
        Route::get('/{role:name}/payments/online', [ PaymentsController::class , 'online' ])->name('officials.payments.online');
        Route::get('/{role:name}/payments/{paymentType}/get', [ PaymentsController::class , 'getPayments' ])->name('officials.payments.get');
    });
    
    //Verification Officers Page
    Route::get('/{role:name}/verification-officers/', [ VerificationOfficersController::class , 'index'])->name('officials.verification.officers.index');
    Route::get('/{role:name}/verification-officers/create', [ VerificationOfficersController::class , 'create'])->name('officials.verification.officers.create');
    Route::get('/{role:name}/verification-officers/{officer}/edit', [ VerificationOfficersController::class , 'edit'])->name('officials.verification.officers.edit');
    Route::post('/{role:name}/verification-officers/create', [ VerificationOfficersController::class , 'store'])->name('officials.verification.officers.store');
    Route::patch('/{role:name}/verification-officers/update/{officer}', [ VerificationOfficersController::class , 'update'])->name('officials.verification.officers.update');
    Route::get('/{role:name}/officer/{officer}/verification-officers/activate', [ VerificationOfficersController::class , 'activate'])->name('officials.verification.officers.activate');


   
    Route::middleware('can:view-faculties-and-departments')->group(function(){

        //Faculties Page
        Route::get('/{role:name}/faculties/', [ FacultiesController::class , 'index'])->name('officials.faculties.index');
        Route::get('/{role:name}/faculties/{faculty}/edit', [ FacultiesController::class , 'edit'])->name('officials.faculties.edit');
        Route::get('/{role:name}/faculties/create', [ FacultiesController::class , 'create'])->name('officials.faculties.create');
        Route::post('/{role:name}/faculties/create', [ FacultiesController::class , 'store'])->name('officials.faculties.store');
        Route::patch('/{role:name}/faculties/update/{faculty}', [ FacultiesController::class , 'update'])->name('officials.faculties.update');

        //Departments Page
        Route::get('/{role:name}/departments/', [ DepartmentsController::class , 'index'])->name('officials.departments.index');
        Route::get('/{role:name}/departments/{department}/edit', [ DepartmentsController::class , 'edit'])->name('officials.departments.edit');
        Route::get('/{role:name}/departments/create', [ DepartmentsController::class , 'create'])->name('officials.departments.create');
        Route::post('/{role:name}/departments/create', [ DepartmentsController::class , 'store'])->name('officials.departments.store');
        Route::patch('/{role:name}/departments/update/{department}', [ DepartmentsController::class , 'update'])->name('officials.departments.update');
    });
   

    //Ventures Page
    Route::middleware('can:view-ventures')->group(function(){
        Route::get('/{role:name}/ventures/', [ VenturesController::class , 'index'])->name('officials.ventures.index');
        Route::get('/{role:name}/venture/{venture}/edit/', [ VenturesController::class , 'edit'])->name('officials.ventures.edit');
        Route::get('/{role:name}/venture/{venture}/edit_fee/', [ VenturesController::class , 'editFee'])->name('officials.ventures.edit_fee');
        Route::patch('/{role:name}/venture/{venture}/update', [ VenturesController::class , 'update'])->name('officials.ventures.update');
        Route::post('/{role:name}/venture/{venture}/update_fee', [ VenturesController::class , 'updateFee'])->name('officials.ventures.update_fee');
    });

    //Sessions Page
    Route::middleware('can:view-sessions')->group(function(){
        Route::get('/{role:name}/sessions/', [ SessionController::class , 'index'])->name('officials.sessions.index');
        Route::get('/{role:name}/session/{session}/edit', [ SessionController::class , 'edit'])->name('officials.sessions.edit');
        Route::get('/{role:name}/session/{session}/toggle', [ SessionController::class , 'toggle'])->name('officials.sessions.toggle');
        Route::get('/{role:name}/session/{session}/update-registration-status/{scheme}', [ SessionController::class , 'updateUpdateRegistrationStatus'])->name('officials.sessions.update.status');
        Route::patch('/{role:name}/session/{session}/update-semester', [ SessionController::class , 'updateSession'])->name('officials.sessions.semester.update');
        Route::post('/{role:name}/sessions/create', [ SessionController::class , 'store'])->name('officials.sessions.store');
        Route::get('/{role:name}/sessions/create', [ SessionController::class , 'create'])->name('officials.sessions.create');
    });

    Route::get('/{role:name}/session/{session}', [ SessionController::class , 'update'])->name('officials.sessions.update');


    Route::middleware('can:perform-super')->group(function(){
         //Courses Page
        Route::get('/{role:name}/courses/{scheme:name}', [ CourseController::class , 'index'])->name('officials.courses.index');
        Route::get('/{role:name}/courses/{scheme:name}/create', [ CourseController::class , 'create'])->name('officials.courses.create');
        Route::post('/{role:name}/courses/{scheme:name}/create', [ CourseController::class , 'store'])->name('officials.courses.store');
        Route::get('/{role:name}/courses/{course:item_code}/{scheme:name}/edit', [ CourseController::class , 'edit'])->name('officials.courses.edit');
        Route::patch('/{role:name}/courses/{course:item_code}/{scheme:name}/update', [ CourseController::class , 'update'])->name('officials.courses.update');
        Route::get('/{role:name}/courses/{course:item_code}/{scheme:name}/edit-departments', [ CourseController::class , 'editDepartments'])->name('officials.courses.edit.departments');
        Route::patch('/{role:name}/courses/{course:item_code}/{scheme:name}/update-departments', [ CourseController::class , 'updateDepartments'])->name('officials.courses.update.departments');

        Route::get('/{role:name}/courses/{scheme:name}/add', [ CourseController::class , 'addCourses'])->name('officials.courses.add_courses');
        Route::post('/{role:name}/courses/{scheme:name}/add-courses-to-session', [ CourseController::class , 'addCoursesToSession'])->name('officials.courses.add_courses_to_session');



        //Account Page
        Route::get('/{role:name}/accounts', [ AccountController::class , 'index'])->name('officials.accounts.index');
        Route::get('/{role:name}/accounts/{account}/edit', [ AccountController::class , 'edit'])->name('officials.accounts.edit');
        Route::get('/{role:name}/accounts/create', [ AccountController::class , 'create'])->name('officials.accounts.create');
        Route::post('/{role:name}/accounts/create', [ AccountController::class , 'store'])->name('officials.accounts.store');
        Route::patch('/{role:name}/accounts/{account}/update', [ AccountController::class , 'update'])->name('officials.accounts.update');
        Route::delete('/{role:name}/accounts/{account}/delete', [ AccountController::class , 'delete'])->name('officials.accounts.delete');
       
       
        //Payments Transactions
        Route::get('/{role:name}/transactions', [ OnlineTransactionsController::class , 'index' ])->name('officials.transactions.index');
        Route::get('/{role:name}/transactions/get', [ OnlineTransactionsController::class , 'getTransactions' ])->name('officials.transactions.get');
        Route::get('/{role:name}/transactions/{transaction}/confirm', [ OnlineTransactionsController::class , 'confirmPayment' ])->name('officials.transactions.confirm');
        Route::post('/{role:name}/payments/verify', [ OnlineTransactionsController::class, 'verifyTransaction' ])->name('officials.transaction.verify');


        //Scheme Settings
        Route::get('/{role:name}/settings/scheme/{scheme:name}/online', [ SchemeSettingsController::class , 'onlineIndex'])->name('officials.schemes.settings.index.online');
        Route::get('/{role:name}/settings/scheme/{scheme:name}/bank', [ SchemeSettingsController::class , 'bankIndex'])->name('officials.schemes.settings.index.bank');
        Route::get('/{role:name}/scheme/charges/{scheme:name}/{channel}/create', [ SchemeSettingsController::class , 'create'])->name('officials.schemes.charges.create');
        Route::get('/{role:name}/settings/{scheme:name}/toggle-online-payments', [ SchemeSettingsController::class , 'toggleOnlinePayments'])->name('officials.schemes.settings.toggle.online');
        Route::get('/{role:name}/settings/{scheme:name}/toggle-bank-payments', [ SchemeSettingsController::class , 'toggleBankPayments'])->name('officials.schemes.settings.toggle.bank');
        Route::post('/{role:name}/scheme/charges/{scheme:name}/{channel}/create', [ SchemeSettingsController::class , 'store'])->name('officials.schemes.charges.store');
        Route::get('/{role:name}/scheme/charges/{scheme:name}/{key}/{type}/{channel}/edit', [ SchemeSettingsController::class , 'edit'])->name('officials.schemes.charges.edit');
        Route::patch('/{role:name}/scheme/charges/{scheme:name}/{key}/{channel}/update', [ SchemeSettingsController::class , 'update'])->name('officials.schemes.charges.update');
        Route::delete('/{role:name}/scheme/charges/{scheme:name}/{key}/{type}/{channel}/delete', [ SchemeSettingsController::class , 'delete'])->name('officials.schemes.charges.delete');

        Route::get('/{role:name}/users', [ UsersController::class , 'index'])->name('officials.users.index');
        Route::get('/{role:name}/users/search', [ UsersController::class , 'search'])->name('officials.users.search');

        Route::get('/{role:name}/users/password/{user}', [ UsersController::class , 'changePassword'])->name('officials.users.password');
        Route::post('/{role:name}/users/password/{user}/update', [ UsersController::class , 'updatePassword'])->name('officials.users.password.update');


       
    });
   
     //Students Page
     Route::get('/{role:name}/students/', [ StudentController::class, 'index' ])->name('officials.students.index');
     Route::get('/{role:name}/student/create', [ StudentController::class, 'create' ])->name('officials.students.create');
     Route::get('/{role:name}/student/edit/{student}', [ StudentController::class, 'edit' ])->name('officials.students.edit');
     Route::post('/{role:name}/student/delete/{student}', [ StudentController::class, 'deleteStudentProfile' ])->name('officials.students.delete');
     Route::get('/{role:name}/student/password/edit/{student}', [ StudentController::class, 'editPassword' ])->name('officials.students.edit.password');
     Route::post('/{role:name}/student/create', [ StudentController::class, 'store' ])->name('officials.students.store');
     Route::post('/{role:name}/student/update/{student}', [ StudentController::class, 'update' ])->name('officials.students.update');
     Route::post('/{role:name}/student/update/password/{student}', [ StudentController::class, 'updatePassword' ])->name('officials.students.update.password');
     Route::get('/{role:name}/search/students', [ StudentController::class, 'searchStudent' ])->name('officials.students.search');
   
});

Route::middleware(['auth:student,vendor', 'has.role:student,vendor'])->group(function(){
    Route::get('registration/pay/{registration:invoice_number}', [PaymentTransactionsController::class, 'onlinePaymentWithPaymentRequest'])->name('registration.payment');
    Route::get('registration/payment/success', [RegistrationPaymentsController::class, 'store'])->name('registration.payment.success');   
});

Route::get('get-departments/{faculty}', [ SharedDepartmentsController::class, 'getDepartments' ]);

