<?php

namespace App\Http\Controllers\Contributor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Category;
use App\Models\Media;
use App\Models\PostRevision;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PostController extends Controller
{
    use AuthorizesRequests;

    public function create()
    {
        $categories = Category::all();
        return view('contributor.posts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'category_id' => 'required|exists:categories,id',
            'thumbnail' => 'nullable|image|max:5120',
            'content' => 'required|min:200',
        ]);

        $post = new Post();
        $post->title = $validated['title'];
        $post->slug = Str::slug($validated['title']) . '-' . uniqid();
        $post->category_id = $validated['category_id'];
        $post->author_id = auth()->id();
        $post->content = $validated['content'];
        $post->excerpt = Str::limit(strip_tags($validated['content']), 150);
        $post->status = $request->input('action') === 'pending' ? 'pending' : 'draft';

        if ($request->hasFile('thumbnail')) {
            $post->thumbnail = $this->processThumbnail($request->file('thumbnail'));
        }

        $post->save();

        // Save initial revision
        $this->saveRevision($post);

        if ($post->status === 'pending') {
            return redirect()->route('contributor.dashboard')->with('success', 'Đã lưu và gửi bài viết chờ duyệt thành công.');
        }

        return redirect()->route('contributor.posts.edit', $post)->with('success', 'Đã lưu nháp bài viết.');
    }

    public function edit(Post $post)
    {
        $this->authorize('update', $post);
        $categories = Category::all();
        return view('contributor.posts.create', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => 'required|max:255',
            'category_id' => 'required|exists:categories,id',
            'thumbnail' => 'nullable|image|max:5120',
            'content' => 'required|min:200',
        ]);

        $post->title = $validated['title'];
        $post->category_id = $validated['category_id'];
        $post->content = $validated['content'];
        $post->excerpt = Str::limit(strip_tags($validated['content']), 150);

        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail if needed
            if ($post->thumbnail) {
                Storage::disk('public')->delete($post->thumbnail);
            }
            $post->thumbnail = $this->processThumbnail($request->file('thumbnail'));
        }

        if ($request->has('action') && $request->input('action') === 'pending') {
            $post->status = 'pending';
        }

        $post->save();

        // Save revision
        $this->saveRevision($post);

        if ($post->status === 'pending') {
             return redirect()->route('contributor.dashboard')->with('success', 'Đã cập nhật và gửi bài viết chờ duyệt thành công.');
        }

        return redirect()->route('contributor.posts.edit', $post)->with('success', 'Đã cập nhật bài viết nháp.');
    }

    public function submit(Request $request, Post $post)
    {
        $this->authorize('update', $post);
        
        $post->status = 'pending';
        $post->save();

        return redirect()->route('contributor.dashboard')->with('success', 'Đã gửi bài viết chờ duyệt.');
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        
        // Cập nhật trạng thái hoặc xoá cứng tuỳ chọn. Hiện tại dùng delete mềm nhờ SoftDeletes nếu có, hoặc delete hard luôn.
        // Tốt nhất nếu $post->delete() thì sẽ xoá.
        $post->delete();

        return redirect()->route('contributor.dashboard')->with('success', 'Đã xóa bài viết nháp thành công.');
    }

    public function autosave(Request $request, Post $post)
    {
        $this->authorize('update', $post);
        
        // Cập nhật nội dung nhưng không đổi trạng thái
        $post->content = $request->input('content', $post->content);
        $post->excerpt = Str::limit(strip_tags($post->content), 150);
        $post->save();

        $this->saveRevision($post);

        return response()->json(['success' => true, 'message' => 'Đã tự động lưu nháp.']);
    }

    private function saveRevision(Post $post)
    {
        // Tạo log revision history
        $lastRevision = PostRevision::where('post_id', $post->id)->orderBy('revision_number', 'desc')->first();
        $revNumber = $lastRevision ? $lastRevision->revision_number + 1 : 1;

        PostRevision::create([
            'post_id' => $post->id,
            'user_id' => auth()->id(),
            'content' => $post->content,
            'revision_number' => $revNumber,
        ]);
    }

    private function processThumbnail($file)
    {
        $manager = new ImageManager(new Driver());
        // Load image từ uploaded file
        $image = $manager->read($file->getRealPath());

        // Resize hình giữ tỉ lệ với width max 1200
        $image->scaleDown(1200);

        $filename = uniqid() . '.jpg';
        $path = 'thumbnails/' . auth()->id() . '/' . $filename;
        
        // Encode sang format Jpeg nén 80% rồi lưu vào disk
        Storage::disk('public')->put($path, (string) $image->toJpeg(80));

        return $path;
    }

    public function uploadMedia(Request $request)
    {
        $request->validate([
            'upload' => 'required|file|max:20480|mimes:jpeg,png,jpg,gif,svg,mp4,webm' // CKEditor sends file as 'upload'
        ]);

        $file = $request->file('upload');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $userId = auth()->id();
        
        $type = str_starts_with($file->getMimeType(), 'video/') ? 'video' : 'image';
        
        // Upload tới thư mục riêng của user theo yêu cầu
        $filePath = $file->storeAs("media/{$userId}", $fileName, 'public');

        $media = Media::create([
            'user_id' => $userId,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'file_type' => $type,
            'file_size' => $file->getSize(),
            'is_shared' => false,
        ]);

        // Trả về JSON cho CKEditor
        return response()->json([
            'uploaded' => 1,
            'fileName' => $fileName,
            'url' => route('contributor.media.view', $media->id),
            'type' => $type
        ]);
    }

    public function importWord(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:docx,doc|max:10240'
        ]);

        $file = $request->file('document');
        
        // Sử dụng PhpWord để đọc file .docx
        $phpWord = IOFactory::load($file->getRealPath());
        $content = '';
        $title = '';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                // Read text elements
                if (method_exists($element, 'getText')) {
                    $text = $element->getText();
                    if (!empty(trim($text))) {
                        if (!$title) {
                            $title = trim($text); // Gán title là đoạn text đầu tiên tìm thấy
                        } else {
                            $content .= '<p>' . htmlspecialchars(trim($text)) . '</p>';
                        }
                    }
                }
            }
        }

        return response()->json([
            'title' => $title,
            'content' => $content
        ]);
    }
}
