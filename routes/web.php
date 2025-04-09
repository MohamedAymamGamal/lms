<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InstructorController;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Controllers\Backend\OrderController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Backend\CouponController;
use App\Http\Controllers\Backend\CourseController;
use App\Http\Controllers\Backend\ReportController;
use App\Http\Controllers\Backend\ReviewController;
use App\Http\Controllers\Frontend\IndexController;
use App\Http\Controllers\Backend\SettingController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\QuestionController;
use App\Http\Controllers\Frontend\WishListController;
use App\Http\Controllers\Backend\ActiveUserController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\BlogController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/dashboard', function () {
//     return view('welcome');
// });
//home
Route::get('/', [UserController::class, 'index'])->name('index');

//home
Route::get('/dashboard', function () {
    return view('frontend.dashboard.index');
})->middleware(['auth', 'roles:user', 'verified'])->name('dashboard');
//normal users
Route::middleware('auth')->group(function () {
    Route::get('/user/profile', [UserController::class, 'UserProfile'])->name('user.profile');
    Route::post('/user/profile/update', [UserController::class, 'UserProfileUpdate'])->name('user.profile.update');
    Route::get('/user/logout', [UserController::class, 'UserLogout'])->name('user.logout');
    Route::get('/user/change/passsword', [UserController::class, 'UserChangePassword'])->name('user.change.password');
    Route::post('/user/password/update', [UserController::class, 'UserPasswordUpdate'])->name('user.password.update');
});

//admin
Route::middleware(['auth', 'roles:admin'])->group(function () {
    //get
    Route::get('/admin/dashboard', [AdminController::class, 'AdminDashborad'])->name('admin.dashboard');
    Route::get('/admin/logout', [AdminController::class, 'AdminLogout'])->name('admin.logout');
    Route::get('/admin/profile', [AdminController::class, 'AdminProfile'])->name('admin.profile');
    Route::get('/admin/profile', [AdminController::class, 'AdminProfile'])->name('admin.profile');
    Route::get('/admin/change/password', [AdminController::class, 'AdminChangePassword'])->name('admin.change.password');
    //post
    Route::post('/admin/profile/store', [AdminController::class, 'AdminProfileStore'])->name('admin.profile.store');
    Route::post('/admin/password/update', [AdminController::class, 'AdminPasswordUpdate'])->name('admin.password.update');
});
Route::get('/admin/login', [AdminController::class, 'AdminLogin'])->name('admin.login')->middleware(RedirectIfAuthenticated::class);
Route::get('/become/instructor', [AdminController::class, 'BecomeInstructor'])->name('become.instructor');
Route::post('/instructor/register', [AdminController::class, 'InstructorRegister'])->name('instructor.register');
//instructor
Route::middleware(['auth', 'roles:instructor'])->group(function () {
    //get
    Route::get('/instructor/dashboard', [InstructorController::class, 'InstructorDashboard'])->name('instructor.dashboard');
    Route::get('/instructor/logout', [InstructorController::class, 'InstructorLogout'])->name('instructor.logout');
    Route::get('/instructor/profile', [InstructorController::class, 'InstructorProfile'])->name('instructor.profile');
    Route::get('/instructor/change/password', [InstructorController::class, 'InstructorChangePassword'])->name('instructor.change.password');

    //post
    Route::post('/instructor/profile/store', [InstructorController::class, 'InstructorProfileStore'])->name('instructor.profile.store');
    Route::post('/instructor/password/update', [InstructorController::class, 'InstructorPasswordUpdate'])->name('instructor.password.update');
});
//instructor login
Route::get('/instructor/login', [InstructorController::class, 'InstructorLogin'])->name('instructor.login')->middleware(RedirectIfAuthenticated::class);;
require __DIR__ . '/auth.php';

