<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Dropfile;

class DropFileController extends Controller
{
    public function __construct()
    {
        $this->dropbox = Storage::disk('dropbox')->getDriver()->getAdapter()->getClient();
    }

    public function index()
    {
        $files = Dropfile::all();
        return view('pages.drop-index', compact('files'));
    }

    public function store(Request $request)
    {
        try {
            if ($request->hasFile('file')) {
                $files = $request->file('file');

                foreach ($files as $file) {
                    $fileExt = $file->getClientOriginalExtension();
                    $mimeType = $file->getClientMimeType();
                    $fileSize = $file->getClientSize();
                    $newName = uniqid() . '.' . $fileExt;

                    Storage::disk('dropbox')->putFileAs('public/uploads/', $file, $newName);
                    $this->dropbox->createdSharedLinkWithSettings('public/uploads/' . $newName);

                    Dropfile::create([
                        'file_title' => $newName,
                        'file_type' => $mimeType,
                        'file_size' => $fileSize,
                    ]);
                }

                return redirect('drop');
            }
        } catch (\Exception $e) {
            return "Message: . {$e->getMessage()}";
        }
    }

    public function show($fileTitle)
    {
        try {
            $link = $this->dropbox->listSharedLinks('public/uploads/' . $fileTitle);
            $raw = explode("?", $link[0]['url']);
            $path = $raw[0] . '?raw=1';
            $tempPath = tempnam(sys_get_temp_dir(), $path);
            $copy = copy($path, $tempPath);

            return response()->file($tempPath);
        } catch (\Exception $e) {
            return abort(404);
        }
    }

    public function download($fileTitle)
    {
        try {
            return Storage::disk('dropbox')->download('public/uploads/' . $fileTitle);
        } catch (\Exception $e) {
            return abort(404);
        }
    }

    public function destroy($id)
    {
        try {
            $file = Dropfile::find($id);
            Storage::disk('dropbox')->delete('public/uploads/' . $file->file_title);
            $file->delete();

            return redirect('drop');
        } catch (\Exception $e) {
            return abort(404);
        }
    }
}
