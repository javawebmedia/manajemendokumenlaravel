<form action="{{ asset('admin/dokumen/update') }}" enctype="multipart/form-data" method="post" accept-charset="utf-8" id="formUploadDokumen">
	{{ csrf_field() }}
	
<input type="hidden" name="id_dokumen" id="id_dokumen" value="{{ $dokumen->id_dokumen }}">
<input type="hidden" name="kode_dokumen" id="kode_dokumen" value="{{ $dokumen->kode_dokumen }}">

<div class="row">
	<div class="col-md-12">
		<p class="alert alert-warning">
			<strong>Perhatian!</strong> Silakan pilih tingkat perkembangan, jenis dokumen dan sub jenis dokumen terlebih dahulu baru kemudian memilih file yang akan Anda unggah. Lalu klik tombol <strong>Simpan dan Unggah</strong>. Jumlah Maksimal File dalam Sekali Unggah adalah <strong><?php echo $konfigurasi->jumlah_file_maksimal ?></strong> File. Ukuran Maksimal File adalah <strong><?php echo number_format($konfigurasi->ukuran_file_mb,2) ?></strong> MB. Jenis File Boleh Diunggah adalah <em><?php echo $konfigurasi->ekstensi_file ?></em>
		</p>
        
	</div>
	<div class="col-md-5">
        <?php if(Session()->get('id_unit_kerja') > 0) { ?>

            <div class="mb-3 alert alert-info">
            <small class="text-muted">Unit Kerja</small>
            <br>{{ $unitKerja->nama_unit_kerja }}
            </div>
            <input type="hidden" name="id_unit_kerja" value="{{ Session()->get('id_unit_kerja') }}">

        <?php }else{ ?>

            <div class="mb-3">
            <select name="id_unit_kerja" class="form-control" id="id_unit_kerja" style="width:100%;">
                <option value="">Pilih Unit Kerja</option>
                <?php foreach($unitKerja as $row) { ?>
                    <option value="<?php echo $row->id_unit_kerja ?>" <?php if($dokumen->id_unit_kerja == $row->id_unit_kerja) { echo 'selected'; } ?>><?php echo $row->nama_unit_kerja ?></option>
                <?php } ?>
            </select>
        </div>

        <?php } ?>

		<div class="mb-3">
			<select name="id_perkembangan" class="form-control" id="id_perkembangan" style="width:100%;" required>
				<?php foreach($perkembangan as $item) { ?>
					<option value="<?php echo $item->id_perkembangan ?>" <?php echo old('id_perkembangan', $dokumen->id_perkembangan ?? '') == $item->id_perkembangan ? 'selected' : '' ?>><?php echo $item->nama_perkembangan ?></option>
				<?php } ?>
			</select>
		</div>

		<div class="mb-3">
			<select name="id_jenis_dokumen" class="form-control" id="id_jenis_dokumen" style="width:100%;" required>
				<option value="">Pilih Jenis Dokumen</option>
				<?php foreach($JenisDokumen as $item) { ?>
					<option value="<?php echo $item->id_jenis_dokumen ?>" <?php echo old('id_jenis_dokumen', $dokumen->id_jenis_dokumen ?? '') == $item->id_jenis_dokumen ? 'selected' : '' ?>><?php echo $item->nama_jenis_dokumen ?></option>
				<?php } ?>
			</select>
		</div>
		
		<div class="mb-3">
			<select name="id_sub_jenis_dokumen" class="form-control"  id="id_sub_jenis_dokumen" required>
				<option value="">Pilih Sub Jenis Dokumen</option>
				<?php foreach($SubJenisDokumen as $item) { ?>
					<option value="<?php echo $item->id_sub_jenis_dokumen ?>" class="<?php echo $item->id_jenis_dokumen ?>" <?php echo old('id_sub_jenis_dokumen', $dokumen->id_sub_jenis_dokumen ?? '') == $item->id_sub_jenis_dokumen ? 'selected' : '' ?>><?php echo $item->nama_sub_jenis_dokumen ?></option>
				<?php } ?>
			</select>
		</div>

        <?php if(count($album) > 0) { ?>
        <div class="mb-3">
            <select name="id_album" class="form-control select2-modal" id="id_album" style="width:100%;">
                <option value="">Pilih Album</option>
                <?php foreach($album as $item) { ?>
                    <option value="<?php echo $item->id_album ?>" <?php echo old('id_album', $dokumen->id_album ?? '') == $item->id_album ? 'selected' : '' ?>><?php echo $item->nama_album ?></option>
                <?php } ?>
            </select>
        </div>
    <?php }else{ ?>
            <input type="hidden" name="id_album" value="0">
    <?php } ?>

		<div class="mb-3">
			<textarea name="keterangan" class="form-control" placeholder="Keterangan"></textarea>
		</div>

	</div>
	<div class="col-md-7">
		<p>Pilih File. Anda dapat mengupload banyak file sekaligus.</p>
		<div id="uploadArea" class="dropzone">
		    <div class="fallback">
		        <input name="file[]" type="file" multiple />
		    </div>
		</div>

        <div class="bg-light mt-3">
            <a href="{{ url('admin/dokumen') }}" class="btn btn-dark">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <button type="reset" name="reset" id="reset" class="btn btn-success " value="Reset">
                <i class="fa-solid fa-recycle"></i> Reset
            </button>
            <button type="submit" name="submit" id="submit" class="btn btn-primary " value="Simpan Data">
                <i class="fa-solid fa-save"></i> Simpan dan Unggah
            </button>
        </div>

	</div>
    <div class="col-md-12">

        <h4>Lihat Dokumen</h4>
        <?php
        $previewable = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf'];
        if(in_array(strtolower($dokumen->ekstensi_file), $previewable)) {
            $nama_file = '<iframe src="'.url('assets/upload/dokumen/'.$dokumen->tahun.'/'.$dokumen->thbl.'/'.$dokumen->nama_file).'"  height="450" style="width:100%;" allowfullscreen webkitallowfullscreen></iframe>';
        }else{
            $nama_file  = '<p class="alert alert-warning">
                                    <strong>Mohon Maaf</strong>, file tidak dapat dilakukan preview secara langsung. Hanya file <strong>PDF</strong> dan <strong>Gambar</strong> yang bisa langsung dipratinjau. Silakan unduh file secara langsung.
                                </p>

                                <p>
                                    <a href="'.url('admin/dokumen/unduh/'.$dokumen->kode_dokumen).'" class="btn btn-info mb-1 w-100"><i class="fa-solid fa-download"></i> Unduh File</a>
                                </p>';
        }
        echo $nama_file;
        ?>
    </div>
