<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index(){
        $document = Document::all();
        return response()->json($document);
    }
    public function uploadFile(Request $request)
    {
        // Validasi file dan input lainnya
        $request->validate([
            // 'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048', // Sesuaikan dengan kebutuhan
            'document_name' => 'required|string', // Nama dokumen
        ]);

        // Simpan file ke folder public/uploads
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName(); // Nama file unik
            $filePath = $file->storeAs('invoice', $fileName, 'public'); // Simpan di public/invoice

            // Simpan data ke tabel documents
            $document = Document::create([
                'document_path' => '/storage/' . $filePath, // Path file yang dapat diakses
                'document_file' => $fileName, // Nama file
                'document_name' => $request->document_name, // Nama dokumen dari input
            ]);

            // Kembalikan respons
            return response()->json([
                'message' => 'File berhasil diunggah!',
                'document' => $document, // Data dokumen yang disimpan
            ], 200);
        }

        return response()->json([
            'message' => 'File gagal diunggah!',
        ], 400);
    }

    public function show($filename) 
    {
        $path = storage_path('app/public/invoice/' . $filename);

        if (!file_exists($path)) {
            abort(404, 'file not found');
        }

        return response()->file($path);
    }
}
