<p class="alert alert-warning">
			Halo <strong><?php echo session()->get('nama') ?></strong>. Selamat datang di <strong><em>{{ $konfigurasi->namaweb }}</em></strong>. 
		</p>

@include('admin/dasbor/statistik')

<h1>Sekilas tentang <em>{{ $konfigurasi->namaweb }}</em></h1>	
<hr>
<div class="row">
	<div class="col-md-3">
		<img src="{{ asset('assets/upload/image/'.$siteConfig->gambar) }}" alt="{{ $siteConfig->namaweb }}" class="img-fluid img-thumbnail">
	</div>
	<div class="col-md-9">
		<?php echo $konfigurasi->tentang ?>	
	</div>
</div>

{{ session('id_unit_kerja') }}
