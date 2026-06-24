<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\Konfigurasi;
use App\Models\JenisDokumen;
use App\Models\SubJenisDokumen;
use App\Models\Dokumen;
use App\Models\Album;
use App\Models\Perkembangan;
use App\Models\Users;
use App\Models\UnitKerja;

class DokumenController extends Controller
{
    // index
    public function index()
    {
        $konfigurasi    = Konfigurasi::first();
        $JenisDokumen       = JenisDokumen::orderBy('urutan','ASC')->get();
        $SubJenisDokumen    = SubJenisDokumen::orderBy('urutan','ASC')->get();
        $Album              = Album::orderBy('nama_album','ASC')->get();
        $Perkembangan       = Perkembangan::orderBy('urutan','ASC')->get();

        if(Session()->get('id_unit_kerja') > 0) {
            $unitKerja      = UnitKerja::where('id_unit_kerja',Session()->get('id_unit_kerja'))->first();
        }else{
            $unitKerja      = UnitKerja::orderBy('nama_unit_kerja','ASC')->get();
        }

        $dokumen            = Dokumen::with([
                                    'jenisDokumen',
                                    'subJenisDokumen',
                                    'perkembangan',
                                    'unitKerja',
                                    'album',
                                    'createdBy',
                                    'updatedBy'
                                ])
                                ->latest('created_at')
                                ->paginate($konfigurasi->pagination);
        // total dokumen
        $totalDokumen = Dokumen::count();

        $data = [   'title'             => 'Kelola Dokumen ('.$totalDokumen.')',
                    'dokumen'           => $dokumen,
                    'JenisDokumen'      => $JenisDokumen,
                    'SubJenisDokumen'   => $SubJenisDokumen,
                    'album'             => $Album,
                    'dokumen'           => $dokumen,
                    'konfigurasi'       => $konfigurasi,
                    'perkembangan'      => $Perkembangan,
                    'unitKerja'         => $unitKerja,
                    'content'           => 'admin.dokumen.index'
                ];
        return view('admin.layout.wrapper',$data);
    }

    // detail
    public function detail($kode_dokumen)
    {
        $konfigurasi        = Konfigurasi::first();

        $dokumen            = Dokumen::with([
                                    'jenisDokumen',
                                    'subJenisDokumen',
                                    'perkembangan',
                                    'unitKerja',
                                    'album',
                                    'createdBy',
                                    'updatedBy'
                                ])
                                ->latest('created_at')
                                ->where('kode_dokumen',$kode_dokumen)
                                ->first();


        $data = [   'title'             => $dokumen->nama_dokumen,
                    'dokumen'           => $dokumen,
                    'content'           => 'admin.dokumen.detail'
                ];
        return view('admin.layout.wrapper',$data);
    }

    // author
    public function author($created_by)
    {
        $konfigurasi        = Konfigurasi::first();
        $JenisDokumen       = JenisDokumen::orderBy('urutan','ASC')->get();
        $SubJenisDokumen    = SubJenisDokumen::orderBy('urutan','ASC')->get();
        $Album              = Album::orderBy('nama_album','ASC')->get();
        $Perkembangan       = Perkembangan::orderBy('urutan','ASC')->get();

        $dokumen            = Dokumen::with([
                                    'jenisDokumen',
                                    'subJenisDokumen',
                                    'perkembangan',
                                    'unitKerja',
                                    'album',
                                    'createdBy',
                                    'updatedBy'
                                ])
                                ->latest('created_at')
                                ->where('created_by',$created_by)
                                ->paginate($konfigurasi->pagination);
        // total dokumen
        $authorDetail       = Users::where('id_user',$created_by)->first();
        $totalDokumen       = Dokumen::where('created_by',$created_by)->count();

        $data = [   'title'             => 'Dokumen diunggah oleh: '.$authorDetail->nama.' ('.$totalDokumen.')',
                    'dokumen'           => $dokumen,
                    'JenisDokumen'      => $JenisDokumen,
                    'SubJenisDokumen'   => $SubJenisDokumen,
                    'album'             => $Album,
                    'dokumen'           => $dokumen,
                    'konfigurasi'       => $konfigurasi,
                    'perkembangan'      => $Perkembangan,
                    'content'           => 'admin.dokumen.index'
                ];
        return view('admin.layout.wrapper',$data);
    }

