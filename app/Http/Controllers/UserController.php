<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $query = User::query();
        
        // Filter by role if specified
        if ($request->has('role')) {
            $role = $request->get('role');
            $query->where('role', $role);
        }
        
        // Filter by active status if specified
        if ($request->has('status') && $request->get('status') !== '') {
            $status = (bool) $request->get('status');
            $query->where('active', $status);
        }
        
        // Search by name or email
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%");
            });
        }
        
        $users = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'role' => ['required', Rule::in(['admin', 'customer'])],
            'active' => 'boolean',
        ]);
        
        $validated['password'] = Hash::make($validated['password']);
        $validated['active'] = $request->has('active');
        
        User::create($validated);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Người dùng đã được tạo thành công!');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load(['orders', 'reviews']);
        
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'role' => ['required', Rule::in(['admin', 'customer'])],
            'active' => 'boolean',
        ]);
        
        // Don't update password if not provided
        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }
        
        $validated['active'] = $request->has('active');
        
        // Prevent locking yourself out
        if ($user->id === Auth::id() && $user->role === 'admin' && $validated['role'] !== 'admin') {
            return redirect()->back()
                ->with('error', 'Không thể thay đổi vai trò của chính bạn từ Admin sang vai trò khác.');
        }
        
        // Prevent disabling your own account
        if ($user->id === Auth::id() && !$validated['active']) {
            return redirect()->back()
                ->with('error', 'Không thể vô hiệu hóa tài khoản của chính bạn.');
        }
        
        $user->update($validated);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Thông tin người dùng đã được cập nhật thành công!');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deleting your own account
        if ($user->id === Auth::id()) {
            return redirect()->back()
                ->with('error', 'Không thể xóa tài khoản của chính bạn.');
        }
        
        // May want to add additional checks before deletion, e.g.:
        // - Check if user has orders and decide what to do with them
        // - Instead of deletion, consider making the account inactive
        
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Người dùng đã được xóa thành công!');
    }
    
    /**
     * Toggle user active status.
     */
    public function toggleActive(User $user)
    {
        // Prevent disabling your own account
        if ($user->id === Auth::id()) {
            return redirect()->back()
                ->with('error', 'Không thể vô hiệu hóa tài khoản của chính bạn.');
        }
        
        $user->active = !$user->active;
        $user->save();
        
        $status = $user->active ? 'kích hoạt' : 'vô hiệu hóa';
        
        return redirect()->back()
            ->with('success', "Tài khoản của {$user->name} đã được $status thành công.");
    }
    
    /**
     * Update user status via AJAX.
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'active' => 'required|boolean',
        ]);
        
        $userId = $request->input('user_id');
        $active = $request->input('active');
        
        // Prevent disabling your own account
        if ($userId == Auth::id() && !$active) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể vô hiệu hóa tài khoản của chính bạn.'
            ], 403);
        }
        
        $user = User::findOrFail($userId);
        $user->active = $active;
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => "Trạng thái của người dùng đã được cập nhật thành công."
        ]);
    }
    
    /**
     * Process bulk actions on users.
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|json',
            'action' => 'required|in:active,inactive',
        ]);
        
        $ids = json_decode($validated['ids'], true);
        $action = $validated['action'];
        
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Không có người dùng nào được chọn.');
        }
        
        // Exclude current user ID to prevent self-deactivation
        $ids = array_filter($ids, function($id) {
            return $id != Auth::id();
        });
        
        $active = ($action === 'active');
        
        // Update user statuses
        $updatedCount = User::whereIn('id', $ids)->update(['active' => $active]);
        
        $status = $active ? 'kích hoạt' : 'vô hiệu hóa';
        
        return redirect()->back()
            ->with('success', "Đã $status $updatedCount tài khoản người dùng thành công.");
    }
}