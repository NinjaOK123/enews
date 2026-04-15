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
        // Fix BUG-12: Only load active categories
        $categories = Category::where('is_active', true)->orderBy('name')->get();
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
        $post->content = clean($validated['content']);
        $post->excerpt = Str::limit(strip_tags($post->content), 150);
        $post->source_author = $request->input('source_author');
        $post->photographer = $request->input('photographer');
        $post->status = $request->input('action') === 'pending' ? 'pending' : 'draft';

        if ($request->hasFile('thumbnail')) {
            $post->thumbnail = $this->processThumbnail($request->file('thumbnail'));
        }

        if (in_array(auth()->user()->role, ['admin', 'editor'])) {
            $post->is_featured = $request->has('is_featured');
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
        // Fix BUG-12: Only load active categories
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $royaltyRates = \App\Models\RoyaltyRate::orderBy('group_name')->orderBy('name')->get();
        return view('contributor.posts.create', compact('post', 'categories', 'royaltyRates'));
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => 'required|max:255',
            'category_id' => 'required|exists:categories,id',
            'thumbnail' => 'nullable|image|max:5120',
            'content' => 'required|min:200',
            'royalty_rate_id' => 'nullable|exists:royalty_rates,id',
            'image_count' => 'nullable|integer|min:0',
        ]);

        $post->title = $validated['title'];

        // Fix BUG-08: Update slug if post is still in draft/rejected state
        if (in_array($post->status, ['draft', 'rejected'])) {
            $post->slug = Str::slug($validated['title']) . '-' . uniqid();
        }

        $post->category_id = $validated['category_id'];
        $post->content = clean($validated['content']);
        $post->excerpt = Str::limit(strip_tags($post->content), 150);
        $post->source_author = $request->input('source_author');
        $post->photographer = $request->input('photographer');

        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail if needed (and only if it is in the thumbnails folder)
            if ($post->thumbnail && str_starts_with($post->thumbnail, 'thumbnails/')) {
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

        if (in_array(auth()->user()->role, ['admin', 'editor'])) {
            $post->is_featured = $request->has('is_featured');
            $post->royalty_rate_id = $request->input('royalty_rate_id');
            $post->image_count = $request->input('image_count', 0);
            $post->royalty_multiplier = 1;
            
            if ($post->royalty_rate_id) {
                $rate = \App\Models\RoyaltyRate::find($post->royalty_rate_id);
                $post->royalty_total = ($rate->amount * $post->royalty_multiplier) + ($post->image_count * 10000);
            } else {
                $post->royalty_total = 0;
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
        $post->content = clean($request->input('content', $post->content));
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

        // Fix BUG-11: Giới hạn revision tối đa 10
        $oldRevisions = PostRevision::where('post_id', $post->id)
            ->orderBy('revision_number', 'desc')
            ->skip(10)
            ->take(PHP_INT_MAX)
            ->pluck('id');
            
        if ($oldRevisions->isNotEmpty()) {
            PostRevision::whereIn('id', $oldRevisions)->delete();
        }
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
            'file_type' => $file->getMimeType(),
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

        $title = '';
        $html = '';
        $titleElementHash = null;

        // Vòng 1: Tìm Tiêu đề (Câu đầu tiên in đậm và canh giữa)
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                $isBold = false;
                $align = '';
                
                $text = trim($this->getWordTextAndStyles($element, $isBold, $align));

                if (mb_strlen($text, 'UTF-8') > 5) {
                    // Ưu tiên dòng đầu tiên có In Đậm và Canh Giữa
                    if (($align === 'center' || $align === 'both') && $isBold) {
                        $title = $text;
                        $titleElementHash = spl_object_hash($element);
                        break 2;
                    }
                }
            }
        }

        // Nếu ko có dòng nào Canh giữa + In đậm, lấy dòng Text rõ ràng đầu tiên làm Tiêu đề
        if (!$title) {
            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    $isBold = false;
                    $align = '';
                    $text = trim($this->getWordTextAndStyles($element, $isBold, $align));
                    
                    if (mb_strlen($text, 'UTF-8') > 5) {
                        $title = $text;
                        $titleElementHash = spl_object_hash($element);
                        break 2;
                    }
                }
            }
        }

        // Vòng 2: Gen HTML bỏ qua Tiêu đề đã trích xuất
        $maxHtmlLength = 200000; // Fix BUG-15: Limit HTML size to ~200KB characters
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (strlen($html) > $maxHtmlLength) {
                    break 2; // Prevent extremely large files from crashing
                }
                if ($titleElementHash && spl_object_hash($element) === $titleElementHash) {
                    continue; // Bỏ qua element tiêu đề để không bị trùng vào nội dung
                }
                $html .= $this->parseWordElement($element);
            }
        }

        return response()->json(['title' => $title, 'content' => $html]);
    }

    private function getWordTextAndStyles($element, &$isBold, &$align): string
    {
        $text = '';
        
        if (method_exists($element, 'getParagraphStyle')) {
            $pStyle = $element->getParagraphStyle();
            if (is_object($pStyle) && method_exists($pStyle, 'getAlignment')) {
                $val = $pStyle->getAlignment();
                if ($val) $align = $val;
            }
        }
        
        if (method_exists($element, 'getFontStyle')) {
            $fStyle = $element->getFontStyle();
            if (is_object($fStyle) && method_exists($fStyle, 'isBold') && $fStyle->isBold()) {
                $isBold = true;
            }
        }
        
        if (method_exists($element, 'getText')) {
            $val = $element->getText();
            if (is_string($val)) $text .= $val;
        }
        
        if (method_exists($element, 'getElements')) {
            foreach ($element->getElements() as $child) {
                $text .= $this->getWordTextAndStyles($child, $isBold, $align);
            }
        }
        
        return $text;
    }

    private function parseWordElement($element): string
    {
        // TextRun (paragraph with multiple runs)
        if ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
            $inner = '';
            foreach ($element->getElements() as $run) {
                $inner .= $this->parseWordElement($run);
            }
            return $inner ? "<p>{$inner}</p>\n" : '';
        }

        // Plain Text
        if ($element instanceof \PhpOffice\PhpWord\Element\Text) {
            $text = htmlspecialchars($element->getText() ?? '');
            $font = method_exists($element, 'getFontStyle') ? $element->getFontStyle() : null;
            if (is_object($font)) {
                if (method_exists($font, 'isBold') && $font->isBold()) $text = "<strong>{$text}</strong>";
                if (method_exists($font, 'isItalic') && $font->isItalic()) $text = "<em>{$text}</em>";
            }
            return $text;
        }

        // Paragraph (Heading or regular)
        if ($element instanceof \PhpOffice\PhpWord\Element\Paragraph) {
            $inner = '';
            foreach ($element->getElements() as $child) {
                $inner .= $this->parseWordElement($child);
            }
            $inner = trim($inner);
            if (!$inner) return '';

            $style = method_exists($element, 'getParagraphStyle') ? $element->getParagraphStyle() : null;
            $styleName = is_object($style) && method_exists($style, 'getStyleName') ? $style->getStyleName() : '';
            $align = is_object($style) && method_exists($style, 'getAlignment') ? $style->getAlignment() : '';

            $styleAttr = '';
            if ($align === 'center') $styleAttr = ' style="text-align: center;"';
            elseif ($align === 'right') $styleAttr = ' style="text-align: right;"';
            elseif ($align === 'both' || $align === 'justify') $styleAttr = ' style="text-align: justify;"';

            if (preg_match('/heading\s*1/i', (string)$styleName)) return "<h1{$styleAttr}>{$inner}</h1>\n";
            if (preg_match('/heading\s*2/i', (string)$styleName)) return "<h2{$styleAttr}>{$inner}</h2>\n";
            if (preg_match('/heading\s*3/i', (string)$styleName)) return "<h3{$styleAttr}>{$inner}</h3>\n";

            return "<p{$styleAttr}>{$inner}</p>\n";
        }

        // List item
        if ($element instanceof \PhpOffice\PhpWord\Element\ListItem) {
            $inner = method_exists($element, 'getTextObject') && $element->getTextObject() ? htmlspecialchars($element->getTextObject()->getText() ?? '') : '';
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
                        $cellContent .= $this->parseWordElement($cellEl);
                    }
                    $tableHtml .= "<td style=\"padding:6px 10px;\">{$cellContent}</td>";
                }
                $tableHtml .= '</tr>';
            }
            $tableHtml .= '</table>';
            return $tableHtml . "\n";
        }

        return '';
    }

}
