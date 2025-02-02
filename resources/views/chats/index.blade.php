@extends('layouts.app')

@section('title', 'Chat với chúng tôi')

@push('styles')
<style>
:root {
    --primary-color: #0084ff;
    --primary-light: #1a91ff;
    --primary-dark: #0074e4;
    --secondary-color: #00c6ff;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --success: #34d399;
    --danger: #ef4444;
}

.chat-container {
    height: calc(100vh - 200px);
    min-height: 700px;
    background: #fff;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    border-radius: 24px;
    box-shadow: 0 12px 48px rgba(0, 0, 0, 0.1);
    border: 1px solid var(--gray-200);
    position: relative;
}

.card-header {
    background: #fff;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    align-items: center;
    gap: 1rem;
    position: relative;
    z-index: 10;
}

.card-header::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
}

.card-header h5 {
    font-weight: 700;
    font-size: 1.15rem;
    color: var(--gray-700);
    margin: 0;
}

.card-header i {
    color: var(--primary-color);
    font-size: 1.25rem;
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 2rem;
    background: var(--gray-50);
    background-image: 
        radial-gradient(circle at 100% 100%, var(--gray-100) 0.5rem, transparent 0.5rem),
        radial-gradient(circle at 0% 100%, var(--gray-100) 0.5rem, transparent 0.5rem);
    scroll-behavior: smooth;
}

.chat-messages::-webkit-scrollbar {
    width: 6px;
}

.chat-messages::-webkit-scrollbar-track {
    background: transparent;
}

.chat-messages::-webkit-scrollbar-thumb {
    background: var(--gray-300);
    border-radius: 20px;
}

.chat-messages::-webkit-scrollbar-thumb:hover {
    background: var(--gray-400);
}

.message {
    display: flex;
    flex-direction: column;
    margin-bottom: 1.5rem;
    max-width: 65%;
    animation: messageSlide 0.3s ease-out;
    position: relative;
}

