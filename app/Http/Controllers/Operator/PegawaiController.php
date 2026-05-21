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

            // NIK → wajib angka & maksimal 16 digit
            'nik' => [
                'nullable',
                'digits_between:1,16',
                'unique:PEGAWAI,nik'
            ],

            'tanggal_lahir' => 'nullable|date',

            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',

            // No HP → wajib angka
            'nomor_hp' => [
                'nullable',
                'digits_between:1,20'
            ],

            // No HP Darurat → wajib angka
            'nomor_hp_darurat' => [
                'nullable',
                'digits_between:1,20'
            ],

            // jurusan HARUS string, bukan numeric
            'jurusan' => 'nullable|string|max:255',

            'prodi' => 'nullable|string|max:255',

            // NIDN → angka max 10 digit
            'nidn' => [
                'nullable',
                'digits_between:1,10'
            ],

            // NIP → angka max 18 digit
            'nip' => [
                'nullable',
                'digits_between:1,18',
                'unique:PEGAWAI,nip'
            ],

            'status_pegawai' => 'required|in:PNS,Non PNS',

            'id_jabfung' => 'nullable|exists:JABATAN_FUNGSIONAL,id_jabfung',

            'id_panggol' => 'nullable|exists:PANGKAT_GOLONGAN,id_panggol',

            'email' => 'required|email|unique:USER_MANAGE,email',

            'roles' => 'nullable|array',
            'roles.*' => 'exists:ROLE,id_role',
        ], [

            // =========================
            // CUSTOM MESSAGE
            // =========================

            'nik.digits_between' => 'NIK harus berupa angka dan maksimal 16 digit.',
            'nik.unique' => 'NIK sudah terdaftar.',

            'nomor_hp.digits_between' => 'Nomor HP harus berupa angka.',

            'nomor_hp_darurat.digits_between' => 'Nomor HP darurat harus berupa angka.',

            'nidn.digits_between' => 'NIDN harus berupa angka dan maksimal 10 digit.',

            'nip.digits_between' => 'NIP harus berupa angka dan maksimal 18 digit.',
            'nip.unique' => 'NIP sudah terdaftar.',

            'email.unique' => 'Email sudah digunakan.',

            'foto.image' => 'File foto harus berupa gambar.',
            'foto.mimes' => 'Foto harus format JPG, JPEG, atau PNG.',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
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
                'digits_between:1,16',
                Rule::unique('PEGAWAI', 'nik')->ignore($pegawai->id_pegawai, 'id_pegawai')
            ],
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'nomor_hp' => ['nullable', 'digits_between:1,20'],
            'nomor_hp_darurat' => ['nullable', 'digits_between:1,20'],
            'email' => [
                'required',
                'email',
                Rule::unique('USER_MANAGE', 'email')->ignore($pegawai->user?->id_user, 'id_user')
            ],
            'jurusan' => 'nullable|string|max:255',
            'prodi' => 'nullable|string|max:255',
            'status_pegawai' => 'required|in:PNS,Non PNS',
            'nip' => [
                'nullable',
                'digits_between:1,18', // Disamakan dengan store() demi konsistensi
                Rule::unique('PEGAWAI', 'nip')->ignore($pegawai->id_pegawai, 'id_pegawai')
            ],
            'nidn' => ['nullable', 'digits_between:1,10'],
            'id_jabfung' => 'nullable|exists:JABATAN_FUNGSIONAL,id_jabfung',
            'id_panggol' => 'nullable|exists:PANGKAT_GOLONGAN,id_panggol',
            'password' => 'nullable|min:6',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:ROLE,id_role',
        ], [
            // Samakan / rapikan custom messages sesuai aturan digits_between
            'nik.digits_between' => 'NIK harus berupa angka dan maksimal 16 digit.',
            'nip.digits_between' => 'NIP harus berupa angka dan maksimal 18 digit.',
            'nomor_hp.digits_between' => 'Nomor HP harus berupa angka (maksimal 20 digit).',
            'nomor_hp_darurat.digits_between' => 'Nomor HP darurat harus berupa angka.',
            // ... sisa pesan lainnya tetap sama
        ]);

        // Proteksi Backend Tambahan: Jika Non PNS, bersihkan NIP/NIDN/Pangkat jika memang bisnis rules-nya begitu
        $dataPegawai = $request->only([
            'nama_lengkap',
            'nik',
            'tanggal_lahir',
            'jenis_kelamin',
            'nomor_hp',
            'nomor_hp_darurat',
            'jurusan',
            'prodi',
            'nip',
            'nidn',
            'status_pegawai',
            'id_jabfung',
            'id_panggol'
        ]);

        if ($request->status_pegawai === 'Non PNS') {
            // Misal: Non PNS tidak punya NIP atau Pangkat Golongan PNS
            $dataPegawai['nip'] = null;
            $dataPegawai['id_panggol'] = null;
        }

        $pegawai->update($dataPegawai);

        if ($request->hasFile('foto')) {
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
                'password' => $request->password ? Hash::make($request->password) : $user->password,
            ]);

            if ($request->roles) {
                $user->roles()->sync($request->roles);
            } else {
                $user->roles()->detach(); // Antisipasi jika semua role dikosongkan saat update
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
