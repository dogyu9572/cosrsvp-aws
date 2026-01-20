@extends('layouts.login')

@section('content')
<div id="mainContent" class="container mem_wrap flex_center">
	
	<div class="inputs">
		<div class="logo"></div>
		
		@if ($errors->any())
			<div style="background: #fee; border: 1px solid #fcc; border-radius: 6px; padding: 12px; margin-bottom: 20px; color: #c00; font-size: 14px;">
				@foreach ($errors->all() as $error)
					<p style="margin: 0 0 4px 0;">{{ $error }}</p>
				@endforeach
			</div>
		@endif
		
		<form method="POST" action="{{ route('kofih.login') }}">
			@csrf
			
			<input type="text" name="code" class="text w100p" placeholder="Please enter CODE." value="{{ old('code') }}" required autofocus>
			
			<button type="submit" class="btn">Login</button>
		</form>
		
		<div class="btm"><span class="c_red">※</span> This system is available only to users who have been approved as participants in the Cosmojin training program after logging in.</div>
		<div class="gbox">
			<strong>Related Inquiries</strong>
			<ul>
				<li class="i1">02-318-0345</li>
				<li class="i2">booking@cosmojin.com</li>
			</ul>
		</div>
	</div>
	
</div>
@endsection