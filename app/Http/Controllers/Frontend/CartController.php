<?php

namespace App\Http\Controllers\Frontend;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Course;
use App\Models\Category;
use App\Models\Payment ;
use App\Models\Course_goal;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Models\CourseLecture;
use App\Models\CourseSection;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Gloudemans\Shoppingcart\Facades\Cart;
use Symfony\Component\HttpFoundation\Session\Session;
use Illuminate\Support\Facades\Mail;
use App\Mail\Orderconfirm;


class CartController extends Controller
{
    public function AddToCart(Request $request, $id){

        $course = Course::find($id);

        // Check if the course is already in the cart
        $cartItem = Cart::search(function ($cartItem, $rowId) use ($id) {
            return $cartItem->id === $id;
        });

        if ($cartItem->isNotEmpty()) {
            return response()->json(['error' => 'Course is already in your cart']);
        }

        if ($course->discount_price == NULL) {

            Cart::add([
                'id' => $id,
                'name' => $request->course_name,
                'qty' => 1,
                'price' => $course->selling_price,
                'weight' => 1,
                'options' => [
                    'image' => $course->course_image,
                    'slug' => $request->course_name_slug,
                    'instructor' => $request->instructor,
                ],
            ]);

        }else{

            Cart::add([
                'id' => $id,
                'name' => $request->course_name,
                'qty' => 1,
                'price' => $course->discount_price,
                'weight' => 1,
                'options' => [
                    'image' => $course->course_image,
                    'slug' => $request->course_name_slug,
                    'instructor' => $request->instructor,
                ],
            ]);
        }

        return response()->json(['success' => 'Successfully Added on Your Cart']);

    }// End Method



    public function CartData(){

        $carts = Cart::content();
        $cartTotal = Cart::total();
        $cartQty = Cart::count();

        return response()->json(array(
            'carts' => $carts,
            'cartTotal' => $cartTotal,
            'cartQty' => $cartQty,
        ));

    }

    public function AddMiniCart(){

        $carts = Cart::content();
        $cartTotal = Cart::total();
        $cartQty = Cart::count();

        return response()->json(array(
            'carts' => $carts,
            'cartTotal' => $cartTotal,
            'cartQty' => $cartQty,
        ));

    }// End Method
    public function RemoveMiniCart($rowId){

        Cart::remove($rowId);
        return response()->json(['success' => 'Course Remove From Cart']);

    }// End Method
    public function MyCart(){
        $carts = Cart::content();
        $cartTotal = Cart::total();
        $cartQty = Cart::count();

        return view('frontend.mycart.view_mycart',compact('carts','cartTotal','cartQty'));

    } // End Method
    public function GetCartCourse(){

        $carts = Cart::content();
        $cartTotal = Cart::total();
        $cartQty = Cart::count();

        return response()->json(array(
            'carts' => $carts,
            'cartTotal' => $cartTotal,
            'cartQty' => $cartQty,
        ));

    }// End Method
    public function CartRemove($rowId){

        Cart::remove($rowId);
        return response()->json(['success' => 'Course Remove From Cart']);

    }// End Method
    public function CheckoutCreate(){

        if (Auth::check()) {

            if (Cart::total() > 0) {
                $carts = Cart::content();
                $cartTotal = Cart::total();
                $cartQty = Cart::count();

                return view('frontend.checkout.checkout_view',compact('carts','cartTotal','cartQty'));
            } else{

                $notification = array(
                    'message' => 'Add At list One Course',
                    'alert-type' => 'error'
                );
                return redirect()->to('/')->with($notification);

            }

        }else{

            $notification = array(
                'message' => 'You Need to Login First',
                'alert-type' => 'error'
            );
            return redirect()->route('login')->with($notification);

        }

    }// End Method
    public function Payment(Request $request){


        $total_amount = round(Cart::total());


        // Cerate a new Payment Record

        $data = new Payment();
        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address;
        $data->cash_delivery = $request->cash_delivery;
        $data->total_amount = $total_amount;
        $data->payment_type = 'Direct Payment';

        $data->invoice_no = 'EOS' . mt_rand(10000000, 99999999);
        $data->order_date = Carbon::now()->format('d F Y');
        $data->order_month = Carbon::now()->format('F');
        $data->order_year = Carbon::now()->format('Y');
        $data->status = 'pending';
        $data->created_at = Carbon::now();
        $data->save();


       foreach ($request->course_title as $key => $course_title) {

            $existingOrder = Order::where('user_id',Auth::user()->id)->where('course_id',$request->course_id[$key])->first();

            if ($existingOrder) {

                $notification = array(
                    'message' => 'You Have already enrolled in this course',
                    'alert-type' => 'error'
                );
                return redirect()->back()->with($notification);
            } // end if

            $order = new Order();
            $order->payment_id = $data->id;
            $order->user_id = Auth::user()->id;
            $order->course_id = $request->course_id[$key];
            $order->instructor_id = $request->instructor_id[$key];
            $order->course_title = $course_title;
            $order->price = $request->price[$key];
            $order->save();

           } // end foreach

           $request->session()->forget('cart');

           $paymentId = $data->id;

           /// Start Send email to student ///
           $sendmail = Payment::find($paymentId);
           $data = [
                'invoice_no' => $sendmail->invoice_no ?? null,
                'amount' => $total_amount ?? null,
                'name' => $sendmail->name ?? null,
                'email' => $sendmail->email ?? null,
           ];



           /// End Send email to student ///
            Mail::to($request->email)->send(new Orderconfirm($data));


            if ($request->cash_delivery == 'stripe') {
               echo "stripe";
            }else{

                $notification = array(
                    'message' => 'Cash Payment Submit Successfully',
                    'alert-type' => 'success'
                );
                return redirect()->route('index')->with($notification);

            }

    }// End Method
    public function BuyToCart(Request $request, $id){

        $course = Course::find($id);

        // Check if the course is already in the cart
        $cartItem = Cart::search(function ($cartItem, $rowId) use ($id) {
            return $cartItem->id === $id;
        });

        if ($cartItem->isNotEmpty()) {
            return response()->json(['error' => 'Course is already in your cart']);
        }

        if ($course->discount_price == NULL) {

            Cart::add([
                'id' => $id,
                'name' => $request->course_name,
                'qty' => 1,
                'price' => $course->selling_price,
                'weight' => 1,
                'options' => [
                    'image' => $course->course_image,
                    'slug' => $request->course_name_slug,
                    'instructor' => $request->instructor,
                ],
            ]);

        }else{

            Cart::add([
                'id' => $id,
                'name' => $request->course_name,
                'qty' => 1,
                'price' => $course->discount_price,
                'weight' => 1,
                'options' => [
                    'image' => $course->course_image,
                    'slug' => $request->course_name_slug,
                    'instructor' => $request->instructor,
                ],
            ]);
        }

        return response()->json(['success' => 'Successfully Added on Your Cart']);

    }// End Method








}