<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Danh sách bình luận với filter trạng thái.
     */
    public function index(Request $request)
    {
        $status = $request->input('status', 'pending'); // pending | approved | rejected | all

        $query = Comment::with(['user', 'post'])->latest();

        // is_approved: 
        // 1 (true) = approved
        // null = pending
        // 0 (false) = rejected
        if ($status === 'pending') {
            $query->whereNull('is_approved');
        } elseif ($status === 'approved') {
            $query->where('is_approved', true);
        } elseif ($status === 'rejected') {
            $query->where('is_approved', false)->whereNotNull('is_approved');
        }
        // 'all' → không filter

        $comments = $query->paginate(15)->withQueryString();

        // Số bình luận chờ duyệt (dùng cho badge)
        $pendingCount = Comment::whereNull('is_approved')->count();

        return view('admin.comments.index', compact('comments', 'status', 'pendingCount'));
    }

    /**
     * Duyệt bình luận.
     */
    public function approve(Comment $comment)
    {
        $comment->update(['is_approved' => true]);
        return back()->with('success', 'Đã duyệt bình luận.');
    }

    /**
     * Từ chối / ẩn bình luận (đặt lại is_approved = false).
     */
    public function reject(Comment $comment)
    {
        $comment->update(['is_approved' => false]);
        return back()->with('success', 'Đã ẩn bình luận.');
    }

    /**
     * Xoá vĩnh viễn bình luận.
     */
    public function destroy(Comment $comment)
    {
        $comment->delete();
        return back()->with('success', 'Đã xoá bình luận.');
    }
}
