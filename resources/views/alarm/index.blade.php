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
        
        <div class="wbox">
            @include('components.board-top', [
                'total' => $alarms->total(),
                'routeName' => 'alarms',
                'perPage' => request('per_page', 10),
                'keyword' => request('keyword', ''),
                'showCategorySelect' => false
            ])
            
            <div class="tbl board_list">
                <table>
                    <colgroup>
                        <col class="w90">
                        <col>
                        <col class="w120">
                        <col class="w120">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Title</th>
                            <th>Attached File</th>
                            <th>Registration Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($alarms as $alarm)
                            <tr>
                                <td class="num">{{ $alarms->total() - (($alarms->currentPage() - 1) * $alarms->perPage()) - $loop->index }}</td>
                                <td class="tal tit">
                                    <a href="{{ route('alarms.show', $alarm->id) }}">{{ $alarm->english_title ?: $alarm->korean_title }}</a>
                                </td>
                                <td class="file">
                                    @if($alarm->files && $alarm->files->count() > 0)
                                        <i></i>
                                    @endif
                                </td>
                                <td class="date">{{ \Carbon\Carbon::parse($alarm->created_at)->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="no_data">등록된 알람이 없습니다.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @include('components.board-pagination', ['paginator' => $alarms])
        </div>
        
    </div>
</div>

@include('components.user-footer')

@endsection
