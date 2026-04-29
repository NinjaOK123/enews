<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Post $post): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Post $post): bool
    {
        // Admin có toàn quyền sửa
        if ($user->role === 'admin') {
            return true;
        }

        // Bài đã duyệt (published) thì chỉ có admin mới được sửa. Editor hay Tác giả đều bị chặn.
        if ($post->status === 'published') {
            return false;
        }

        // Editor có quyền sửa các bài đang chờ duyệt (pending) hoặc bản nháp của người khác
        if ($user->role === 'editor') {
            return true;
        }

        // Tác giả chỉ được sửa bài của mình khi đang nháp hoặc bị từ chối
        return $post->author_id === $user->id && in_array($post->status, ['draft', 'rejected']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Post $post): bool
    {
        return $post->author_id === $user->id && in_array($post->status, ['draft', 'rejected']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Post $post): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Post $post): bool
    {
        return false;
    }
}
