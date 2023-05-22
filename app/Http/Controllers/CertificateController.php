<?php

namespace App\Http\Controllers;

use App\Certificate;
use App\User;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
// use Illuminate\Http\Request;
use Inertia\Inertia;

class CertificateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($user_id)
    {
        // dd($user_id);
        $data['user'] = User::findOrFail($user_id);
        $arrayRole = [1, 3]; // admin

        // cek jika bukan admin
        if (!in_array(Auth::user()->role, $arrayRole)) {
            // cek jika user biasa ubah id dari url
            if (Auth::id() != $data['user']->id)
                return Redirect::back()->with('error', 'Kamu tidak memiliki akses ke sertifikat orang lain');
        }

        $data['filters'] = Request::all('search');
        $data['certificates'] =  Certificate::filter(Request::only('search'))
            ->orderBy('nama')
            ->where('user_id', $user_id)
            ->paginate()
            ->transform(function ($certificate) {
                return [
                    'id' => $certificate->id,
                    'user' => $certificate->user,
                    'nama' => $certificate->nama,
                    'keterangan' => $certificate->keterangan,
                    'tahun' => $certificate->tahun_sertifikat,
                    'desc_certificate' => $certificate->desc_certificate,
                    'file' => $certificate->file,
                ];
            });

        return Inertia::render('Certificates/Index', $data);
    }

    public function create($user_id)
    {
        $data['user'] = User::find($user_id);
        return Inertia::render('Certificates/Create', $data);
    }


    public function store(\Illuminate\Http\Request $request)
    {
        // dd($request->desc_certificate);
        $rules = [
            'nama' => ['required'],
            'keterangan' => ['required'],
            'file' => ['required'],
            'tahun_sertifikat' => ['required'],
            'desc_certificate' => ['required'],
        ];
        $messages = [
            'nama.required' => 'Nama sertifikat tidak boleh kosong',
            'keterangan.required' => 'Keterangan tidak boleh kosong',
            'file.required' => 'File tidak boleh kosong',
            'tahun_sertifikat.required' => 'Tahun sertifikat tidak boleh kosong',
            'desc_certificate.required' => 'Description Certificate tidak boleh kosong',
        ];
        $request->validate($rules, $messages);

        $certificate =Certificate::create([
            'user_id' => Auth::id(),
            'nama' => $request->nama,
            'keterangan' => $request->keterangan,
            'tahun_sertifikat' => $request->tahun_sertifikat,
            'desc_certificate' => $request->desc_certificate,
            'file' => Certificate::setFile($request->file),
        ]);

        return Redirect::route('certificates.index', $certificate->user_id)->with('success', 'Berhasil menambah sertifikat baru.');
    }

    public function downloadFile(Certificate $certificate)
    {
        // dd($certificate);

        $path = public_path('/files/certificates/' . $certificate->file);
        $header = [
            'Content-Type' => 'application/*',
        ];
        return response()->download($path, $certificate->file, $header);
    }

    public function show(Certificate $certificate)
    {
        //
    }

    public function edit(Certificate $certificate)
    {
        // dd($certificate);
        if ($certificate->user_id != Auth::id())
            return Redirect::back()->with('error', 'Anda tidak memiliki akses ke sertifikat ini');
        $data['certificate'] = $certificate;

        return Inertia::render('Certificates/Edit', $data);
    }


    public function update(\Illuminate\Http\Request $request, Certificate $certificate)
    {
        // dd($request->all());
        $rules = [
            'nama' => ['required'],
            'keterangan' => ['required'],
            'tahun_sertifikat' => ['required'],
        ];
        $messages = [
            'nama.required' => 'Nama sertifikat tidak boleh kosong',
            'keterangan.required' => 'Keterangan tidak boleh kosong',
            'tahun_sertifikat.required' => 'Tahun sertifikat tidak boleh kosong',
        ];
        $request->validate($rules, $messages);

        if ($request->hasFile('file')) {
            if (!empty($certificate->file)) {
                $path = public_path('files/certificates/' . $certificate->file);
                if (!empty($path))
                    unlink($path);
            }
            $newFile = $certificate->setFile($request->file);
        }

        $certificate->update([
            'nama' => $request->nama,
            'keterangan' => $request->keterangan,
            'tahun_sertifikat' => $request->tahun_sertifikat,
            'desc_certificate' => $request->desc_certificate,
            'file' => $request->hasFile('file') ? $newFile : $certificate->file,
        ]);

        return Redirect::route('certificates.index', $certificate->user_id)->with('success', 'Berhasil memperbarui sertifikat.');
    }

    public function destroy(Certificate $certificate)
    {
        // dd($certificate);
        if (!empty($certificate->file)) {
            $path = public_path('files/certificates/' . $certificate->file);
            if (!empty($path))
                unlink($path);
        }
        $user_id = $certificate->user_id;
        $certificate->delete();

        return Redirect::route('certificates.index', $user_id)->with('success', 'Berhasil menghapus sertifikat');
    }
}
