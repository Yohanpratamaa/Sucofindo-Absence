<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SetupController extends Controller
{
    /**
     * Show setup form untuk create super admin
     */
    public function showSetupForm()
    {
        // Check if super admin already exists
        $adminExists = Pegawai::where('role_user', 'super admin')->exists();
        
        if ($adminExists) {
            return redirect('/admin')->with('message', 'Super Admin sudah ada. Silakan login.');
        }

        return view('setup.create-admin');
    }

    /**
     * Process setup form
     */
    public function processSetup(Request $request)
    {
        // Check if super admin already exists
        $adminExists = Pegawai::where('role_user', 'super admin')->exists();
        
        if ($adminExists) {
            return redirect('/admin')->with('error', 'Super Admin sudah ada.');
        }

        // Validate request
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'npp' => 'required|string|unique:pegawais,npp|max:50',
            'email' => 'required|email|unique:pegawais,email|max:255',
            'password' => 'required|string|min:6|confirmed',
            'nik' => 'required|string|unique:pegawais,nik|max:20',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Create super admin
            $superAdmin = Pegawai::create([
                'nama' => $request->nama,
                'npp' => $request->npp,
                'email' => $request->email,
                'password' => $request->password, // Will be hashed by mutator
                'nik' => $request->nik,
                'status_pegawai' => 'PTT',
                'nomor_handphone' => $request->nomor_handphone ?? '08123456789',
                'status' => 'active',
                'role_user' => 'super admin',
                'alamat' => $request->alamat ?? 'Jakarta',
                'jabatan_nama' => 'System Administrator',
                'jabatan_tunjangan' => 0,
                'posisi_nama' => 'System Administrator',
                'posisi_tunjangan' => 0,
                'pendidikan_list' => [],
                'emergency_contacts' => [],
                'fasilitas_list' => [],
            ]);

            return redirect('/admin')->with('success', 'Super Admin berhasil dibuat! Silakan login dengan email: ' . $superAdmin->email);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat Super Admin: ' . $e->getMessage())->withInput();
        }
    }
}
