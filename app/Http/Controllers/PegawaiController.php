<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PegawaiController extends Controller
{
    public function show(int $id)
    {
        $pegawai = Pegawai::findOrFail($id);
        
        return view('dosen atau tendik.data_diri.detail', compact('pegawai'));
    }

    public function edit(int $id)
    {
        $pegawai = Pegawai::findOrFail($id);
        return view('dosen atau tendik.data_diri.edit', compact('pegawai'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'nomor_hp' => 'required|max:20',
            'nomor_hp_darurat' => 'required|max:20',
            'alamat' => 'required|max:255',
        ]);

        $pegawai = Pegawai::findOrFail($id);
        $pegawai->update($request->only(['nomor_hp', 'nomor_hp_darurat', 'alamat']));

        return redirect()->route('pegawai.show', $id);
    }

    public function passwordForm(int $id)
{
    $pegawai = Pegawai::findOrFail($id);
    return view('dosen atau tendik.data_diri.password', compact('pegawai'));
}

public function passwordUpdate(Request $request, int $id)
{
}
}