</div>
					


			
</form>
<?php 
$ekstensi_file      = '.' . implode(',.', array_map('trim', explode(',', $konfigurasi->ekstensi_file)));
?>
<script>
Dropzone.autoDiscover = false;
let myDropzone;
/* ==============================
   INIT DROPZONE
============================== */
function initDropzone(){
    myDropzone = new Dropzone("#uploadArea",{
        url: "{{ url('admin/dokumen/update') }}",
        autoProcessQueue:false,
        uploadMultiple:true,
        parallelUploads:1,
        paramName:"file",
        maxFilesize:<?php echo $konfigurasi->ukuran_file_mb ?>,
        acceptedFiles:"<?php echo $ekstensi_file ?>",
        addRemoveLinks:true,
        init:function(){
            let dz = this;
            dz.on("sendingmultiple",function(file,xhr,formData){
                formData.append("_token","{{ csrf_token() }}");
                formData.append("id_dokumen",$("#id_dokumen").val());
                formData.append("kode_dokumen",$("#kode_dokumen").val());
                formData.append("id_perkembangan",$("#id_perkembangan").val());
                formData.append("id_jenis_dokumen",$("#id_jenis_dokumen").val());
                formData.append("id_sub_jenis_dokumen",$("#id_sub_jenis_dokumen").val());
                formData.append("id_album",$("#id_album").val());
                formData.append("keterangan",$("textarea[name=keterangan]").val());

            });
            dz.on("successmultiple",function(files,response){
                Swal.fire({
                    icon:"success",
                    title:"Upload berhasil",
                    timer:1500,
                    showConfirmButton:false
                });
                window.location.href = '<?php echo url("admin/dokumen/edit/".$dokumen->kode_dokumen); ?>';

            });
            dz.on("errormultiple",function(files,response){

                Swal.fire({
                    icon:"error",
                    title:"Upload gagal",
                    text:"Terjadi kesalahan saat upload"
                });
            });
        }
    });
}

/* ==============================
   VALIDASI FORM
============================== */
function validasiUpload(){

    let jenis = $("#id_jenis_dokumen").val();
    let sub   = $("#id_sub_jenis_dokumen").val();
    let perkembangan   = $("#id_perkembangan").val();

    if(jenis === ""){
        Swal.fire({
            icon:"warning",
            title:"Jenis dokumen belum dipilih"
        });
        return false;
    }

    if(perkembangan === ""){
        Swal.fire({
            icon:"warning",
            title:"Tingkat perkembangan dokumen belum dipilih"
        });
        return false;
    }

    if(sub === ""){
        Swal.fire({
            icon:"warning",
            title:"Sub jenis dokumen belum dipilih"
        });
        return false;
    }

    if(myDropzone.getQueuedFiles().length === 0){
        Swal.fire({
            icon:"warning",
            title:"File belum dipilih"
        });
        return false;
    }

    return true;

}

/* ==============================
   SUBMIT UPLOAD
============================== */
function submitUpload(){

    $("#submit").click(function(e){
        e.preventDefault();
        if(validasiUpload()){
            myDropzone.processQueue();
        }
    });

}

/* ==============================
   CHAINED SELECT
============================== */
function chainedSelect(){
    $("#id_jenis_dokumen").change(function(){
        let jenis = $(this).val();
        $("#id_sub_jenis_dokumen option").hide();
        $("#id_sub_jenis_dokumen option:first").show();
        $("#id_sub_jenis_dokumen option."+jenis).show();
        $("#id_sub_jenis_dokumen").val("");

    });
}

/* ==============================
   RESET FORM + DROPZONE
============================== */
function resetForm(){
    $("#reset").on("click", function(e){
        e.preventDefault();
        // reset form HTML
        document.getElementById("formUploadDokumen").reset();
        // reset dropzone
        if(myDropzone){
            myDropzone.removeAllFiles(true);
        }
        // reset select option
        $("#id_perkembangan").val("");
        $("#id_sub_jenis_dokumen").val("");
        $("#id_sub_jenis_dokumen option").show();
    });

}


/* ==============================
   READY
============================== */
$(document).ready(function(){
    initDropzone();
    submitUpload();
    chainedSelect();
    resetForm();
});
</script>

