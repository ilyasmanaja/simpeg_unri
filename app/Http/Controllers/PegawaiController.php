<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PegawaiController extends Controller
{
    public function show(int $id)
    {
        $pegawai = Pegawai::findOrFail($id);

        return view('dosen.data_diri.detail', compact('pegawai'));
    }

    public function edit(int $id)
    {
        $pegawai = Pegawai::findOrFail($id);
        return view('dosen.data_diri.edit', compact('pegawai'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'nomor_hp'         => 'required|max:20',
            'nomor_hp_darurat' => 'required|max:20',
        ]);

        $pegawai = Pegawai::findOrFail($id);
        $pegawai->update($request->only(['nomor_hp', 'nomor_hp_darurat']));

        return redirect()->route('pegawai.show', $id)->with('success', 'Data berhasil diperbarui.');
    }

    public function passwordForm(int $id)
    {
        $pegawai = Pegawai::findOrFail($id);
        return view('dosen.password', compact('pegawai'));
    }

    public function passwordUpdate(Request $request, int $id)
    {
        $request->validate([
            'password_lama'       => 'required',
            'password_baru'       => 'required|min:8',
            'password_konfirmasi' => 'required|same:password_baru',
        ]);

        /** @var \App\Models\UserManage $user */
        $user = Auth::user();

        if (!Hash::check($request->password_lama, $user->password)) {
            return back()
                ->withErrors(['password_lama' => 'Password lama tidak sesuai.'])
                ->withInput();
        }

        $user->password = Hash::make($request->password_baru);
        $user->save();

        return redirect()
            ->route('pegawai.show', $id)
            ->with('success', 'Password berhasil diperbarui.');
    }
}