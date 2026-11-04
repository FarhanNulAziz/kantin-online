<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CanteenStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CanteenStatusController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:penuh,tidak_penuh'],
        ]);

        $status = CanteenStatus::current();
        $status->update([
            'status' => $data['status'],
            'updated_by' => $request->user()->id,
        ]);

        $label = $data['status'] === 'penuh' ? 'Penuh' : 'Tidak Penuh';

        return back()->with('success', "Status kantin diperbarui menjadi {$label}.");
    }
}
