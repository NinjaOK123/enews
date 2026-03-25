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
        $post->source_author = $request->input('source_author');
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
        $post->source_author = $request->input('source_author');

        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail if needed
            if ($post->thumbnail) {
                Storage::disk('public')->delete($post->thumbnail);
            }
            $post->thumbnail = $this->processThumbnail($request->file('thumbnail'));
        }

        if ($request->has('action')) {
            $action = $request->input('action');
            if ($action === 'pending') {
                $post->status = 'pending';
            } elseif ($action === 'approve' && in_array(auth()->user()->role, ['admin', 'editor'])) {
                $post->status = 'published';
                $post->published_at = now();
            } elseif ($action === 'reject' && in_array(auth()->user()->role, ['admin', 'editor'])) {
                $post->status = 'rejected';
            }
        }

        $post->save();

        // Save revision
        $this->saveRevision($post);

        if ($post->status === 'pending') {
             return redirect()->route('contributor.dashboard')->with('success', 'Đã cập nhật và gửi bài viết chờ duyệt thành công.');
        } elseif ($post->status === 'published' && request()->input('action') === 'approve') {
             return redirect()->route('admin.posts.index')->with('success', 'Đã cập nhật và duyệt bài viết thành công. Bài viết đã được xuất bản.');
        } elseif ($post->status === 'rejected' && request()->input('action') === 'reject') {
             return redirect()->route('admin.posts.index')->with('success', 'Đã gỡ/từ chối bài viết thành công.');
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
        $phpWord = IOFactory::load($file->getRealPath());

        $html   = '';
        $title  = '';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                $html .= $this->parseWordElement($element, $title);
            }
        }

        return response()->json(['title' => $title, 'content' => $html]);
    }

    private function parseWordElement($element, &$title): string
    {
        $class = get_class($element);

        // TextRun (paragraph with multiple runs)
        if ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
            $inner = '';
            foreach ($element->getElements() as $run) {
                $inner .= $this->parseWordElement($run, $title);
            }
            if (!$title && trim(strip_tags($inner))) {
                $title = trim(strip_tags($inner));
                return '';
            }
            return $inner ? "<p>{$inner}</p>\n" : '';
        }

        // Plain Text
        if ($element instanceof \PhpOffice\PhpWord\Element\Text) {
            $text = htmlspecialchars($element->getText() ?? '');
            $font = $element->getFontStyle();
            if (is_object($font)) {
                if ($font->getBold())   $text = "<strong>{$text}</strong>";
                if ($font->getItalic()) $text = "<em>{$text}</em>";
            }
            return $text;
        }

        // Paragraph (Heading or regular)
        if ($element instanceof \PhpOffice\PhpWord\Element\Paragraph) {
            $inner = '';
            foreach ($element->getElements() as $child) {
                $inner .= $this->parseWordElement($child, $title);
            }
            $inner = trim($inner);
            if (!$inner) return '';

            $style = $element->getParagraphStyle();
            $styleName = is_object($style) ? ($style->getStyleName() ?? '') : (is_string($style) ? $style : '');

            if (preg_match('/heading\s*1/i', $styleName)) return "<h1>{$inner}</h1>\n";
            if (preg_match('/heading\s*2/i', $styleName)) return "<h2>{$inner}</h2>\n";
            if (preg_match('/heading\s*3/i', $styleName)) return "<h3>{$inner}</h3>\n";

            if (!$title) { $title = strip_tags($inner); return ''; }
            return "<p>{$inner}</p>\n";
        }

        // List item
        if ($element instanceof \PhpOffice\PhpWord\Element\ListItem) {
            $inner = htmlspecialchars($element->getTextObject()->getText() ?? '');
            return "<li>{$inner}</li>\n";
        }

        // Table
        if ($element instanceof \PhpOffice\PhpWord\Element\Table) {
            $tableHtml = '<table border="1" style="border-collapse:collapse;width:100%;">';
            foreach ($element->getRows() as $row) {
                $tableHtml .= '<tr>';
                foreach ($row->getCells() as $cell) {
                    $cellContent = '';
                    foreach ($cell->getElements() as $cellEl) {
                        $cellContent .= $this->parseWordElement($cellEl, $title);
                    }
                    $tableHtml .= "<td style=\"padding:6px 10px;\">{$cellContent}</td>";
                }
                $tableHtml .= '</tr>';
            }
            $tableHtml .= '</table>';
            return $tableHtml . "\n";
        }

        // Image
        if ($element instanceof \PhpOffice\PhpWord\Element\Image) {
            return ''; // Bỏ qua ảnh (không extract được từ docx dễ dàng)
        }

        return '';
    }

}
