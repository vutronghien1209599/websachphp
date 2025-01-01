<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class VNPayController extends Controller
{
    public function createPayment(Order $order)
    {
        // Kiểm tra và in ra các giá trị để debug
        Log::info('VNPAY Config:', [
            'url' => config('app.vnpay_url'),
            'tmn_code' => config('app.vnpay_tmn_code'),
            'hash_secret' => config('app.vnpay_hash_secret'),
            'return_url' => config('app.vnpay_return_url'),
        ]);

        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_ReturnUrl = URL::to('/vnpay/return');
        $vnp_TmnCode = "GEBGNQZC";
        $vnp_HashSecret = "391WOHKIIMQZIH348STZWJTF1I9LO974";

        $vnp_TxnRef = $order->id . '_' . time(); // Mã đơn hàng
        $vnp_OrderInfo = 'Thanh toan don hang #' . $order->id;
        $vnp_Amount = $order->total_amount * 100;
        $vnp_Locale = 'vn';
        $vnp_IpAddr = request()->ip();
        $vnp_OrderType = 'other';

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_ReturnUrl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        // Lưu thông tin vào session để kiểm tra khi VNPAY callback
        session(['vnpay_order_id' => $order->id]);

        // Log URL cuối cùng để debug
        Log::info('Final VNPAY URL:', ['url' => $vnp_Url]);

        return redirect($vnp_Url);
    }

    public function return(Request $request)
    {
        $vnp_HashSecret = "391WOHKIIMQZIH348STZWJTF1I9LO974";
        $inputData = array();
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $hashData = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        // Lấy order_id từ session
        $orderId = session('vnpay_order_id');
        if (!$orderId) {
            return redirect()->route('cart.index')
                ->with('error', 'Không tìm thấy thông tin đơn hàng');
        }

        if ($secureHash == $request->vnp_SecureHash) {
            if ($request->vnp_ResponseCode == '00') {
                $order = Order::findOrFail($orderId);
                
                // Cập nhật trạng thái đơn hàng
                $order->update([
                    'status' => 'confirmed',
                    'note' => 'Đã thanh toán qua VNPAY - Mã giao dịch: ' . $request->vnp_TransactionNo
                ]);

                // Xóa session VNPAY
                session()->forget('vnpay_order_id');

                return redirect()->route('orders.show', $order)
                    ->with('success', 'Thanh toán thành công!');
            }
        }

        return redirect()->route('cart.index')
            ->with('error', 'Thanh toán thất bại! Vui lòng thử lại sau.');
    }
} 