//category
Route::controller(CategoryController::class)->group(function () {
    //get
    
    Route::get('/all/category', 'AllCategory')->name('all.category')->middleware('permission:category.all');
    Route::get('/add/category', 'AddCategory')->name('add.category');
    Route::get('/edit/category/{id}', 'EditCategory')->name('edit.category');
    Route::get('/delete/category/{id}', 'DeleteCategory')->name('delete.category');
    //post
    Route::post('/update/category', 'UpdateCategory')->name('update.category');
    Route::post('/store/category', 'StoreCategory')->name('store.category');
});
//subcategory
Route::controller(CategoryController::class)->group(function () {
    //get
    Route::get('/all/subcategory', 'AllSubCategory')->name('all.subcategory')->middleware('permission:subcategory.all');
    Route::get('/add/subcategory', 'AddSubCategory')->name('add.subcategory');
    Route::get('/edit/subcategory/{id}', 'EditSubCategory')->name('edit.subcategory');
    Route::get('/delete/subcategory/{id}', 'DeleteSubCategory')->name('delete.subcategory');
    //post
    Route::post('/update/subcategory', 'UpdateSubCategory')->name('update.subcategory');
    Route::post('/store/subcategory', 'StoreSubCategory')->name('store.subcategory');
});
// Instructor All Route
Route::controller(AdminController::class)->group(function () {
    Route::get('/all/instructor', 'AllInstructor')->name('all.instructor');
    Route::post('/update/user/stauts', 'UpdateUserStatus')->name('update.user.stauts');
});
//Coures
Route::controller(CourseController::class)->group(function () {
    //get
    Route::get('/all/course', 'AllCourse')->name('all.course');
    Route::get('/add/course', 'AddCourse')->name('add.course');
    Route::get('/subcategory/ajax/{category_id}', 'GetSubCategory');
    Route::get('/edit/course/{id}', 'EditCourse')->name('edit.course');
    Route::get('/delete/course/{id}', 'DeleteCourse')->name('delete.course');
    //post
    Route::post('/store/course', 'StoreCourse')->name('store.course');
    Route::post('/update/course', 'UpdateCourse')->name('update.course');
    Route::post('/update/course/image', 'UpdateCourseImage')->name('update.course.image');
    Route::post('/update/course/video', 'UpdateCourseVideo')->name('update.course.video');
    Route::post('/update/course/goal', 'UpdateCourseGoal')->name('update.course.goal');
});
//for lecture
Route::controller(CourseController::class)->group(function () {
    //get
    Route::get('/add/course/lecture/{id}', 'AddCourseLecture')->name('add.course.lecture');
    Route::get('/edit/lecture/{id}', 'EditLecture')->name('edit.lecture');
    Route::get('/delete/lecture/{id}', 'DeleteLecture')->name('delete.lecture');
    //post
    Route::post('/add/course/section/', 'AddCourseSection')->name('add.course.section');
    Route::post('/save-lecture/', 'SaveLecture')->name('save-lecture');
    Route::post('/update/course/lecture', 'UpdateCourseLecture')->name('update.course.lecture');
    Route::post('/delete/section/{id}', 'DeleteSection')->name('delete.section');
});

Route::controller(IndexController::class)->group(function () {
    //get
    Route::get('/course/details/{id}/{slug}', 'CourseDetails');
    Route::get('/category/{id}/{slug}', 'CategoryCourse');
    Route::get('/category/{id}/{slug}', 'CategoryCourse');
    Route::get('/subcategory/{id}/{slug}', 'SubCategoryCourse');
    Route::get('/instructor/details/{id}', 'InstructorDetails')->name('instructor.details');
    Route::get('/about-us', 'AboutUS')->name('index.about-us');

    //post
});

Route::controller(CartController::class)->group(function () {
    //get
    Route::get('/cart/data/',  'CartData');
    Route::get('/course/mini/cart/',  'AddMiniCart');
    Route::get('/minicart/course/remove/{rowId}', 'RemoveMiniCart');
    Route::get('/mycart', 'MyCart')->name('mycart');
    //checkout
    Route::get('/checkout', 'CheckoutCreate')->name('checkout');

    //post
    Route::post('/cart/data/store/{id}', 'AddToCart');
    Route::post('/payment', 'Payment')->name('payment');
    Route::post('/buy/data/store/{id}', 'BuyToCart');
    Route::post('/coupon-apply',  'CouponApply');

    Route::get('/coupon-remove',  'CouponRemove');
    Route::get('/coupon-calculation', 'CouponCalculation');
    //coupon for user
    Route::post('/inscoupon-apply',  'InsCouponApply');
});

Route::post('/add-to-wishlist/{course_id}', [WishListController::class, 'AddToWishList']);