    // perkembangan
    public function perkembangan($id_perkembangan)
    {
        $konfigurasi        = Konfigurasi::first();
        $JenisDokumen       = JenisDokumen::orderBy('urutan','ASC')->get();
        $SubJenisDokumen    = SubJenisDokumen::orderBy('urutan','ASC')->get();
        $Album              = Album::orderBy('nama_album','ASC')->get();
        $Perkembangan       = Perkembangan::orderBy('urutan','ASC')->get();

        $dokumen            = Dokumen::with([
                                    'jenisDokumen',
                                    'subJenisDokumen',
                                    'perkembangan',
                                    'unitKerja',
                                    'album',
                                    'createdBy',
                                    'updatedBy'
                                ])
                                ->latest('created_at')
                                ->where('id_perkembangan',$id_perkembangan)
                                ->paginate($konfigurasi->pagination);
        // total dokumen
        $perkembanganDetail = Perkembangan::where('id_perkembangan',$id_perkembangan)->first();
        $totalDokumen       = Dokumen::where('id_perkembangan',$id_perkembangan)->count();

        $data = [   'title'             => $perkembanganDetail->nama_perkembangan.' ('.$totalDokumen.')',
                    'dokumen'           => $dokumen,
                    'JenisDokumen'      => $JenisDokumen,
                    'SubJenisDokumen'   => $SubJenisDokumen,
                    'album'             => $Album,
                    'dokumen'           => $dokumen,
                    'konfigurasi'       => $konfigurasi,
                    'perkembangan'      => $Perkembangan,
                    'content'           => 'admin.dokumen.index'
                ];
        return view('admin.layout.wrapper',$data);
    }

    // album
    public function album($id_album)
    {
        $konfigurasi        = Konfigurasi::first();
        $JenisDokumen       = JenisDokumen::orderBy('urutan','ASC')->get();
        $SubJenisDokumen    = SubJenisDokumen::orderBy('urutan','ASC')->get();
        $Album              = Album::orderBy('nama_album','ASC')->get();
        $Perkembangan       = Perkembangan::orderBy('urutan','ASC')->get();

        $dokumen            = Dokumen::with([
                                    'jenisDokumen',
                                    'subJenisDokumen',
                                    'perkembangan',
                                    'unitKerja',
                                    'album',
                                    'createdBy',
                                    'updatedBy'
                                ])
                                ->latest('created_at')
                                ->where('id_album',$id_album)
                                ->paginate($konfigurasi->pagination);
        // total dokumen
        $albumDetail    = Album::where('id_album',$id_album)->first();
        $totalDokumen   = Dokumen::where('id_album',$id_album)->count();

        $data = [   'title'             => $albumDetail->nama_album.' ('.$totalDokumen.')',
                    'dokumen'           => $dokumen,
                    'JenisDokumen'      => $JenisDokumen,
                    'SubJenisDokumen'   => $SubJenisDokumen,
                    'album'             => $Album,
                    'dokumen'           => $dokumen,
                    'konfigurasi'       => $konfigurasi,
                    'perkembangan'      => $Perkembangan,
                    'content'           => 'admin.dokumen.index'
                ];
        return view('admin.layout.wrapper',$data);
    }

