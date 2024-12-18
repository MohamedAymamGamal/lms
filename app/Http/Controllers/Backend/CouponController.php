<?php

namespace App\Http\Controllers\Backend;

use Carbon\Carbon;
use App\Models\Coupon;
use App\Models\SubCategory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;

class CouponController extends Controller
{
    public function AdminAllCoupon()
    {
        $coupon = Coupon::latest()->get();
        return view('admin.backend.coupon.coupon_all', compact('coupon'));
    } //end
    public function AdminAddCoupon()
    {
        return view('admin.backend.coupon.coupon_add');
    }
    public function AdminStoreCoupon(Request $request)
    {
        Coupon::insert([
            'coupon_name' => strtoupper($request->coupon_name),
            'coupon_discount' => $request->coupon_discount,
            'coupon_validity' => $request->coupon_validity,
            'created_at' => Carbon::now(),
        ]);
        $notification = array(
            'message' => 'Coupon Inserted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('admin.all.coupon')->with($notification);
    }
    public function AdminEditCoupon($id)
    {
        $coupon = Coupon::find($id);
        return view('admin.backend.coupon.coupon_edit', compact('coupon'));
    } /// End Method
    public function AdminUpdateCoupon(Request $request)
    {
        $coupon_id = $request->id;

        Coupon::find($coupon_id)->update([
            'coupon_name' => strtoupper($request->coupon_name),
            'coupon_discount' => $request->coupon_discount,
            'coupon_validity' => $request->coupon_validity,
            'created_at' => Carbon::now(),
        ]);
        $notification = array(
            'message' => 'Coupon Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('admin.all.coupon')->with($notification);
    } /// End Method
    public function AdminDeleteCoupon($id)
    {
        Coupon::find($id)->delete();
        $notification = array(
            'message' => 'Coupon Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    } /// End Method
}
