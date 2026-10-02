@if(session('success'))<div class="fi-notice fi-green" role="status">{{ session('success') }}</div>@endif
@if(session('error'))<div class="fi-notice fi-red" role="alert">{{ session('error') }}</div>@endif
@if($errors->any())<div class="fi-notice fi-red" role="alert"><strong>Informations à corriger</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
