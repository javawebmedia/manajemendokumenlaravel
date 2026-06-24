<div class="row">
	<div class="col-md-7">
		<form action="{{ url('admin/dokumen/cari') }}" method="get" accept-charset="utf-8">
			<div class="input-group mb-3">
				<input type="text" class="form-control" name="keywords" placeholder="Kata kunci pencarian..." value="{{ request('keywords') }}">
				<button type="submit" class="btn btn-dark">
					<i class="fa-solid fa-search"></i>
				</button>
				<?php if(isset($_GET['keywords']) || request()->segment(3) != '') { ?>
					<a href="<?php echo url('admin/dokumen') ?>" class="btn btn-primary"><i class="fa-solid fa-arrow-left"></i></a>
				<?php } ?>
				<button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#basicModalAlbum">
					<i class="fa-solid fa-plus-circle"></i> Buat Album
				</button>
				<button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#basicModal">
					<i class="fa-solid fa-plus-circle"></i> Unggah File
				</button>

			</div>
		</form>
	</div>
	<div class="col-md-5">
		<div class="justify-content-end">
			{{ $dokumen->links() }}
		</div>
	</div>
</div>
	


<table class="table table-bordered">
	<thead>
		<tr class="table-secondary">
			<th width="5%">No</th>
			<th width="25%">Dokumen</th>
			<th width="10%">Unit Kerja</th>
			<th width="10%">Jenis Dokumen</th>
			<th width="10%">Perkembangan &amp; Album</th>
			<th width="5%">Ekstensi</th>
			<th width="10%">Ukuran</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php $no = 1; foreach($dokumen as $item) { ?>
		<tr>
			<td><?php echo $no ?></td>
			<td><strong><a href="{{ url('admin/dokumen/detail/'.$item->kode_dokumen) }}"><?php echo $item->nama_dokumen ?></a></strong>
				<small class="text-muted">
					<br>Tanggal: <?php echo date('d-m-Y H:i:s',strtotime($item->created_at)) ?>
					<br>Oleh: <a href="{{ url('admin/dokumen/author/'.$item->created_by) }}"><?php echo $item->createdBy->nama ?></a>
					<br>Catatan: <em><?php echo $item->keterangan ?></em>
				</small>
			</td>
			<td>{{ $item->unitKerja?->nama_unit_kerja }}</td>
			<td><a href="{{ url('admin/dokumen/jenis/'.$item->id_jenis_dokumen) }}">
				{{ optional($item->jenisDokumen)->nama_jenis_dokumen ?? '-' }}
			</a>
				<br>
				<small>
					<em><a href="{{ url('admin/dokumen/sub-jenis/'.$item->id_jenis_dokumen) }}">
							{{ optional($item->subJenisDokumen)->nama_sub_jenis_dokumen ?? '-' }}
						</a></em>
				</small>
			</td>
			<td><a href="{{ url('admin/dokumen/perkembangan/'.$item->id_jenis_dokumen) }}">
				{{ optional($item->perkembangan)->nama_perkembangan ?? '-' }}
					
				</a><br>
				<small>
					<em><a href="{{ url('admin/dokumen/album/'.$item->id_jenis_dokumen) }}">{{ $item->album->nama_album ?? '-' }}</a></em>
				</small></td>
			<td><?php echo strtoupper($item->ekstensi_file) ?></td>
			<td><?php echo number_format($item->ukuran_file,'2') ?> MB</td>
			<td>
				
			    <button type="button" class="btn btn-success btn-sm mb-1" data-bs-toggle="modal" data-bs-target="#detailDokumen" data-id="{{ $item->kode_dokumen }}">
			        <i class="fa-solid fa-eye"></i> Pratinjau
			    </button>

			    <a href="<?php echo url('admin/dokumen/detail/'.$item->kode_dokumen) ?>" class="btn btn-primary btn-sm mb-1"><i class="fa-solid fa-eye"></i> Detail</a>
				
				<a href="<?php echo url('admin/dokumen/unduh/'.$item->kode_dokumen) ?>" class="btn btn-info btn-sm mb-1"><i class="fa-solid fa-download"></i></a>

				<?php if(Session()->get('id_unit_kerja') > 0) { 
					if(Session()->get('id_unit_kerja')==$item->id_unit_kerja) {
					?>

					<a href="<?php echo url('admin/dokumen/edit/'.$item->kode_dokumen) ?>" class="btn btn-warning btn-sm mb-1"><i class="fa-solid fa-edit"></i></a>
					<a href="<?php echo url('admin/dokumen/delete/'.$item->kode_dokumen) ?>" class="btn btn-dark btn-sm delete-link mb-1"><i class="fa-solid fa-trash"></i></a>

				<?php }}else{ ?>
					<a href="<?php echo url('admin/dokumen/edit/'.$item->kode_dokumen) ?>" class="btn btn-warning btn-sm mb-1"><i class="fa-solid fa-edit"></i></a>
					<a href="<?php echo url('admin/dokumen/delete/'.$item->kode_dokumen) ?>" class="btn btn-dark btn-sm delete-link mb-1"><i class="fa-solid fa-trash"></i></a>
				<?php } ?>
			</td>
		</tr>
		<?php $no++; } ?>
	</tbody>
</table>

<div class="justify-content-end mb-1 mt-1">
	{{ $dokumen->links() }}
</div>

@include('admin/dokumen/tambah')
@include('admin/dokumen/dokumen')
@include('admin/dokumen/tambah-album')

<script>
$(document).ready(function(){

    $('#detailDokumen').on('show.bs.modal', function (event) {

        var button = $(event.relatedTarget);
        var kode_dokumen = button.data('id');

        var modal = $(this);

        // daftar field yang akan diisi
        var fields = [
            'nama_dokumen',
            'nama_jenis_dokumen',
            'nama_sub_jenis_dokumen',
            'nama_perkembangan',
            'nama_album',
            'keterangan',
            'ekstensi_file',
            'ukuran_file',
            'hits',
            'unduh',
            'createdBy',
            'created_at'
        ];

        // tampilkan loading
        fields.forEach(function(field){
            modal.find('#'+field).html('<span class="text-muted">Loading...</span>');
        });

        modal.find('#nama_file').html('<div class="text-center p-3">Loading preview...</div>');

        // AJAX request
        $.ajax({
            url: '<?php echo url("admin/dokumen/show") ?>/' + kode_dokumen,
            type: 'GET',
            dataType: 'json',
            success: function(data){

                // isi data text
                fields.forEach(function(field){
                    if(data[field] !== null){
                        modal.find('#'+field).text(data[field]);
                    }else{
                        modal.find('#'+field).text('-');
                    }
                });

                // khusus HTML preview file
                modal.find('#nama_file').html(data.nama_file);

            },
            error: function(){
                modal.find('#nama_file').html(
                    '<div class="alert alert-danger">Gagal memuat dokumen</div>'
                );
            }

        });

    });

});
</script>