//WishListController
Route::controller(WishListController::class)->group(function () {
    //get
    Route::get('/user/wishlist', 'AllWishlist')->name('user.wishlist');
    Route::get('/wishlist-remove/{id}', 'RemoveWishlist');
    Route::get('/get-wishlist-course/', 'GetWishlistCourse');
    Route::get('/get-cart-course', 'GetCartCourse');
});
Route::controller(AdminController::class)->group(function () {
    //get
    Route::get('/admin/all/course', 'AdminAllCourse')->name('admin.all.course');
    Route::get('/admin/course/details/{id}', 'AdminCourseDetails')->name('admin.course.details');
    //post
    Route::post('/update/course/stauts', 'UpdateCourseStatus')->name('update.course.stauts');
});
Route::controller(SettingController::class)->group(function () {
    //get
    Route::get('/smtp/setting', 'SmtpSetting')->name('smtp.setting');
    //post
    Route::post('/update/smtp', 'SmtpUpdate')->name('update.smtp');
    Route::post('/update/smtp', 'SmtpSetting')->name('update.smtp');
});
Route::controller(OrderController::class)->group(function () {
    //get
    Route::get('/admin/order/details/{id}', 'AdminOrderDetails')->name('admin.order.details');
    Route::get('/admin/pending/order', 'AdminPendingOrder')->name('admin.pending.order');
    Route::get('/admin/order/details/{id}', 'AdminOrderDetails')->name('admin.order.details');
    Route::get('/pending-confrim/{id}', 'PendingToConfirm')->name('pending-confrim');
    Route::get('/admin/confirm/order', 'AdminConfirmOrder')->name('admin.confirm.order');
    Route::get('/instructor/all/order', 'InstructorAllOrder')->name('instructor.all.order');
    Route::get('/instructor/order/details/{payment_id}', 'InstructorOrderDetails')->name('instructor.order.details');
    Route::get('/my/course', 'MyCourse')->name('my.course');
    Route::get('/instructor/order/invoice/{payment_id}', 'InstructorOrderInvoice')->name('instructor.order.invoice');
    //FOR COURSE VIEW TO USER
    Route::get('/course/view/{course_id}', 'CourseView')->name('course.view');
});
Route::controller(ReportController::class)->group(function () {
    //get
    Route::get('/report/view', 'ReportView')->name('report.view');
    //post
    Route::post('/search/by/date','SearchByDate')->name('search.by.date');
    Route::post('/search/by/month','SearchByMonth')->name('search.by.month');
    Route::post('/search/by/year','SearchByYear')->name('search.by.year');

});
Route::controller(ActiveUserController::class)->group(function () {
    Route::get('/all/user', 'AllUser')->name('all.user');
    Route::get('/all/instructor', 'AllInstructor')->name('all.instructor');
});
Route::controller(CouponController::class)->group(function () {
    Route::get('/admin/all/coupon', 'AdminAllCoupon')->name('admin.all.coupon');
    Route::get('/admin/add/coupon', 'AdminAddCoupon')->name('admin.add.coupon');
    Route::post('/admin/store/coupon', 'AdminStoreCoupon')->name('admin.store.coupon');
    Route::get('/admin/edit/coupon/{id}', 'AdminEditCoupon')->name('admin.edit.coupon');
    Route::post('/admin/update/coupon', 'AdminUpdateCoupon')->name('admin.update.coupon');
    Route::get('/admin/delete/coupon/{id}', 'AdminDeleteCoupon')->name('admin.delete.coupon');
});
Route::controller(QuestionController::class)->group(function () {
    //post
    Route::post('/user/question', 'UserQuestion')->name('user.question');
    Route::post('/instructor/replay', 'InstructorReplay')->name('instructor.replay');

    //get
    Route::get('/instructor/all/question', 'InstructorAllQuestion')->name('instructor.all.question');
    Route::get('/question/details/{id}', 'QuestionDetails')->name('question.details');

});
// Instructor Coupon  Route
Route::controller(CouponController::class)->group(function(){
    //get
    Route::get('/instructor/delete/coupon/{id}','InstructorDeleteCoupon')->name('instructor.delete.coupon');
    Route::get('/instructor/all/coupon','InstructorAllCoupon')->name('instructor.all.coupon');
    Route::get('/instructor/add/coupon','InstructorAddCoupon')->name('instructor.add.coupon');
    Route::get('/instructor/edit/coupon/{id}','InstructorEditCoupon')->name('instructor.edit.coupon');
    //post
    Route::post('/instructor/update/coupon','InstructorUpdateCoupon')->name('instructor.update.coupon');
    Route::post('/instructor/store/coupon','InstructorStoreCoupon')->name('instructor.store.coupon');
    
});
Route::controller(ReviewController::class)->group(function(){
    //review for main page 
    Route::post('/store/review',  'StoreReview')->name('store.review');
    //review for admin 
    Route::get('/admin/pending/review','AdminPendingReview')->name('admin.pending.review'); 
    Route::post('/update/review/stauts','UpdateReviewStatus')->name('update.review.stauts'); 
    Route::get('/admin/active/review','AdminActiveReview')->name('admin.active.review');

    //review for instructor 
    Route::get('/instructor/all/review','InstructorAllReview')->name('instructor.all.review');  
    //review for instructor 

});
//blog controller
Route::controller(BlogController::class)->group(function(){
    Route::get('/blog/category','AllBlogCategory')->name('blog.category');  
    Route::post('/blog/category/store','StoreBlogCategory')->name('blog.category.store'); 
    Route::post('/blog/category/update','UpdateBlogCategory')->name('blog.category.update'); 
    Route::get('/delete/blog/category/{id}','DeleteBlogCategory')->name('delete.blog.category'); 
    //for user interface
    Route::get('/blog/post','BlogPost')->name('blog.post'); 
    Route::get('/add/blog/post','AddBlogPost')->name('add.blog.post'); 
    Route::post('/store/blog/post','StoreBlogPost')->name('store.blog.post'); 
    Route::post('/store/blog/post','StoreBlogPost')->name('store.blog.post');
    Route::get('/edit/post/{id}','EditBlogPost')->name('edit.post');  
    Route::post('/update/blog/post','UpdateBlogPost')->name('update.blog.post');
    Route::get('/delete/post/{id}','DeleteBlogPost')->name('delete.post');  
    Route::get('/blog/details/{slug}','BlogDetails');
    Route::get('/blog/cat/list/{id}', 'BlogCatList');
    Route::get('/blog', 'BlogList')->name('blog');
});
//for notification system
Route::post('/mark-notification-as-read/{notification}', [CartController::class, 'MarkAsRead']);

