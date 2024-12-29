@extends('layouts.app')

@section('title', 'Danh sách yêu thích')

@section('content')
<div class="container">
    <h2 class="mb-4">Danh sách yêu thích</h2>

    @if($wishlist->count() > 0)
        <div class="row">
            @foreach($wishlist as $item)
                <div class="col-md-3 mb-4">
                    <div class="card">
                        <img src="{{ asset('storage/books/'.$item->book->image) }}" 
                             class="card-img-top" alt="{{ $item->book->title }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $item->book->title }}</h5>
                            <p class="card-text">{{ number_format($item->book->price) }}đ</p>
                            
                            <div class="d-flex justify-content-between">
                                <form action="{{ route('cart.add', $item->book) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        Thêm vào giỏ
                                    </button>
                                </form>
                                
                                <form action="{{ route('wishlist.remove', $item->book) }}" 
                                      method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        Xóa
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info">
            Danh sách yêu thích của bạn đang trống.
            <a href="{{ route('books.index') }}" class="alert-link">Xem sách ngay</a>
        </div>
    @endif
</div>
@endsection 