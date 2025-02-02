<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $chats = Chat::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('chats.index', compact('chats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        try {
            $chat = Chat::create([
                'user_id' => auth()->id(),
                'message' => $request->message
            ]);

            // Xử lý câu trả lời tự động
            $response = $this->generateResponse($request->message);
            $chat->update(['response' => $response]);

            return response()->json([
                'success' => true,
                'message' => 'Tin nhắn đã được gửi',
                'chat' => [
                    'id' => $chat->id,
                    'message' => $chat->message,
                    'response' => $chat->response,
                    'created_at' => $chat->formatted_date
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending chat message: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi gửi tin nhắn'
            ], 500);
        }
    }

    private function generateResponse($message)
    {
        // Danh sách các từ khóa và câu trả lời tương ứng
        $responses = [
            'xin chào' => 'Xin chào! Tôi có thể giúp gì cho bạn?',
            'hello' => 'Xin chào! Tôi có thể giúp gì cho bạn?',
            'hi' => 'Xin chào! Tôi có thể giúp gì cho bạn?',
            'tạm biệt' => 'Tạm biệt! Hẹn gặp lại bạn.',
            'bye' => 'Tạm biệt! Hẹn gặp lại bạn.',
            'giờ mở cửa' => 'Chúng tôi mở cửa từ 8h00 - 22h00 hàng ngày.',
            'địa chỉ' => 'Địa chỉ của chúng tôi là: 123 Đường ABC, Quận XYZ, TP.HCM',
            'liên hệ' => 'Bạn có thể liên hệ với chúng tôi qua số điện thoại: 0123456789 hoặc email: support@example.com',
            'phí vận chuyển' => 'Phí vận chuyển sẽ được tính dựa trên địa chỉ giao hàng của bạn. Vui lòng thêm sản phẩm vào giỏ hàng để xem chi tiết.',
            'thanh toán' => 'Chúng tôi hỗ trợ các hình thức thanh toán: COD, chuyển khoản ngân hàng, và các ví điện tử phổ biến.',
            'đổi trả' => 'Chúng tôi có chính sách đổi trả trong vòng 7 ngày kể từ ngày nhận hàng. Sản phẩm phải còn nguyên vẹn và đầy đủ phụ kiện.',
            'sách mới' => 'Bạn có thể xem các sách mới nhất tại trang chủ hoặc mục "Sách mới". Chúng tôi cập nhật sách mới hàng tuần.',
            'khuyến mãi' => 'Hiện tại chúng tôi đang có các chương trình khuyến mãi sau: Giảm 10% cho đơn hàng trên 500k, Freeship cho đơn hàng trên 300k.',
            'tài khoản' => 'Bạn có thể quản lý tài khoản của mình tại mục "Tài khoản". Tại đây bạn có thể cập nhật thông tin, xem lịch sử đơn hàng và điểm tích lũy.',
        ];

        // Chuyển message về chữ thường để dễ so sánh
        $message = mb_strtolower($message, 'UTF-8');

        // Tìm câu trả lời phù hợp
        foreach ($responses as $keyword => $response) {
            if (str_contains($message, $keyword)) {
                return $response;
            }
        }

        // Câu trả lời mặc định nếu không tìm thấy từ khóa phù hợp
        return 'Xin lỗi, tôi không hiểu câu hỏi của bạn. Bạn có thể thử hỏi cách khác hoặc liên hệ trực tiếp với chúng tôi qua số điện thoại: 0123456789';
    }

    public function markAsRead()
    {
        Chat::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
} 