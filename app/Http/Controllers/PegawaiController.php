<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PegawaiController extends Controller
{
    public function create()
    {
        /** @var \App\Models\UserManage $user */
        $user = Auth::user();

        // Mengambil relasi pegawai dari model UserManage. 
        // Jika belum ada relasi (belum isi data), buat objek Pegawai kosong agar tidak error di HTML
        $pegawai = $user->pegawai ?? new \App\Models\Pegawai();

        return view('dosen.data-diri.create', compact('pegawai'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_lahir' => 'required|date',
            'nomor_hp' => 'required|max:20',
            'nomor_hp_darurat' => 'required|max:20',
        ]);

        /** @var \App\Models\UserManage $user */
        $user = Auth::user();

        // Cek apakah user ini sudah punya data pegawai. Kalau belum, buat instans baru.
        if ($user->id_pegawai) {
            $pegawai = \App\Models\Pegawai::findOrFail($user->id_pegawai);
        } else {
            $pegawai = new \App\Models\Pegawai();
        }

        $pegawai->tanggal_lahir = $request->tanggal_lahir;
        $pegawai->nomor_hp = $request->nomor_hp;
        $pegawai->nomor_hp_darurat = $request->nomor_hp_darurat;
        $pegawai->save();

        // PENTING: Jika ini adalah pengisian data pertama kali (sebelumnya kosong),
        // kita harus memperbarui tabel user_manage agar menyimpan id_pegawai yang baru saja dibuat.
        if (!$user->id_pegawai) {
            $user->id_pegawai = $pegawai->id_pegawai; // Mengambil ID dari pegawai yang baru di-save
            $user->save();
        }

        return redirect()->route('dosen.datadiri.index')->with('success', 'Data berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $pegawai = Pegawai::findOrFail($id);

        // Sudah benar: diarahkan ke update.blade.php sesuai struktur folder kamu
        return view('dosen.data-diri.update', compact('pegawai'));
    }

    public function update(Request $request, int $id)
    {
        // Tambahkan validasi untuk tanggal lahir
        $request->validate([
            'tanggal_lahir' => 'required|date',
            'nomor_hp' => 'required|max:20',
            'nomor_hp_darurat' => 'required|max:20',
        ]);

        $pegawai = Pegawai::findOrFail($id);

        // Tambahkan 'tanggal_lahir' ke dalam daftar data yang diupdate
        $pegawai->update($request->only(['tanggal_lahir', 'nomor_hp', 'nomor_hp_darurat']));

        return redirect()->route('dosen.datadiri.index')->with('success', 'Data berhasil diperbarui.');
    }

    // Fungsi show dihapus/dikomentari karena file detail.blade.php tidak ada di folder kamu
    // public function show(int $id) 
    // {
    //     $pegawai = Pegawai::findOrFail($id);
    //     return view('dosen.data-diri.detail', compact('pegawai'));
    // }

    public function passwordForm(int $id)
    {
        $pegawai = Pegawai::findOrFail($id);
        return view('dosen.password', compact('pegawai'));
    }

    public function passwordUpdate(Request $request, int $id)
    {
        $request->validate([
            'password_lama' => 'required',
            'password_baru' => 'required|min:8',
            'password_konfirmasi' => 'required|same:password_baru',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->password_lama, $user->password)) {
            return back()
                ->withErrors(['password_lama' => 'Password lama tidak sesuai.'])
                ->withInput();
        }

        $user->password = Hash::make($request->password_baru);
        $user->save();

        // Redirect diubah ke datadiri.index karena fungsi show/detail sudah tidak digunakan
        return redirect()
            ->route('datadiri.index')
            ->with('success', 'Password berhasil diperbarui.');
    }
}