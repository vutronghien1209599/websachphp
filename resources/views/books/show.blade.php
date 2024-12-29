@extends('layouts.app')

@section('title', $book->title)

@section('content')
<div class="row">
    <div class="col-md-4">
        <img src="{{ asset('storage/books/'.$book->image) }}" 
             class="img-fluid rounded" alt="{{ $book->title }}">
    </div>
    <div class="col-md-8">
        <h1>{{ $book->title }}</h1>
        <p class="text-muted">Tác giả: {{ $book->author }}</p>
        <p class="text-muted">Danh mục: {{ $book->category }}</p>
        <h4 class="text-danger">{{ number_format($book->price) }}đ</h4>
        
        <div class="my-4">
            <h5>Mô tả:</h5>
            <p>{{ $book->description }}</p>
        </div>

        @auth
            @if($book->stock > 0)
                <form action="{{ route('cart.add', $book) }}" method="POST" class="d-flex align-items-center">
                    @csrf
                    <input type="number" name="quantity" value="1" min="1" 
                           max="{{ $book->stock }}" class="form-control" style="width: 100px">
                    <button type="submit" class="btn btn-primary ms-3">Thêm vào giỏ</button>
                </form>
            @else
                <button class="btn btn-secondary" disabled>Hết hàng</button>
            @endif
        @else
            <a href="{{ route('login') }}" class="btn btn-primary">
                Đăng nhập để mua hàng
            </a>
        @endauth
    </div>
</div>
@endsection 