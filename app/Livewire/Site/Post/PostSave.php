<?php

namespace App\Livewire\Site\Post;

use App\Services\Site\PostSaveService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PostSave extends Component
{
    public $save = false;
    public $userId = null;
    public $postId = null;

    public function mount($postId, PostSaveService $postSaveService)
    {
        $this->postId = $postId;

        if (Auth::guard('site')->check()) {
            $this->userId = Auth::guard('site')->user()->id;

            $postSave = $postSaveService->getById($this->userId, $this->postId);
            $this->save = !!$postSave;
        }
    }

    public function showAlert($icon, $title, $text)
    {
        $this->dispatch('show-alert', [
            'icon' => $icon,
            'title' => $title,
            'text' => $text
        ]);
    }

    public function showToast($icon, $title)
    {
        $this->dispatch('show-toast', [
            'icon' => $icon,
            'title' => $title,
            'timer' => '1500'
        ]);
    }

    public function savePost(PostSaveService $postSaveService)
    {
        if (!$this->userId) {
            $this->showAlert('error', 'Lỗi', 'Vui lòng đăng nhập');
            $this->skipRender();
            return;
        }

        $postSaveService->create($this->userId, $this->postId);
        $this->save = true;

        $this->showToast('success', 'Lưu bài viết thành công');
    }

    public function unSavePost(PostSaveService $postSaveService)
    {
        if (!$this->userId) {
            $this->showAlert('error', 'Lỗi', 'Vui lòng đăng nhập');
            $this->skipRender();
            return;
        }

        $postSaveService->delete($this->userId, $this->postId);
        $this->save = false;

        $this->showToast('success', 'Hủy lưu bài viết thành công');
    }

    public function render()
    {
        return view('livewire.site.post.post-save');
    }
}
