@php
$gNum = $gNum ?? "08";
$sNum = $sNum ?? "02";
$gName = $gName ?? "Alarm";
@endphp

@extends('layouts.user')

@section('content')
<div id="mainContent" class="container alarm_wrap">
    @include('components.user-header')
    <div class="contents">
    
        <div class="stitle">{{ $gName }}<div class="location"><a href="{{ route('home') }}" class="home">Home</a><span><strong>{{ $gName }}</strong></span></div></div>
        
        <div class="wbox sh2">
            <div class="board_view">
                <div class="tit">{{ $alarm->korean_title ?: $alarm->english_title }}
                    <dl class="date">
                        <dt>Registration Date</dt>
                        <dd>{{ \Carbon\Carbon::parse($alarm->created_at)->format('Y.m.d') }}</dd>
                    </dl>
                </div>
                @if($alarm->files && $alarm->files->count() > 0)
                    <div class="files">
                        @foreach($alarm->files as $file)
                            <a href="{{ asset('storage/' . $file->file_path) }}" download="{{ $file->file_name }}">{{ $file->file_name }}</a>
                        @endforeach
                    </div>
                @endif
                <div class="con">
                    {!! $alarm->korean_content ?: $alarm->english_content !!}
                </div>
            </div>
            <div class="board_bottom flex_center">
                <a href="{{ route('alarms') }}" class="btn_back">List</a>
            </div>
        </div>
        
    </div>
</div>

@include('components.user-footer')

@endsection