    // jenisDokumen
    public function jenisDokumen($id_jenis_dokumen)
    {
        $konfigurasi        = Konfigurasi::first();
        $JenisDokumen       = JenisDokumen::orderBy('urutan','ASC')->get();
        $SubJenisDokumen    = SubJenisDokumen::orderBy('urutan','ASC')->get();
        $Album              = Album::orderBy('nama_album','ASC')->get();
        $Perkembangan       = Perkembangan::orderBy('urutan','ASC')->get();

        $dokumen            = Dokumen::with([
                                    'jenisDokumen',
                                    'subJenisDokumen',
                                    'perkembangan',
                                    'unitKerja',
                                    'album',
                                    'createdBy',
                                    'updatedBy'
                                ])
                                ->latest('created_at')
                                ->where('id_jenis_dokumen',$id_jenis_dokumen)
                                ->paginate($konfigurasi->pagination);
        // total dokumen
        $jenisDokumenDetail     = JenisDokumen::where('id_jenis_dokumen',$id_jenis_dokumen)->first();
        $totalDokumen           = Dokumen::where('id_jenis_dokumen',$id_jenis_dokumen)->count();

        $data = [   'title'             => $jenisDokumenDetail->nama_jenis_dokumen.' ('.$totalDokumen.')',
                    'dokumen'           => $dokumen,
                    'JenisDokumen'      => $JenisDokumen,
                    'SubJenisDokumen'   => $SubJenisDokumen,
                    'album'             => $Album,
                    'dokumen'           => $dokumen,
                    'konfigurasi'       => $konfigurasi,
                    'perkembangan'      => $Perkembangan,
                    'content'           => 'admin.dokumen.index'
                ];
        return view('admin.layout.wrapper',$data);
    }

    // subJenisDokumen
    public function subJenisDokumen($id_sub_jenis_dokumen)
    {
        $konfigurasi        = Konfigurasi::first();
        $JenisDokumen       = JenisDokumen::orderBy('urutan','ASC')->get();
        $SubJenisDokumen    = SubJenisDokumen::orderBy('urutan','ASC')->get();
        $Album              = Album::orderBy('nama_album','ASC')->get();
        $Perkembangan       = Perkembangan::orderBy('urutan','ASC')->get();

        $dokumen            = Dokumen::with([
                                    'jenisDokumen',
                                    'subJenisDokumen',
                                    'perkembangan',
                                    'unitKerja',
                                    'album',
                                    'createdBy',
                                    'updatedBy'
                                ])
                                ->latest('created_at')
                                ->where('id_sub_jenis_dokumen',$id_sub_jenis_dokumen)
                                ->paginate($konfigurasi->pagination);
        // total dokumen
        $subJenisDokumenDetail  = SubJenisDokumen::where('id_sub_jenis_dokumen',$id_sub_jenis_dokumen)->first();
        $totalDokumen           = Dokumen::where('id_sub_jenis_dokumen',$id_sub_jenis_dokumen)->count();

        $data = [   'title'             => $subJenisDokumenDetail->nama_sub_jenis_dokumen.' ('.$totalDokumen.')',
                    'dokumen'           => $dokumen,
                    'JenisDokumen'      => $JenisDokumen,
                    'SubJenisDokumen'   => $SubJenisDokumen,
                    'album'             => $Album,
                    'dokumen'           => $dokumen,
                    'konfigurasi'       => $konfigurasi,
                    'perkembangan'      => $Perkembangan,
                    'content'           => 'admin.dokumen.index'
                ];
        return view('admin.layout.wrapper',$data);
    }

