<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;
use App\Models\Pegawai;
use App\Models\Role;
use App\Models\User;
use App\Models\JabatanFungsional;
use App\Models\PangkatGolongan;
use App\Models\UserManage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pegawai::with('user.roles');

        // SEARCH
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%$search%")
                    ->orWhere('nik', 'like', "%$search%")
                    ->orWhere('nip', 'like', "%$search%")
                    ->orWhere('nidn', 'like', "%$search%");
            });
        }

        // FILTER STATUS
        if ($request->filled('status')) {
            $query->where('status_pegawai', $request->status);
        }

        $perPage = $request->get('per_page', 10);

        if ($perPage == 'all') {
            $pegawais = $query->get();
        } else {
            $pegawais = $query->paginate($perPage)->withQueryString();
        }

        return view('Operator.manajemen_akun.index', compact('pegawais'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();

        $jabfungs = JabatanFungsional::all();

        $pangkats = PangkatGolongan::all();

        return view('Operator.manajemen_akun.tambah', compact(
            'roles',
            'jabfungs',
            'pangkats'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'nik' => 'nullable|string|max:16|unique:PEGAWAI,nik',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'nomor_hp' => 'nullable|string|max:20',
            'nomor_hp_darurat' => 'nullable|string|max:20',
            'jurusan' => 'nullable|string|max:255',
            'prodi' => 'nullable|string|max:255',
            'nidn' => 'nullable|string|max:10',
            'nip' => 'nullable|string|max:18|unique:PEGAWAI,nip',
            'status_pegawai' => 'required|in:PNS,Non PNS',

            'id_jabfung' => 'nullable|exists:JABATAN_FUNGSIONAL,id_jabfung',
            'id_panggol' => 'nullable|exists:PANGKAT_GOLONGAN,id_panggol',

            'email' => 'required|email|unique:USER_MANAGE,email',

            'roles' => 'nullable|array',
            'roles.*' => 'exists:ROLE,id_role',
        ]);



        $foto = null;

        if ($request->hasFile('foto')) {
            $foto = $request->file('foto')
                ->store('foto_pegawai', 'public');
        }

        $pegawai = Pegawai::create([
            'nama_lengkap' => $request->nama_lengkap,
            'foto' => $foto,
            'nik' => $request->nik,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'nomor_hp' => $request->nomor_hp,
            'nomor_hp_darurat' => $request->nomor_hp_darurat,
            'jurusan' => $request->jurusan,
            'prodi' => $request->prodi,
            'nidn' => $request->nidn,
            'nip' => $request->nip,
            'status_pegawai' => $request->status_pegawai,
            'id_jabfung' => $request->id_jabfung,
            'id_panggol' => $request->id_panggol,
        ]);

        $user = UserManage::create([
            'id_pegawai' => $pegawai->id_pegawai,
            'email' => $request->email,
            'password' => Hash::make($request->nik ?? 'password123'),
        ]);

        if ($request->roles) {
            $user->roles()->sync($request->roles);
        }

        return redirect()
            ->route('operator.manajemen_akun.index')->with('success', 'Data berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $pegawai = Pegawai::with([
            'user.roles',
            'jabatanFungsional',
            'pangkatGolongan'
        ])->findOrFail($id);

        return view('Operator.manajemen_akun.detail', compact('pegawai'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pegawai = Pegawai::with('user.roles')->findOrFail($id);

        $jabfungs = JabatanFungsional::all();
        $pangkats = PangkatGolongan::all();
        $roles = Role::all();

        return view('Operator.manajemen_akun.edit', compact(
            'pegawai',
            'jabfungs',
            'pangkats',
            'roles'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pegawai = Pegawai::with('user')->findOrFail($id);

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',

            'nik' => [
                'nullable',
                'string',
                'max:16',
                Rule::unique('PEGAWAI', 'nik')->ignore($pegawai->id_pegawai, 'id_pegawai')
            ],

            'tanggal_lahir' => 'nullable|date',

            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',

            'nomor_hp' => 'nullable|string|max:20',

            'nomor_hp_darurat' => 'nullable|string|max:20',

            'email' => [
                'required',
                'email',
                Rule::unique('USER_MANAGE', 'email')
                    ->ignore($pegawai->user?->id_user, 'id_user')
            ],

            'jurusan' => 'nullable|string|max:255',
            'prodi' => 'nullable|string|max:255',

            'status_pegawai' => 'required|in:PNS,Non PNS',

            'nip' => [
                'nullable',
                'string',
                Rule::unique('PEGAWAI', 'nip')
                    ->ignore($pegawai->id_pegawai, 'id_pegawai')
            ],

            'nidn' => 'nullable|string|max:10',

            'id_jabfung' => 'nullable|exists:JABATAN_FUNGSIONAL,id_jabfung',

            'id_panggol' => 'nullable|exists:PANGKAT_GOLONGAN,id_panggol',

            'password' => 'nullable|min:6',

            'roles' => 'nullable|array',

            'roles.*' => 'exists:ROLE,id_role',
        ]);

        $pegawai->update([
            'nama_lengkap' => $request->nama_lengkap,
            'nik' => $request->nik,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'nomor_hp' => $request->nomor_hp,
            'nomor_hp_darurat' => $request->nomor_hp_darurat,
            'jurusan' => $request->jurusan,
            'prodi' => $request->prodi,
            'nip' => $request->nip,
            'nidn' => $request->nidn,
            'status_pegawai' => $request->status_pegawai,
            'id_jabfung' => $request->id_jabfung,
            'id_panggol' => $request->id_panggol,
        ]);

        if ($request->hasFile('foto')) {
            // HAPUS foto lama dulu sebelum simpan baru
            if ($pegawai->foto && Storage::disk('public')->exists($pegawai->foto)) {
                Storage::disk('public')->delete($pegawai->foto);
            }
            $foto = $request->file('foto')->store('foto_pegawai', 'public');
            $pegawai->update(['foto' => $foto]);
        }

        $user = $pegawai->user;

        if ($user) {

            $user->update([
                'email' => $request->email,

                'password' => $request->password
                    ? Hash::make($request->password)
                    : $user->password,
            ]);

            if ($request->roles) {
                $user->roles()->sync($request->roles);
            }
        }

        return redirect()
            ->route('operator.manajemen_akun.index')
            ->with('success', 'Data pegawai berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pegawai = Pegawai::findOrFail($id);

        // Hapus foto dari storage
        if ($pegawai->foto && Storage::disk('public')->exists($pegawai->foto)) {
            Storage::disk('public')->delete($pegawai->foto);
        }

        $pegawai->delete();

        return redirect()
            ->route('operator.manajemen_akun.index')
            ->with('success', 'Data pegawai berhasil dihapus');
    }
}
