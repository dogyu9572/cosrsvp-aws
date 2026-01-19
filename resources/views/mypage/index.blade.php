@php
$gNum = "08";
$sNum = "01";
$gName = "MY PAGE";
@endphp

@extends('layouts.user')

@section('content')
<div id="mainContent" class="container mypage_wrap">
    @include('components.user-header')
    <div class="contents">
    
        <div class="stitle">{{ $gName }}<div class="location"><a href="{{ route('home') }}" class="home">Home</a><span><strong>{{ $gName }}</strong></span></div></div>
        
        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom: 20px; padding: 15px; background-color: #d4edda; color: #155724; border-radius: 4px;">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger" style="margin-bottom: 20px; padding: 15px; background-color: #f8d7da; color: #721c24; border-radius: 4px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('mypage.update') }}" method="POST">
            @csrf
            <div class="wbox">
            
                <div class="ntit"><span>1</span>My Information</div>
                <div class="board_write tbl border break_write">
                    <table>
                        <colgroup>
                            <col class="w170">
                            <col>
                            <col class="w170">
                            <col>
                        </colgroup>
                        <tr>
                            <th>Name</th>
                            <td>{{ $member->name }}</td>
                            <th>Project Class</th>
                            <td>
                                @php
                                    $projectClass = [];
                                    if ($member->projectTerm) $projectClass[] = $member->projectTerm->name;
                                    if ($member->course) $projectClass[] = $member->course->name_ko;
                                    if ($member->operatingInstitution) $projectClass[] = $member->operatingInstitution->name_ko;
                                    if ($member->projectPeriod) $projectClass[] = $member->projectPeriod->name_ko;
                                    if ($member->country) $projectClass[] = $member->country->name_ko;
                                @endphp
                                {{ implode(' / ', $projectClass) }}
                            </td>
                        </tr>
                        <tr>
                            <th>Gender</th>
                            <td>{{ $member->gender == 'male' ? 'Male' : ($member->gender == 'female' ? 'Female' : '') }}</td>
                            <th>Occupation</th>
                            <td><input type="text" name="occupation" class="text w100p" value="{{ old('occupation', $member->occupation) }}" placeholder="Please enter your occupation."></td>
                        </tr>
                        <tr>
                            <th>Date of Birth<span class="c_red">*</span></th>
                            <td><input type="text" name="birth_date" class="datepicker" value="{{ old('birth_date', $member->birth_date?->format('Y.m.d')) }}"></td>
                            <th>Major</th>
                            <td><input type="text" name="major" class="text w100p" value="{{ old('major', $member->major) }}" placeholder="Please enter your major."></td>
                        </tr>
                        <tr>
                            <th>Local Phone Number</th>
                            <td>{{ $member->phone_local }}</td>
                            <th>Affiliation</th>
                            <td><input type="text" name="affiliation" class="text w100p" value="{{ old('affiliation', $member->affiliation) }}" placeholder="Please enter your affiliation."></td>
                        </tr>
                        <tr>
                            <th>Korean Phone Number</th>
                            <td>{{ $member->phone_kr }}</td>
                            <th>Department</th>
                            <td><input type="text" name="department" class="text w100p" value="{{ old('department', $member->department) }}" placeholder="Please enter your department."></td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $member->email }}</td>
                            <th>Position</th>
                            <td><input type="text" name="position" class="text w100p" value="{{ old('position', $member->position) }}" placeholder="Please enter your position."></td>
                        </tr>
                        <tr>
                            <th>Passport Number<span class="c_red">*</span></th>
                            <td><input type="text" name="passport_number" class="text w100p" value="{{ old('passport_number', $member->passport_number) }}"></td>
                            <th>Alien Registration Number</th>
                            <td><input type="text" name="alien_registration_number" class="text w100p" value="{{ old('alien_registration_number', $member->alien_registration_number) }}" placeholder="Please enter your alien registration number. ex. 123456-1234567"></td>
                        </tr>
                        <tr>
                            <th>Passport Expiration Date<span class="c_red">*</span></th>
                            <td><input type="text" name="passport_expiry" class="datepicker" value="{{ old('passport_expiry', $member->passport_expiry?->format('Y.m.d')) }}"></td>
                            <th>Alien Registration Card Expiration Date</th>
                            <td><input type="text" name="alien_registration_expiry" class="datepicker" value="{{ old('alien_registration_expiry', $member->alien_registration_expiry?->format('Y.m.d')) }}"></td>
                        </tr>
                    </table>
                </div>
            
                <div class="ntit"><span>2</span>Training Information</div>
                <div class="board_write tbl border break_write">
                    <table>
                        <colgroup>
                            <col class="w170">
                            <col>
                            <col class="w170">
                            <col>
                        </colgroup>
                        <tr>
                            <th>Hotel</th>
                            <td>{{ $member->hotel_name }}</td>
                            <th>Training Period</th>
                            <td>{{ $member->training_period }}</td>
                        </tr>
                        <tr>
                            <th>Room Number</th>
                            <td>{{ $member->hotel_address_detail }}</td>
                            <th>Visa Type</th>
                            <td>{{ $member->visa_type }}</td>
                        </tr>
                        <tr>
                            <th>Cultural Experience</th>
                            <td>{{ $member->cultural_experience }}</td>
                            <th>Clothing Size<span class="c_red">*</span></th>
                            <td><input type="text" name="clothing_size" class="text w100p" value="{{ old('clothing_size', $member->clothing_size) }}" placeholder="Please enter your clothing size. Ex. XL"></td>
                        </tr>
                        <tr>
                            <th>Account Number</th>
                            <td>{{ $member->account_info }}</td>
                            <th>Special Dietary Needs</th>
                            <td><input type="text" name="dietary_restrictions" class="text w100p" value="{{ old('dietary_restrictions', $member->dietary_restrictions) }}" placeholder="Please enter your dietary restrictions. ex. pork, pork stock"></td>
                        </tr>
                        <tr>
                            <th>Insurance Subscription</th>
                            <td>{{ $member->insurance_status == 'yes' ? 'Subscription' : ($member->insurance_status == 'no' ? 'Not Subscription' : '') }}</td>
                            <th>Special Notes and Requests</th>
                            <td><input type="text" name="special_requests" class="text w100p" value="{{ old('special_requests', $member->special_requests) }}" placeholder="Please enter any special notes or requests. For example, holding a visa to stay in Korea."></td>
                        </tr>
                    </table>
                </div>
            
                <div class="ntit"><span>3</span>Immigration and Departure Information</div>
                <div class="board_write tbl border break_write">
                    <table>
                        <colgroup>
                            <col class="w170">
                            <col>
                            <col class="w170">
                            <col>
                        </colgroup>
                        <tr>
                            <th>Flight Ticket</th>
                            <td colspan="3">
                                <div class="flex gap32">
                                    @if($member->ticket_file)
                                        @php
                                            $storedFileName = basename($member->ticket_file);
                                            $originalFileName = preg_replace('/_\d+\./', '.', $storedFileName);
                                        @endphp
                                        <span>{{ $originalFileName }}</span>
                                    @else
                                        <span>-</span>
                                    @endif
                                    <p class="c_red">*Please check your flight ticket before entering the country.</p>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>Departure Location</th>
                            <td>{{ $member->departure_location }}</td>
                            <th>Arrival Location</th>
                            <td>{{ $member->arrival_location }}</td>
                        </tr>
                        <tr>
                            <th>Arrival Date</th>
                            <td>{{ $member->entry_date?->format('Y/m/d') }}</td>
                            <th>Departure Date</th>
                            <td>{{ $member->exit_date?->format('Y/m/d') }}</td>
                        </tr>
                        <tr>
                            <th>Account Number</th>
                            <td>{{ $member->account_info }}</td>
                            <th>Outbound Flight</th>
                            <td>{{ $member->exit_flight }}</td>
                        </tr>
                    </table>
                </div>
                
                <div class="board_bottom flex_center">
                    <button type="submit" class="btn_submit">Save</button>
                </div>
            </div>
        </form>
        
    </div>
</div>

@include('components.user-footer')

@push('scripts')
<script src="//code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
//달력
    $(".datepicker").datepicker({
        dateFormat: 'yy.mm.dd',
        showMonthAfterYear: true,
        showOn: "focus",
        changeYear: true,
        changeMonth: true,
        yearRange: 'c-100:c+10',
        yearSuffix: "년 ",
        monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
        dayNamesMin: ['일','월','화','수','목','금','토']
    });
});
//]]>
</script>
@endpush
@endsection
