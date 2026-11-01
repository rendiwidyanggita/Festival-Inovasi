<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Jury\StoreRequest;
use App\Http\Requests\Jury\UpdateRequest;
use App\Models\User;
use App\Enums\UserRole;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;


class JuryController extends Controller
{
    public function index(): View
    {
        $juries = User::withTrashed()
            ->where('role', UserRole::Jury->value)
            ->orderBy('name')
            ->paginate(15);

        return view('admin.juries.index', compact('juries'));
    }

    public function create(): View
    {
        return view('admin.juries.create');
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => UserRole::Jury->value,
            'is_active' => true,
        ]);

        session()->flash('success', 'Juri berhasil dibuat.');

        return redirect()->route('admin.juries.index');
    }

    public function edit(User $jury): View
    {
        return view('admin.juries.edit', compact('jury'));
    }

    public function update(UpdateRequest $request, User $jury): RedirectResponse
    {
        $oldValues = [
            'name' => $jury->name,
            'email' => $jury->email,
            'is_active' => $jury->is_active,
        ];

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $jury->update($updateData);

        // Log audit if is_active changed from true to false
        if ($oldValues['is_active'] && !$jury->is_active) {
            AuditService::logChange(
                auth()->user(),
                $jury,
                'deactivate',
                $oldValues,
                ['is_active' => false]
            );
        }

        session()->flash('success', 'Juri berhasil diperbarui.');

        return redirect()->route('admin.juries.index');
    }

    public function destroy(User $jury): RedirectResponse
    {
        // Use policy to prevent self-deletion (will throw 403)
        Gate::authorize('delete', $jury);


        $oldValues = [
            'name' => $jury->name,
            'email' => $jury->email,
        ];

        $jury->delete();

        AuditService::logChange(
            auth()->user(),
            $jury,
            'delete',
            $oldValues,
            []
        );

        session()->flash('success', 'Juri berhasil dihapus.');

        return redirect()->route('admin.juries.index');
    }

    public function restore(int $id): RedirectResponse
    {
        $jury = User::withTrashed()->findOrFail($id);

        $jury->restore();

        AuditService::logChange(
            auth()->user(),
            $jury,
            'restore',
            [],
            ['name' => $jury->name, 'email' => $jury->email]
        );

        session()->flash('success', 'Juri berhasil dipulihkan.');

        return redirect()->route('admin.juries.index');
    }
}
