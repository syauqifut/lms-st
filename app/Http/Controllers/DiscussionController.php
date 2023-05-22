<?php

namespace App\Http\Controllers;

use App\CourseModule;
use App\User;
use App\Discussion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class DiscussionController extends Controller
{
    public function index($course_module_id)
    {
        $data['courseModule'] = CourseModule::find($course_module_id);

        /// from helpers
        if (checkAccessCourse($data['courseModule']->course_id, Auth::id()) ===  false) {
            return Redirect::route('courses.index');
        };

        $data['filters'] = Request::all('search');
        $data['discussions'] = Discussion::filter(Request::only('search'))
            ->where('course_module_id', $course_module_id)
            ->whereNull('parent_discuss_id')
            ->whereIn('is_active', [0, 1])
            ->orderBy('title')
            ->paginate()
            ->transform(function ($discussion) {
                return [
                    'id' => $discussion->id,
                    'title' => $discussion->title,
                    'discuss' => $discussion->discuss,
                    'file_attachment' => $discussion->file_attachment,
                    'user' => $discussion->user,
                    'course_module' => $discussion->courseModule,
                    'is_active' => $discussion->is_active,
                    'cekExt' => $this->cekExtension($discussion->file_attachment),
                    'viewEmbed' => $this->previewFileDiscussion($discussion->file_attachment),
                ];
            });
        // dd($data['discussions']);
        return Inertia::render('Discussions/Index', $data);
    }

    private function cekExtension($file_attachment)
    {
        $ext = pathinfo($file_attachment, PATHINFO_EXTENSION);
        $result = '';
        if (course_unit_show_width_google($ext)) {
            $result = 'document';
        } elseif ($ext == 'pdf') {
            $result =  'pdf';
        } elseif (in_array($ext, ['jpg', 'jpeg', 'png'])) {
            $result =  'gambar';
        } elseif ($ext == 'zip') {
            $result =  'zip';
        } else {
            $result =  'No preview available';
        }
        return $result;
    }

    private function previewFileDiscussion($file_attachment)
    {
        $ext = pathinfo($file_attachment, PATHINFO_EXTENSION);
        $contentPath = url('/') . "/files/discussions/" . $file_attachment;
        $embedPreview = '';

        if (course_unit_show_width_google($ext)) {
            $embedPreview = "https://view.officeapps.live.com/op/embed.aspx?src=" . $contentPath;
        } elseif ($ext == 'pdf') {
             // if server Offline
            // $embedPreview = "http://docs.google.com/gview?url=http://infolab.stanford.edu/pub/papers/google.pdf&embedded=true";

            $embedPreview = "https://docs.google.com/viewer?url=" . $contentPath . "&embedded=true";
        } else {
            $embedPreview = $contentPath;
        }

        return $embedPreview;
    }
    public function create($course_module_id)
    {
        $course_module = CourseModule::where('id', $course_module_id)->first();

        if ($course_module) {
            $data['courseModule'] = $course_module;
            return Inertia::render('Discussions/Create', $data);
        }
        return Redirect::back()->with('error', 'Anda tidak memiliki akses untuk membuat diskusi di course module ini');
    }


    public function store(\Illuminate\Http\Request $request)
    {
        $rules = [
            'title' => ['required', 'max:255'],
            'file_attachment' => ['mimes: pptx,ppt,pdf,doc,docx,jpg,jpeg,png,zip', 'nullable'],

        ];
        $messages = [
            'title.required' => 'Judul diskusi tidak boleh kosong',
            'file_attachment.mimes' => 'Format file yang boleh diupload hanya pptx, ppt, pdf, doc, docx, jpg, jpeg, png, zip',
        ];

        $request->validate($rules, $messages);

        $discussion = Discussion::create([
            'title' => $request->title,
            'discuss' => $request->discuss,
            'file_attachment' => $request->hasFile('file_attachment') ? Discussion::setFile($request->file_attachment) : null,
            'user_id' => Auth::id(),
            'course_module_id' => $request->course_module_id,
            'is_active' => 1,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        if ($request->addAgain) {
            return Redirect::route('discussions.create', $request->course_module_id)->with('success', 'Diskusi berhasil dibuat.');
        }
        return Redirect::route('discussions.index', $request->course_module_id)->with('success', 'Diskusi berhasil dibuat.');
    }

    public function storeCommentDiscussion(\Illuminate\Http\Request $request)
    {
        $rules = [
            'discuss' => ['required'],
            'file_attachment' => ['mimes: pptx,ppt,pdf,doc,docx,jpg,jpeg,png,zip', 'nullable'],
        ];
        $messages = [
            'discuss.required' => 'Pesan diskusi tidak boleh kosong',
            'file_attachment.mimes' => 'Format file yang boleh diupload hanya pptx, ppt, pdf, doc, docx, jpg, jpeg, png,zip',
        ];
        $request->validate($rules, $messages);

        $comment = Discussion::create([
            'parent_discuss_id' => $request->parent_discuss_id,
            'course_module_id' => $request->course_module_id,
            'user_id' => Auth::id(),
            'discuss' => $request->discuss,
            'file_attachment' => $request->hasFile('file_attachment') ? Discussion::setFile($request->file_attachment) : null,
            'is_active' => 1,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        if ($comment)
            return Redirect::route('discussions.show', ['course_module_id' => $request->course_module_id, 'discussion_id' => $request->parent_discuss_id])->with('success', 'Komentar berhasil dibuat.');
    }

    public function show($course_module_id, $discussion_id)
    {
        $data['userLogin'] = Auth::user();

        $data['discussion'] = Discussion::with(['user'])
            ->where('id', $discussion_id)
            ->where('course_module_id', $course_module_id)
            ->first();
        $data['discussion']->photo = $data['discussion']->user->photoUrl([]);
        // dd($data['discussion']);
        $data['discussionComments'] = Discussion::where('parent_discuss_id', $discussion_id)
            ->where('is_active', 1)
            ->paginate()
            ->transform(function ($comment) {
                return [
                    'id' => $comment->id,
                    'title' => $comment->title,
                    'discuss' => $comment->discuss,
                    'file_attachment' => $comment->file_attachment,
                    'parent_discuss_id' => $comment->parent_discuss_id,
                    'user' => $comment->user,
                    'photo' => $comment->user->photoUrl([]),
                    'course_module' => $comment->courseModule,
                    'is_active' => $comment->is_active,
                    'created_at' => $comment->created_at,
                ];
            });
        // dd($data);
        return Inertia::render('Discussions/Show', $data);
    }

    public function reply($reply_id){

         $data = user::findOrFail($reply_id);
         return view ('Discussions/Show',$data); 
         
    }

    public function toggleStatusActive($discussion_id)
    {
        $discussion = Discussion::find($discussion_id);
        $discussion->is_active = $discussion->is_active ? '0' : '1';
        $discussion->save();

        return Redirect::back()->with('success', 'Berhasil mengubah status aktif diskusi');
    }

    public function downloadFile($discussion_id)
    {
        $discussion = Discussion::find($discussion_id);
        // dd($discussion);

        $path = public_path('/files/discussions/' . $discussion->file_attachment);
        $header = [
            'Content-Type' => 'application/*',
        ];
        return response()->download($path, $discussion->file_attachment, $header);
    }

    public function destroy($discussion_id)
    {
        $discussion = Discussion::findOrFail($discussion_id);
        $url = explode('/', url()->previous());
        $course_module_id = $url[4];

        if ($discussion->parent_discuss_id) {
            // komentar
            if ($discussion->file_attachment) {
                Discussion::removeFile($discussion->file_attachment);
            }
            $discussion->is_active = 0;
            $discussion->save();

            return Redirect::back()->with('success', 'Berhasil menghapus komentar');
        } else {
            // parent diskusi
            if ($discussion->file_attachment != null) {
                Discussion::removeFile($discussion->file_attachment);
            }

            $discussionComments = Discussion::where('parent_discuss_id', $discussion->id)->get();
            foreach ($discussionComments as $comment) {
                if ($comment->file_attachment != null) {
                    Discussion::removeFile($comment->file_attachment);
                }
            }

            $discussion->is_active = 2;
            $discussion->save();

            return Redirect::route('discussions.index', $course_module_id)->with('success', 'Berhasil menghapus diskusi');
        }
    }
}
