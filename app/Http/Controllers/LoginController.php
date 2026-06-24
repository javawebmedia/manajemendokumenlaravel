<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use App\Models\Konfigurasi;
use App\Models\Users;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

    // index
    public function index()
    {
        $konfigurasi = Konfigurasi::listing();
        $randomString = strtoupper(Str::random(5)); // Generate random alphanumeric
        session()->put('captcha', $randomString);     // Simpan di session
        $this->createCaptcha($randomString);

        $data = [
            'title'         => 'Login Administrator',
            'konfigurasi'   => $konfigurasi,
            'captcha'       => url('assets/images/captcha/' . $randomString . '.png'),
            'randomString'  => $randomString,
            'content'       => 'login.index'
        ];
        return view('login.wrapper', $data);
    }


    // prosesLogin
    public function prosesLogin(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
            'captcha'  => 'required'
        ]);
        // cek captcha
        if (strtoupper($request->captcha) !== strtoupper(session('captcha'))) {
            return redirect('login')->withInput()->with('warning', 'Captcha salah.');
        }
        $user = Users::where('username', $request->username)->first();
        if (!$user) {
            return redirect('login')
                ->withInput($request->except('password'))
                ->with('warning', 'Username atau password salah');
        }
        // cek status user
        if ($user->status_user == 'Non Aktif') {
            return redirect('login')->with('warning', 'Akun Anda Non Aktif.');
        }
        $password = $request->password;
        // ==============================
        // CASE 1: Password sudah bcrypt
        // ==============================
        if (!empty($user->password_hash)) {
            if (Hash::check($password, $user->password_hash)) {
                Auth::login($user);
                $request->session()->regenerate();
                // set session
                $request->session()->put([
                    'id_user'           => $user->id_user,
                    'nama'              => $user->nama,
                    'id_akses_level'    => $user->id_akses_level,
                    'akses_level'       => $user->akses_level,
                    'id_unit_kerja'     => $user->id_unit_kerja,
                    'username'          => $user->username
                ]);
                session()->forget('captcha');
                return redirect()->to('admin/dasbor')->with('sukses', 'Login Berhasil');
            }
        } 
        // ==============================
        // CASE 2: Password masih SHA1
        // ==============================
        else {
            if ($user->password === sha1($password)) {
                // upgrade ke bcrypt
                $user->update([
                    'password_hash' => Hash::make($password)
                ]);
                Auth::login($user);
                $request->session()->regenerate();
                // set session
                $request->session()->put([
                    'id_user'           => $user->id_user,
                    'nama'              => $user->nama,
                    'id_akses_level'    => $user->id_akses_level,
                    'akses_level'       => $user->akses_level,
                    'id_unit_kerja'     => $user->id_unit_kerja,
                    'username'          => $user->username
                ]);
                session()->forget('captcha');
                return redirect()->to('admin/dasbor')->with('sukses', 'Login Berhasil');
            }
        }
        return redirect('login')
            ->withInput($request->except('password'))
            ->with('warning', 'Username atau password salah');
    }

    // prosesLogin
    public function prosesLoginx(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
            'captcha'  => 'required'
        ]);

        if (strtoupper($request->captcha) !== strtoupper(session('captcha'))) {
            return redirect('login')->withInput()->with('warning', 'Captcha salah.');
        }

        $user = Users::where('username', $request->username)->first();

        if ($user) {
            if ($user->status_user == 'Non Aktif') {
                return redirect('login')->with('warning', 'Akun Anda Non Aktif.');
            }

            // Cek apakah password SHA1
            if (strlen($user->password) == 40) {
                if ($user->password === sha1($request->password)) {
                    // Update ke Bcrypt agar aman
                    $user->password = Hash::make($request->password);
                    $user->save();

                    Auth::login($user);
                    $request->session()->regenerate();
                    return redirect()->intended('admin/dasbor');
                }
            }

            // Cek login standar (Bcrypt)
            if (Hash::check($request->password, $user->password)) {
                // Pastikan user diloginkan secara utuh
                Auth::login($user);

                $request->session()->regenerate();
                session()->forget('captcha');

                // Coba redirect ke URL statis untuk memastikan tidak ada cache redirect
                return redirect()->to('admin/dasbor')->with('sukses', 'Login Berhasil');
            }
        }

        return redirect('login')->withInput($request->except('password'))->with('warning', 'Username atau password salah');
    }

    /**
     * Helper untuk menangani redirect setelah sukses login
     */
    protected function authenticated(Request $request)
    {
        $user = Auth::user();

        if ($user->status_user == 'Non Aktif') {
            Auth::logout();
            return redirect('login')->with('warning', 'Akun Anda Non Aktif.');
        }

        $request->session()->regenerate();
        session()->forget('captcha');

        if ($request->redirect_page == '') {
            return redirect()->intended('admin/dasbor')->with('sukses', 'Login Berhasil');
        }

        return redirect($request->redirect_page)->with('sukses', 'Anda berhasil login');
    }

    // resetPassword
    public function resetPassword()
    {
        $konfigurasi = Konfigurasi::listing();
        $data = [
            'title'        => 'Reset Password',
            'konfigurasi'  => $konfigurasi,
            'content'      => 'login.reset-password'
        ];
        return view('login.wrapper', $data);
    }

    // gantiPassword
    public function gantiPassword($TokenRahasia)
    {
        $konfigurasi = Konfigurasi::listing();
        $data = [
            'title'        => 'Ganti Password',
            'konfigurasi'  => $konfigurasi,
            'content'      => 'login.ganti-password'
        ];
        return view('login.wrapper', $data);
    }

    // prosesReset
    public function prosesReset(Request $request) {}

    // prosesGantiPassword
    public function prosesGantiPassword(Request $request) {}

    // refreshChaptcha
    public function refreshChaptcha(Request $request): JsonResponse
    {
        $randomString = strtoupper(Str::random(5)); // generate kode
        session()->put('captcha', $randomString);   // simpan ke session
        $this->createCaptcha($randomString);
        return response()->json([
            'message' => 'Captcha telah direfresh',
            'captcha' => url('assets/images/captcha/' . $randomString . '.png')
        ]);
    }

    // createCaptcha
    public function createCaptcha($randomString)
    {
        // Hapus captcha lama (>10 menit)
        $files = File::glob('assets/images/captcha/*');
        $threshold = strtotime('-10 minutes');
        foreach ($files as $file) {
            if (is_file($file) && $threshold >= filemtime($file)) {
                unlink($file);
            }
        }
        // Load gambar source PNG
        $sourcePath = 'assets/images/source/captcha.png';
        $image = imagecreatefrompng($sourcePath);
        // Alokasi warna hitam untuk teks
        $black = imagecolorallocate($image, 0, 0, 0);
        // Tentukan font path (TTF)
        $fontPath = 'assets/font/Libre_Baskerville/LibreBaskerville-Regular.ttf';
        // Hitung posisi text (center horizontal, near bottom vertical)
        $fontSize = 25;
        $bbox = imagettfbbox($fontSize, 0, $fontPath, $randomString);
        $textWidth = abs($bbox[2] - $bbox[0]);
        $textHeight = abs($bbox[7] - $bbox[1]);
        $x = (imagesx($image) - $textWidth) / 2;
        $y = imagesy($image) - 10; // 10 px dari bawah
        // Tulis teks ke gambar
        imagettftext($image, $fontSize, 0, $x, $y, $black, $fontPath, $randomString);
        // Simpan gambar captcha
        $savePath = 'assets/images/captcha/' . $randomString . '.png';
        imagepng($image, $savePath);
        imagedestroy($image);
    }

    // logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('login')->with('sukses', 'Anda sudah logout');
    }

    public function migrasiKeBcrypt()
    {
        $users = \App\Models\Users::all();
        $count = 0;

        foreach ($users as $user) {
            // Cek apakah password sudah Bcrypt (panjang bcrypt biasanya 60 karakter)
            // Jika panjangnya masih 40 (SHA1), maka update
            if (strlen($user->password) == 40) {
                // Kita tidak bisa dpt password asli dari SHA1, 
                // Jadi sementara kita set password sama dengan username atau '123456'
                // ATAU jika Anda tahu passwordnya, ganti manual.
                $user->update([
                    'password' => Hash::make($user->username) // Contoh: password jadi sama dengan username
                ]);
                $count++;
            }
        }

        return "Berhasil mengupdate $count user ke Bcrypt. Password default sekarang sama dengan Username masing-masing.";
    }

    // logic
    public function logicLoginSatu(Request $request)
    {
        $username   = $request->username;
        $password   = $request->password;
        // ambil data user
        $checkUserSha1      = Users::where([    'username'  => $username,
                                            'password'  => sha1($password)
                                        ])->first();
        $checkUserBcript    = Users::where([    'username'  => $username,
                                            'password'  => bcrypt($password)
                                        ])->first();
        // proses check
        if($checkUserSha1) {
            Users::where('id_user', $checkUserSha1->id_user)->update(['password'  => bcrypt($password)]);
            // create session
            // Coba redirect ke URL statis untuk memastikan tidak ada cache redirect
            return redirect()->to('admin/dasbor')->with('sukses', 'Login Berhasil');
        }else{
            if($checkUserBcript) {
                // create session
                return redirect()->to('admin/dasbor')->with('sukses', 'Login Berhasil');
            }else{
                // create session
                return redirect()->to('login')->with('sukses', 'Login gagal');
            }
        }
    }


    // logicDua
    public function logicLoginDua(Request $request)
    {
        $username   = $request->username;
        $password   = $request->password;
        // ambil data user
        $user       = Users::where('username',$username)->first();
        // password_has kosong atau null
        if($users->password_has == '' || $users->password_has == null) {
            $checkUserSha1      = Users::where([    'username'  => $username,
                                            'password'  => sha1($password)
                                        ])->first();
            if($checkUserSha1) {
                Users::where('id_user', $checkUserSha1->id_user)->update(['password_has'  => bcrypt($password)]);
                // create session
                // Coba redirect ke URL statis untuk memastikan tidak ada cache redirect
                return redirect()->to('admin/dasbor')->with('sukses', 'Login Berhasil');
            }else{
               return redirect()->to('login')->with('sukses', 'Login gagal');
            }
        }else{
            $checkUserBcript    = Users::where([    'username'  => $username,
                                                    'password'  => bcrypt($password)
                                        ])->first();
            if($checkUserBcript) {
                // create session
                return redirect()->to('admin/dasbor')->with('sukses', 'Login Berhasil');
            }else{
                // create session
                return redirect()->to('login')->with('sukses', 'Login gagal');
            }
        }
    }

    // logicLoginTiga
    public function logicLoginTiga(Request $request)
    {
        $username = $request->username;
        $password = $request->password;

        $user = Users::where('username', $username)->first();

        if (!$user) {
            return redirect()->to('login')->with('error','Login gagal');
        }

        // CASE 1: sudah pakai bcrypt
        if (!empty($user->password_hash)) {
            if (Hash::check($password, $user->password_hash)) {
                // create session
                return redirect()->to('admin/dasbor')->with('sukses','Login berhasil');
            }
        } 
        // CASE 2: masih SHA1
        else {

            if ($user->password === sha1($password)) {

                // upgrade password ke bcrypt
                $user->update([
                    'password_hash' => Hash::make($password)
                ]);

                // create session
                return redirect()->to('admin/dasbor')->with('sukses','Login berhasil');
            }

        }

        return redirect()->to('login')->with('error','Login gagal');
    }

}