@keyframes messageSlide {
    from { 
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.message.user-message {
    margin-left: auto;
    align-items: flex-end;
}

.message.bot-message {
    margin-right: auto;
    align-items: flex-start;
}

.message .content {
    padding: 0.875rem 1.25rem;
    border-radius: 20px;
    position: relative;
    word-break: break-word;
    line-height: 1.6;
    font-size: 0.95rem;
    transition: all 0.2s ease;
}

.message.user-message .content {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    border-bottom-right-radius: 4px;
    box-shadow: 0 2px 8px rgba(0, 132, 255, 0.15);
}

.message.user-message .content::before {
    content: '';
    position: absolute;
    bottom: 0;
    right: -6px;
    width: 12px;
    height: 12px;
    background: var(--secondary-color);
    clip-path: polygon(0 0, 0% 100%, 100% 100%);
}

.message.bot-message .content {
    background: white;
    color: var(--gray-700);
    border-bottom-left-radius: 4px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.message.bot-message .content::before {
    content: '';
    position: absolute;
    bottom: 0;
    left: -6px;
    width: 12px;
    height: 12px;
    background: white;
    clip-path: polygon(100% 0, 0 100%, 100% 100%);
}

.message .time {
    font-size: 0.75rem;
    color: var(--gray-500);
    margin-top: 0.375rem;
    opacity: 0.9;
}

.message:hover .time {
    opacity: 1;
}

.suggestions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin: 2rem 0;
    justify-content: center;
    animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.suggestion-chip {
    padding: 0.75rem 1.5rem;
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 100px;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    font-size: 0.9rem;
    color: var(--gray-600);
    display: flex;
    align-items: center;
    gap: 0.625rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    font-weight: 500;
}

.suggestion-chip:hover {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    border-color: transparent;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 132, 255, 0.2);
}

.suggestion-chip i {
    font-size: 1rem;
    transition: transform 0.2s ease;
}

.suggestion-chip:hover i {
    transform: scale(1.1);
}

.chat-input {
    padding: 1.5rem;
    background: white;
    border-top: 1px solid var(--gray-200);
    position: relative;
}

.chat-input::before {
    content: '';
    position: absolute;
    top: -1px;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
    opacity: 0.5;
}

.chat-input form {
    display: flex;
    gap: 1rem;
    align-items: flex-end;
    max-width: 900px;
    margin: 0 auto;
}

.chat-input textarea {
    flex: 1;
    border: 2px solid var(--gray-200);
    border-radius: 20px;
    padding: 1rem 1.5rem;
    resize: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    line-height: 1.5;
    min-height: 54px;
    max-height: 150px;
    font-size: 0.95rem;
    background: var(--gray-50);
    color: var(--gray-700);
}

.chat-input textarea::placeholder {
    color: var(--gray-400);
}

.chat-input textarea:focus {
    border-color: var(--primary-color);
    background: white;
    box-shadow: 0 0 0 4px rgba(0, 132, 255, 0.1);
    outline: none;
}

.chat-input .btn {
    width: 54px;
    height: 54px;
    padding: 0;
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border: none;
    box-shadow: 0 2px 8px rgba(0, 132, 255, 0.25);
}

.chat-input .btn:hover {
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 4px 12px rgba(0, 132, 255, 0.3);
}

.chat-input .btn:active {
    transform: translateY(0) scale(0.98);
}

.chat-input .btn i {
    font-size: 1.25rem;
    color: white;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--gray-600);
    animation: fadeIn 0.5s ease-out;
}

.empty-state p {
    margin-bottom: 2rem;
    line-height: 1.6;
}

.empty-state .h5 {
    color: var(--gray-700);
    font-weight: 700;
    font-size: 1.25rem;
    margin-bottom: 1rem;
}

.empty-state .h5::after {
    content: '';
    display: block;
    width: 50px;
    height: 3px;
    background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
    margin: 1rem auto 0;
    border-radius: 100px;
}

.pagination {
    margin-top: 2rem;
    display: flex;
    justify-content: center;
    gap: 0.5rem;
}

.pagination .page-link {
    border-radius: 12px;
    padding: 0.75rem 1rem;
    color: var(--gray-600);
    border: 1px solid var(--gray-200);
    transition: all 0.2s ease;
    font-weight: 500;
    min-width: 40px;
    text-align: center;
}

.pagination .page-link:hover {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
    transform: translateY(-1px);
}

.pagination .active .page-link {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border-color: transparent;
    color: white;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(0, 132, 255, 0.2);
}

@media (max-width: 768px) {
    .chat-container {
        height: calc(100vh - 120px);
        border-radius: 16px;
    }

    .message {
        max-width: 85%;
    }

    .suggestions {
        gap: 0.5rem;
    }

    .suggestion-chip {
        padding: 0.625rem 1rem;
        font-size: 0.85rem;
    }

    .chat-input {
        padding: 1rem;
    }

    .chat-input textarea {
        padding: 0.875rem 1.25rem;
        font-size: 0.9rem;
    }
}
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="chat-container">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-comments me-2"></i>Chat với chúng tôi</h5>
                </div>
                
                <div class="chat-messages" id="chat-messages">
                    @forelse ($chats as $chat)
                        <!-- Tin nhắn của user -->
                        <div class="message user-message">
                            <div class="content">{{ $chat->message }}</div>
                            <div class="time">{{ $chat->formatted_date }}</div>
                        </div>

                        <!-- Phản hồi của bot -->
                        @if ($chat->response)
                            <div class="message bot-message">
                                <div class="content">{{ $chat->response }}</div>
                                <div class="time">{{ $chat->formatted_date }}</div>
                            </div>
                        @endif
                    @empty
                        <div class="empty-state">
                            <p class="h5 mb-3">Chào mừng bạn đến với hệ thống chat hỗ trợ! 👋</p>
                            <p class="text-muted">Bạn có thể hỏi về:</p>
                        </div>
                        <div class="suggestions">
                            <div class="suggestion-chip" onclick="sendSuggestion(this)">
                                <i class="far fa-clock"></i>Giờ mở cửa
                            </div>
                            <div class="suggestion-chip" onclick="sendSuggestion(this)">
                                <i class="fas fa-map-marker-alt"></i>Địa chỉ
                            </div>
                            <div class="suggestion-chip" onclick="sendSuggestion(this)">
                                <i class="fas fa-phone"></i>Liên hệ
                            </div>
                            <div class="suggestion-chip" onclick="sendSuggestion(this)">
                                <i class="fas fa-truck"></i>Phí vận chuyển
                            </div>
                            <div class="suggestion-chip" onclick="sendSuggestion(this)">
                                <i class="fas fa-credit-card"></i>Thanh toán
                            </div>
                            <div class="suggestion-chip" onclick="sendSuggestion(this)">
                                <i class="fas fa-exchange-alt"></i>Đổi trả
                            </div>
                            <div class="suggestion-chip" onclick="sendSuggestion(this)">
                                <i class="fas fa-book"></i>Sách mới
                            </div>
                            <div class="suggestion-chip" onclick="sendSuggestion(this)">
                                <i class="fas fa-tags"></i>Khuyến mãi
                            </div>
                        </div>
                    @endforelse
                </div>

                <div class="chat-input">
                    <form id="chat-form">
                        <textarea class="form-control" 
                                  id="message" 
                                  name="message" 
                                  placeholder="Nhập tin nhắn của bạn..."
                                  rows="1"></textarea>
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>

            @if ($chats->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $chats->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const chatMessages = $('#chat-messages');
    const chatForm = $('#chat-form');
    const messageInput = $('#message');

    // Thiết lập CSRF token cho tất cả các request Ajax
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Cuộn xuống tin nhắn mới nhất
    function scrollToBottom() {
        chatMessages.scrollTop(chatMessages[0].scrollHeight);
    }

    // Thêm tin nhắn mới vào khung chat
    function appendMessage(message, isUser = true) {
        const messageHtml = `
            <div class="message ${isUser ? 'user-message' : 'bot-message'}">
                <div class="content">${message}</div>
                <div class="time">Vừa xong</div>
            </div>
        `;
        chatMessages.append(messageHtml);
        scrollToBottom();
    }

    // Xử lý gửi tin nhắn
    chatForm.submit(function(e) {
        e.preventDefault();
        const message = messageInput.val().trim();
        
        if (!message) return;

        // Disable form
        const submitBtn = chatForm.find('button[type="submit"]');
        messageInput.prop('disabled', true);
        submitBtn.prop('disabled', true);

        // Gửi tin nhắn
        $.post('{{ route('chats.store') }}', {
            message: message
        })
        .done(function(response) {
            if (response.success) {
                // Hiển thị tin nhắn của user
                appendMessage(response.chat.message, true);
                
                // Hiển thị phản hồi của bot
                if (response.chat.response) {
                    appendMessage(response.chat.response, false);
                }

                // Reset form
                messageInput.val('');
            } else {
                toastr.error(response.message);
            }
        })
        .fail(function(xhr) {
            toastr.error(xhr.responseJSON?.message || 'Có lỗi xảy ra khi gửi tin nhắn');
        })
        .always(function() {
            // Enable form
            messageInput.prop('disabled', false);
            submitBtn.prop('disabled', false);
            messageInput.focus();
        });
    });

    // Cuộn xuống tin nhắn mới nhất khi mới vào trang
    scrollToBottom();

    // Đánh dấu đã đọc khi vào trang
    $.post('{{ route('chats.mark-as-read') }}');

    // Auto resize textarea
    messageInput.on('input', function() {
        this.style.height = '50px';
        this.style.height = (this.scrollHeight) + 'px';
    });
});

// Xử lý click vào suggestion
function sendSuggestion(element) {
    const message = $(element).text();
    $('#message').val(message);
    $('#chat-form').submit();
}
</script>
@endpush 