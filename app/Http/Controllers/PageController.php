<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use App\Mail\ContactFormMail;
class PageController extends Controller
{
    public function gioiThieu(): View
    {
        return view('pages.gioi-thieu');
    }

    public function quyDinh(): View
    {
        return view('pages.quy-dinh');
    }

    public function lienHe(): View
    {
        return view('pages.lien-he');
    }

    public function docVaSuyNgam(): View
    {
        return view('pages.doc-suy-ngam');
    }

    public function sendContactEmail(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:2000',
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'message.required' => 'Vui lòng nhập nội dung tin nhắn.',
            'message.max' => 'Nội dung quá dài (tối đa 2000 ký tự).'
        ]);

        Mail::to('enews@agu.edu.vn')->send(new ContactFormMail($validated));

        return back()->with('success', 'Tin nhắn của bạn đã được gửi. Chúng tôi sẽ phản hồi trong thời gian sớm nhất!');
    }
}
