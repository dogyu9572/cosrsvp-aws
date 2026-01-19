@extends('layouts.login')

@section('content')
<div id="mainContent" class="container mem_wrap flex_center">
	
	<div class="inputs">
		<div class="tit">Find ID</div>
		<div class="tab_area">
			<a href="{{ route('find-id') }}" class="on">Find ID</a>
			<a href="{{ route('find-pw') }}">find password</a>
		</div>
		
		@if ($errors->any())
			<div style="background: #fee; border: 1px solid #fcc; border-radius: 6px; padding: 12px; margin-bottom: 20px; color: #c00; font-size: 14px;">
				@foreach ($errors->all() as $error)
					<p style="margin: 0 0 4px 0;">{{ $error }}</p>
				@endforeach
			</div>
		@endif
		
		<form method="POST" action="{{ route('find-id') }}">
			@csrf
			
			<div class="tt">Name<span class="c_red">*</span></div>
			<input type="text" name="name" class="text w100p" placeholder="Please enter your name." value="{{ old('name') }}" required autofocus>
			
			<div class="tt">degree program<span class="c_red">*</span></div>
			<select name="project_term_id" id="project_term_id" class="text w100p" required>
				<option value="">Please select a degree program.</option>
				@foreach($projectTerms as $projectTerm)
					<option value="{{ $projectTerm->id }}" {{ old('project_term_id') == $projectTerm->id ? 'selected' : '' }}>{{ $projectTerm->name }}</option>
				@endforeach
			</select>
			
			<div class="tt">E-Mail<span class="c_red">*</span></div>
			<input type="email" name="email" class="text w100p" placeholder="Please enter your email address." value="{{ old('email') }}" required>
			
			<div class="btns_btm flex_center">
				<button type="button" class="btn btn_kwg" onclick="location.href='{{ route('login') }}'">Cancel</button>
				<button type="submit" class="btn btn_wkk">Confirm</button>
			</div>
		</form>
	</div>
	
</div>
@endsection
