<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProfileController extends Controller
{
    /**
     * Hiển thị trang thông tin cá nhân của người dùng đã đăng nhập.
     */
    public function index()
    {
        return view('profile.index', ['user' => auth()->user()]);
    }

    /**
     * Cập nhật avatar của user
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $user = auth()->user();

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            
            // Xử lý ảnh: Crop vuông 200x200
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getRealPath());
            $image->coverDown(400, 400); // cover center 400x400

            $filename = 'avatar_' . $user->id . '_' . time() . '.jpg';
            $path = 'avatars/' . $filename;

            // Xoá ảnh cũ (nếu có và không phải ảnh mặc định từ ngoài)
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Lưu ảnh mới
            Storage::disk('public')->put($path, (string) $image->toJpeg(80));

            // Cập nhật database
            $user->avatar = $path;
            $user->save();

            return redirect()->back()->with('success', 'Khoác áo mới thành công! (Cập nhật Avatar)');
        }

        return redirect()->back()->with('error', 'Có lỗi khi tải ảnh lên.');
    }
}
