<form action="{{ asset('admin/dokumen/tambah') }}" enctype="multipart/form-data" method="post" accept-charset="utf-8" id="formUploadDokumen">
	{{ csrf_field() }}

	<div class="modal fade" id="basicModal" tabindex="-1">
		<div class="modal-dialog modal-xl">
			<div class="modal-content">
				<div class="modal-header bg-light">

					<h4 class="modal-title" id="myModalLabel">Unggah Dokumen Baru</h4>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">

					<div class="row">
						<div class="col-md-12">
							<p class="alert alert-warning">
								<strong>Perhatian!</strong> Silakan pilih tingkat perkembangan, jenis dokumen dan sub jenis dokumen terlebih dahulu baru kemudian memilih file yang akan Anda unggah. Lalu klik tombol <strong>Simpan dan Unggah</strong>. Jumlah Maksimal File dalam Sekali Unggah adalah <strong><?php echo $konfigurasi->jumlah_file_maksimal ?></strong> File. Ukuran Maksimal File adalah <strong><?php echo number_format($konfigurasi->ukuran_file_mb,2) ?></strong> MB. Jenis File Boleh Diunggah adalah <em><?php echo $konfigurasi->ekstensi_file ?></em>
							</p>
                            
						</div>
						<div class="col-md-4">
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
                                        <option value="<?php echo $row->id_unit_kerja ?>"><?php echo $row->nama_unit_kerja ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <?php } ?>
							<div class="mb-3">
								<select name="id_perkembangan" class="form-control" id="id_perkembangan" style="width:100%;" required>
									<?php foreach($perkembangan as $item) { ?>
										<option value="<?php echo $item->id_perkembangan ?>"><?php echo $item->nama_perkembangan ?></option>
									<?php } ?>
								</select>
							</div>

							<div class="mb-3">
								<select name="id_jenis_dokumen" class="form-control" id="id_jenis_dokumen" style="width:100%;" required>
									<option value="">Pilih Jenis Dokumen</option>
									<?php foreach($JenisDokumen as $item) { ?>
										<option value="<?php echo $item->id_jenis_dokumen ?>"><?php echo $item->nama_jenis_dokumen ?></option>
									<?php } ?>
								</select>
							</div>
							
							<div class="mb-3">
								<select name="id_sub_jenis_dokumen" class="form-control"  id="id_sub_jenis_dokumen" required>
									<option value="">Pilih Sub Jenis Dokumen</option>
									<?php foreach($SubJenisDokumen as $item) { ?>
										<option value="<?php echo $item->id_sub_jenis_dokumen ?>" class="<?php echo $item->id_jenis_dokumen ?>"><?php echo $item->nama_sub_jenis_dokumen ?></option>
									<?php } ?>
								</select>
							</div>

                            <?php if(count($album) > 0) { ?>
                            <div class="mb-3">
                                <select name="id_album" class="form-control select2-modal" id="id_album" style="width:100%;">
                                    <option value="">Pilih Album</option>
                                    <?php foreach($album as $item) { ?>
                                        <option value="<?php echo $item->id_album ?>"><?php echo $item->nama_album ?></option>
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
						<div class="col-md-8">
							<p>Pilih File. Anda dapat mengupload banyak file sekaligus.</p>
							<div id="uploadArea" class="dropzone">
							    <div class="fallback">
							        <input name="file[]" type="file" multiple />
							    </div>
							</div>

						</div>
					</div>
					



				</div>
				<div class="modal-footer d-flex justify-content-end bg-light">
					<button type="button" class="btn btn-dark" data-bs-dismiss="modal">
						<i class="fa-solid fa-times-circle"></i> Close
					</button>
					<button type="reset" name="reset" id="reset" class="btn btn-success " value="Reset">
						<i class="fa-solid fa-recycle"></i> Reset
					</button>
					<button type="submit" name="submit" id="submit" class="btn btn-primary " value="Simpan Data">
						<i class="fa-solid fa-save"></i> Simpan dan Unggah
					</button>
				</div>
			</div>
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
        url: "{{ url('admin/dokumen/tambah') }}",
        autoProcessQueue:false,
        uploadMultiple:true,
        parallelUploads:<?php echo $konfigurasi->jumlah_file_maksimal ?>,
        paramName:"file",
        maxFilesize:<?php echo $konfigurasi->ukuran_file_mb ?>,
        acceptedFiles:"<?php echo $ekstensi_file ?>",
        addRemoveLinks:true,
        init:function(){
            let dz = this;
            dz.on("sendingmultiple",function(file,xhr,formData){
                formData.append("_token","{{ csrf_token() }}");
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
                setTimeout(function(){
                    location.reload();
                },1500);

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