    // index
    public function cari(Request $request)
    {
        $konfigurasi    = Konfigurasi::first();
        $keywords = $request->get('keywords');

        $JenisDokumen       = JenisDokumen::orderBy('urutan','ASC')->get();
        $SubJenisDokumen    = SubJenisDokumen::orderBy('urutan','ASC')->get();
        $Album              = Album::orderBy('nama_album','ASC')->get();
        $Perkembangan       = Perkembangan::orderBy('urutan','ASC')->get();

        $query = Dokumen::with([
                    'jenisDokumen',
                    'subJenisDokumen',
                    'perkembangan',
                    'unitKerja',
                    'album',
                    'createdBy',
                    'updatedBy'
                ]);

        // filter jika ada keywords
        if(!empty($keywords)) {
            $query->where(function($q) use ($keywords){
                $q->where('nama_dokumen','like','%'.$keywords.'%')
                  ->orWhere('kode_dokumen','like','%'.$keywords.'%')
                  ->orWhere('ekstensi_file','like','%'.$keywords.'%')
                  ->orWhere('ukuran_file','like','%'.$keywords.'%')
                  ->orWhere('thbl','like','%'.$keywords.'%')
                  ->orWhere('keterangan','like','%'.$keywords.'%');
            });
        }

        $dokumen = $query->latest('created_at')
                         ->paginate($konfigurasi->pagination)
                         ->withQueryString(); // supaya pagination tetap membawa keywords

        $totalDokumen = $dokumen->total();

        $data = [
            'title'             => 'Pencarian Dokumen: '.$keywords.' ('.$totalDokumen.')',
            'dokumen'           => $dokumen,
            'JenisDokumen'      => $JenisDokumen,
            'SubJenisDokumen'   => $SubJenisDokumen,
            'album'             => $Album,
            'dokumen'           => $dokumen,
            'konfigurasi'       => $konfigurasi,
            'perkembangan'      => $Perkembangan,
            'content'           => 'admin.dokumen.index'
        ];

        return view('admin.layout.wrapper',$data);
    }

    // edit
    public function edit($kode_dokumen)
    {
        $konfigurasi        = Konfigurasi::first();
        $dokumen            = Dokumen::where('kode_dokumen',$kode_dokumen)->first();
        $JenisDokumen       = JenisDokumen::orderBy('urutan','ASC')->get();
        $SubJenisDokumen    = SubJenisDokumen::orderBy('urutan','ASC')->get();
        $Album              = Album::orderBy('nama_album','ASC')->get();
        $Perkembangan       = Perkembangan::orderBy('urutan','ASC')->get();

        if(Session()->get('id_unit_kerja') > 0) {
            $unitKerja      = UnitKerja::where('id_unit_kerja',Session()->get('id_unit_kerja'))->first();
        }else{
            $unitKerja      = UnitKerja::orderBy('nama_unit_kerja','ASC')->get();
        }

        $data = [
            'title'             => 'Edit Dokumen: '.$dokumen->nama_dokumen,
            'dokumen'           => $dokumen,
            'JenisDokumen'      => $JenisDokumen,
            'SubJenisDokumen'   => $SubJenisDokumen,
            'album'             => $Album,
            'dokumen'           => $dokumen,
            'konfigurasi'       => $konfigurasi,
            'perkembangan'      => $Perkembangan,
            'unitKerja'         => $unitKerja,
            'content'           => 'admin.dokumen.edit'
        ];

        return view('admin.layout.wrapper',$data);
    }

    // show
    public function show($kode_dokumen)
    {
        $dokumen            = Dokumen::with([
                                    'jenisDokumen',
                                    'subJenisDokumen',
                                    'perkembangan',
                                    'unitKerja',
                                    'album',
                                    'createdBy',
                                    'updatedBy'
                                ])
                                ->where('kode_dokumen',$kode_dokumen)
                                ->first();
                                
        Dokumen::where('kode_dokumen',$kode_dokumen)->update(['hits' => $dokumen->hits+1]);

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
        
        $data = [   'id_dokumen'                => $dokumen->id_dokumen,
                    'nama_dokumen'              => $dokumen->nama_dokumen,
                    'nama_file'                 => $nama_file,
                    'nama_perkembangan'         => $dokumen->perkembangan->nama_perkembangan ?? '-', 
                    'nama_jenis_dokumen'        => $dokumen->jenisDokumen->nama_jenis_dokumen ?? '-',
                    'nama_sub_jenis_dokumen'    => $dokumen->subJenisDokumen->nama_sub_jenis_dokumen ?? '-',
                    'nama_album'                => $dokumen->album->nama_album ?? '-',
                    'createdBy'                 => $dokumen->createdBy->nama ?? '-',
                    'updatedBy'                 => $dokumen->updatedBy->nama ?? '-',
                    'keterangan'                => $dokumen->keterangan,
                    'created_at'                => $dokumen->created_at,
                    'updated_at'                => $dokumen->updated_at,
                    'tahun'                     => $dokumen->tahun,
                    'thbl'                      => $dokumen->thbl,
                    'hits'                      => $dokumen->hits,
                    'unduh'                     => $dokumen->unduh,
                    'ekstensi_file'             => $dokumen->ekstensi_file,
                    'ukuran_file'               => number_format($dokumen->ukuran_file,'2').' MB',
                    'ukuran_aktual'             => $dokumen->ukuran_file
                ];
        return response()->json($data);
    }