//for notification 
Route::controller(SettingController::class)->group(function(){
    Route::get('/site/setting','SiteSetting')->name('site.setting'); 
    Route::post('/update/site','UpdateSite')->name('update.site'); 
});

Route::controller(RoleController::class)->group(function(){
    Route::get('/all/permission','AllPermission')->name('all.permission'); 
    Route::get('/add/permission','AddPermission')->name('add.permission');
    Route::post('/store/permission','StorePermission')->name('store.permission'); 
    Route::get('/edit/permission/{id}','EditPermission')->name('edit.permission');
    Route::post('/update/permission','UpdatePermission')->name('update.permission');
    Route::get('/delete/permission/{id}','DeletePermission')->name('delete.permission');
    //for excel
    Route::get('/import/permission','ImportPermission')->name('import.permission');
    Route::get('/export','Export')->name('export');
    Route::post('/import','Import')->name('import');
    //role mangement 
    Route::get('/all/roles','AllRoles')->name('all.roles');
    Route::get('/add/roles','AddRoles')->name('add.roles'); 
    Route::post('/store/roles','StoreRoles')->name('store.roles'); 
    Route::get('/edit/roles/{id}','EditRoles')->name('edit.roles');
    Route::post('/update/roles','UpdateRoles')->name('update.roles');
    Route::get('/delete/roles/{id}','DeleteRoles')->name('delete.roles');  
    Route::get('/add/roles/permission','AddRolesPermission')->name('add.roles.permission');
    Route::post('/role/permission/store','RolePermissionStore')->name('role.permission.store');
    Route::get('/all/roles/permission','AllRolesPermission')->name('all.roles.permission');
    Route::get('/admin/edit/roles/{id}','AdminEditRoles')->name('admin.edit.roles');
    Route::post('/admin/roles/update/{id}','AdminUpdateRoles')->name('admin.roles.update');
    Route::get('/admin/delete/roles/{id}','AdminDeleteRoles')->name('admin.delete.roles');
});
//// Admin User for All Route 
Route::controller(AdminController::class)->group(function(){
    Route::get('/all/admin','AllAdmin')->name('all.admin');  
    Route::get('/all/admin','AllAdmin')->name('all.admin'); 
    Route::get('/add/admin','AddAdmin')->name('add.admin');
    Route::post('/store/admin','StoreAdmin')->name('store.admin'); 
    Route::get('/edit/admin/{id}','EditAdmin')->name('edit.admin');
    Route::post('/update/admin/{id}','UpdateAdmin')->name('update.admin'); 
    Route::get('/delete/admin/{id}','DeleteAdmin')->name('delete.admin'); 
});
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
});

Route::middleware(['auth', 'role:instructor'])->group(function () {
    Route::get('/instructor/dashboard', [InstructorController::class, 'index'])->name('instructor.dashboard');
});

Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/dashboard', [UserController::class, 'index'])->name('user.dashboard');
});
