<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProfileController extends Controller
{
    /**
     * Profile riêng (yêu cầu đăng nhập).
     */
    public function show($id = null)
    {
        $user = $id ? User::findOrFail($id) : auth()->user();

        // Chỉ xem profile của chính mình hoặc Admin
        if ($user->id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403);
        }

        return $this->buildProfileView($user, isOwnProfile: true);
    }

    /**
     * Profile công khai (ai cũng xem được).
     */
    public function showPublic($id)
    {
        $user = User::findOrFail($id);
        $isOwnProfile = auth()->check() && auth()->id() === $user->id;
        return $this->buildProfileView($user, isOwnProfile: $isOwnProfile);
    }

    // ─── Helper ──────────────────────────────────────────────────────────────

    private function buildProfileView(User $user, bool $isOwnProfile)
    {
        // Thống kê chung
        $articlesCount = $user->posts()->published()->count();
        $totalViews    = $user->posts()->sum('view_count');
        $totalLikes    = DB::table('post_likes')
            ->whereIn('post_id', $user->posts()->pluck('id'))
            ->count();

        // Bài viết (tab Bài viết)
        $posts = $user->posts()
            ->with('category')
            ->published()
            ->latest('published_at')
            ->paginate(12);

        // Bộ sưu tập (tab Đã lưu) — chỉ hiện bộ sưu tập công khai nếu không phải chủ sở hữu
        $collections = $isOwnProfile
            ? $user->collections()->withCount('posts')->get()
            : $user->collections()->where('is_public', true)->withCount('posts')->get();

        // Bài đã thích (tab Đã thích) — chỉ chủ sở hữu mới thấy
        $likedPosts = $isOwnProfile
            ? $user->likedPosts()->with('category')->take(12)->get()
            : collect();

        return view('profile.show', compact(
            'user',
            'isOwnProfile',
            'articlesCount',
            'totalViews',
            'totalLikes',
            'posts',
            'collections',
            'likedPosts'
        ));
    }

    /**
     * Cập nhật thông tin cá nhân.
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'name' => 'required|string|max:100',
            'bio'  => 'nullable|string|max:300',
        ]);

        $user->update($request->only(['name', 'bio']));

        return redirect()->back()->with('success', 'Cập nhật thông tin thành công!');
    }

    /**
     * Cập nhật avatar.
     */
    public function updateAvatar(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($request->hasFile('avatar')) {
            try {
                $manager  = new ImageManager(new Driver());
                $image    = $manager->read($request->file('avatar')->getRealPath());
                $image->cover(400, 400);

                $filename = 'avatar_' . $user->id . '_' . time() . '.jpg';
                $path     = 'avatars/' . $filename;

                // Xóa avatar cũ nếu có
                if ($user->avatar && str_starts_with($user->avatar, 'avatars/') && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }

                Storage::disk('public')->put($path, (string) $image->toJpeg(85));

                DB::table('users')->where('id', $user->id)->update(['avatar' => $path]);

                return redirect()->back()->with('success', 'Cập nhật ảnh đại diện thành công!');

            } catch (\Throwable $e) {
                \Log::error('Avatar upload error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Lỗi khi xử lý ảnh: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('error', 'Vui lòng chọn file ảnh.');
    }

    /**
     * Cập nhật ảnh bìa (cover photo).
     */
    public function updateCover(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'cover_photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if ($request->hasFile('cover_photo')) {
            try {
                $manager  = new ImageManager(new Driver());
                $image    = $manager->read($request->file('cover_photo')->getRealPath());
                // Resize về tỷ lệ 16:9 — 1200×400
                $image->cover(1200, 400);

                $filename = 'cover_' . $user->id . '_' . time() . '.jpg';
                $path     = 'covers/' . $filename;

                // Xóa ảnh bìa cũ nếu có
                if ($user->cover_photo
                    && str_starts_with($user->cover_photo, 'covers/')
                    && Storage::disk('public')->exists($user->cover_photo)) {
                    Storage::disk('public')->delete($user->cover_photo);
                }

                Storage::disk('public')->put($path, (string) $image->toJpeg(85));

                DB::table('users')->where('id', $user->id)->update(['cover_photo' => $path]);

                return redirect()->back()->with('success', 'Cập nhật ảnh bìa thành công!');

            } catch (\Throwable $e) {
                \Log::error('Cover upload error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Lỗi khi xử lý ảnh: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('error', 'Vui lòng chọn file ảnh.');
    }
}
