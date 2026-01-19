@extends('layouts.login')

@section('content')
<div id="mainContent" class="container mem_wrap flex_center">
	
	<div class="inputs">
		<div class="tit">Find ID</div>
		<p class="tb tac">Your ID is as follows.<br/>If you don't remember your password, you can receive a temporary password.</p>
		<div class="gbox id_end">{{ $loginId }}</div>
		<div class="btns_btm flex_center">
			<button type="button" class="btn btn_kwg" onclick="location.href='{{ route('login') }}'">Cancel</button>
			<button type="button" class="btn btn_wkk" onclick="location.href='{{ route('find-pw') }}'">find password</button>
		</div>
	</div>
	
</div>
@endsection