    // unduh
    public function unduh($kode_dokumen)
    {
        $dokumen            = Dokumen::where('kode_dokumen',$kode_dokumen)->first();
        Dokumen::where('kode_dokumen',$kode_dokumen)->update(['unduh' => $dokumen->unduh+1]);
        $path = './assets/upload/dokumen/'.$dokumen->tahun.'/'.$dokumen->thbl.'/'.$dokumen->nama_file;
        return response()->download($path);
    }

    // delete
    public function delete($kode_dokumen)
    {
        $dokumen    = Dokumen::where('kode_dokumen',$kode_dokumen)->first();
        $path       = './assets/upload/dokumen/'.$dokumen->tahun.'/'.$dokumen->thbl.'/'.$dokumen->nama_file;
        // cek file ada atau tidak
        if (file_exists($path)) {
            unlink($path);
        }
        // hapus data database
        $dokumen->delete();
        return redirect('admin/dokumen')->with(['sukses' => 'Data telah dihapus']);
    }

    // tambah
    public function tambah(Request $request)
    {
        $konfigurasi = Konfigurasi::first();

        $rules = [
            'id_jenis_dokumen'     => 'required',
            'id_sub_jenis_dokumen' => 'required',
            'file'                 => 'required',
            'file.*'               => 'file|mimes:'.$konfigurasi->ekstensi_file.'|max:'.$konfigurasi->ukuran_file_kb
        ];

        $request->validate($rules);
        if ($request->hasFile('file')) {
            foreach ($request->file('file') as $file) {
                $tahun          = date('Y');
                $thbl           = date('Ym');
                $pathTahun      = './assets/upload/dokumen/'.$tahun;
                $pathThbl       = './assets/upload/dokumen/'.$tahun.'/'.$thbl;
                // cek folder tahun
                if (!file_exists($pathTahun)) {
                    mkdir($pathTahun, 0775, true);
                    chmod($pathTahun, 0775);
                }
                // cek folder thbl
                if (!file_exists($pathThbl)) {
                    mkdir($pathThbl, 0775, true);
                    chmod($pathThbl, 0775);
                }
                // proses file
                $ukuran         = $file->getSize();
                $ext            = $file->getClientOriginalExtension();
                $namaAsli       = $file->getClientOriginalName();
                $nama_dokumen   = $this->namaDokumen($namaAsli);
                $nama_file      = $tahun.'_'.$thbl.'_'.Str::random(10).'.'.$ext;
                // pindahkan file
                $file->move($pathThbl, $nama_file);

                Dokumen::create([
                    'id_jenis_dokumen'     => $request->id_jenis_dokumen,
                    'id_sub_jenis_dokumen' => $request->id_sub_jenis_dokumen,
                    'id_perkembangan'      => $request->id_perkembangan,
                    'id_album'             => $request->id_album,
                    'id_unit_kerja'        => $request->id_unit_kerja,
                    'kode_dokumen'         => strtoupper(Str::random(12)),
                    'nama_dokumen'         => $nama_dokumen,
                    'keterangan'           => $request->keterangan,
                    'nama_file'            => $nama_file,
                    'ekstensi_file'        => $ext,
                    'ukuran_file'          => round($ukuran/1024/1024,2),
                    'tahun'                => $tahun,
                    'thbl'                 => $thbl,
                    'created_by'           => auth()->user()->id_user,
                    'created_at'           => now()
                ]);
            }
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Upload berhasil'
        ]);
    }

    // update
    public function update(Request $request)
    {
        $konfigurasi = Konfigurasi::first();

        $rules = [
            'id_jenis_dokumen'     => 'required',
            'id_sub_jenis_dokumen' => 'required',
            'file'                 => 'required',
            'file.*'               => 'file|mimes:'.$konfigurasi->ekstensi_file.'|max:'.$konfigurasi->ukuran_file_kb
        ];

        $request->validate($rules);
        if ($request->hasFile('file')) {
            foreach ($request->file('file') as $file) {
                $tahun          = date('Y');
                $thbl           = date('Ym');
                $pathTahun      = './assets/upload/dokumen/'.$tahun;
                $pathThbl       = './assets/upload/dokumen/'.$tahun.'/'.$thbl;
                // cek folder tahun
                if (!file_exists($pathTahun)) {
                    mkdir($pathTahun, 0775, true);
                    chmod($pathTahun, 0775);
                }
                // cek folder thbl
                if (!file_exists($pathThbl)) {
                    mkdir($pathThbl, 0775, true);
                    chmod($pathThbl, 0775);
                }
                // proses file
                $ukuran         = $file->getSize();
                $ext            = $file->getClientOriginalExtension();
                $namaAsli       = $file->getClientOriginalName();
                $nama_dokumen   = $this->namaDokumen($namaAsli);
                $nama_file      = $tahun.'_'.$thbl.'_'.Str::random(10).'.'.$ext;
                // pindahkan file
                $file->move($pathThbl, $nama_file);

                Dokumen::where('id_dokumen',$request->id_dokumen)->update([
                    'id_jenis_dokumen'     => $request->id_jenis_dokumen,
                    'id_sub_jenis_dokumen' => $request->id_sub_jenis_dokumen,
                    'id_perkembangan'      => $request->id_perkembangan,
                    'id_album'             => $request->id_album,
                    'id_unit_kerja'        => $request->id_unit_kerja,
                    'nama_dokumen'         => $nama_dokumen,
                    'keterangan'           => $request->keterangan,
                    'nama_file'            => $nama_file,
                    'ekstensi_file'        => $ext,
                    'ukuran_file'          => round($ukuran/1024/1024,2),
                    'tahun'                => $tahun,
                    'thbl'                 => $thbl,
                    'created_by'           => auth()->user()->id_user,
                    'created_at'           => now()
                ]);
            }
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Upload berhasil'
        ]);
    }

    // Nama Dokumen
    function namaDokumen($namaAsli)
    {
        // buang ekstensi file
        $namaFile = pathinfo($namaAsli, PATHINFO_FILENAME);
        // ganti underscore dan strip menjadi spasi
        $namaFile = str_replace(['_', '-'], ' ', $namaFile);
        // hilangkan spasi ganda
        $namaFile = preg_replace('/\s+/', ' ', $namaFile);
        // trim spasi di awal dan akhir
        $namaFile = trim($namaFile);
        // ubah jadi title case
        return ucwords(strtolower($namaFile));
    }

    // tambahAlbum
    public function tambahAlbum(Request $request)
    {
        $request->validate([
            'nama_album' => 'required|max:255',
            'keterangan' => 'nullable',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        $namaFile = null;

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $namaFile = time().'_'.$file->getClientOriginalName();
            $file->move('./assets/upload/image/', $namaFile);
        }

        Album::create([
            'id_unit_kerja' => auth()->user()->id_unit_kerja ?? 1,
            'slug_album' => strtolower(Str::random(5).'-'.Str::slug($request->nama_album)),
            'nama_album' => $request->nama_album,
            'keterangan' => $request->keterangan,
            'gambar' => $namaFile,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
            'deleted_by' => 0
        ]);
        return redirect('admin/dokumen')->with(['sukses' => 'Data album telah ditambahkan']);
    }

}
