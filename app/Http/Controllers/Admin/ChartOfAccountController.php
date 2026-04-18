<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ChartOfAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $directorsQuery = User::role('director')
            ->with(['chartOfAccounts' => function ($q) use ($request) {
                $q->withoutTrashed();
                if ($request->filled('search')) {
                    $search = $request->get('search');
                    $q->where(function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%")
                           ->orWhere('code', 'like', "%{$search}%");
                    });
                }
                if ($request->filled('type')) {
                    $q->where('type', $request->get('type'));
                }
                $q->orderBy('code');
            }]);

        $directors = $directorsQuery->get();

        return view('admin.coa.index', compact('directors'));
    }

    /**
     * Show the form for creating a single new resource.
     */
    public function create()
    {
        $directors = User::role('director')->orderBy('name')->get();
        return view('admin.coa.create', compact('directors'));
    }

    /**
     * Store a single newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'         => ['required', 'string', 'max:50', Rule::unique('chart_of_accounts', 'code')->whereNull('deleted_at')],
            'name'         => ['required', 'string', 'max:255'],
            'type'         => ['required', Rule::in(['income', 'expense'])],
            'director_id'  => ['nullable', 'exists:users,id'],
            'description'  => ['nullable', 'string', 'max:1000'],
            'budget_limit' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
        ]);

        ChartOfAccount::create(array_merge($validated, [
            'created_by' => Auth::id(),
            'is_active'  => true,
        ]));

        return redirect()->route('admin.coa.index')
            ->with('success', 'Account category created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ChartOfAccount $coa)
    {
        $directors = User::role('director')->orderBy('name')->get();
        return view('admin.coa.edit', [
            'chartOfAccount' => $coa,
            'account'        => $coa,
            'directors'      => $directors,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ChartOfAccount $coa)
    {
        $validated = $request->validate([
            'code'         => ['required', 'string', 'max:50', Rule::unique('chart_of_accounts', 'code')->ignore($coa->id)->whereNull('deleted_at')],
            'name'         => ['required', 'string', 'max:255'],
            'type'         => ['required', Rule::in(['income', 'expense'])],
            'director_id'  => ['nullable', 'exists:users,id'],
            'description'  => ['nullable', 'string', 'max:1000'],
            'budget_limit' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
        ]);

        $coa->update($validated);

        return redirect()->route('admin.coa.index')
            ->with('success', 'Account category updated successfully.');
    }

    /**
     * Remove or soft-deactivate the specified resource.
     */
    public function destroy(ChartOfAccount $coa)
    {
        $linkedCount = $coa->expenseLines()->count();

        if ($linkedCount === 0) {
            $coa->forceDelete();
            return redirect()->route('admin.coa.index')
                ->with('success', 'Account category deleted permanently.');
        }

        // Toggle active status instead of deleting when linked
        $coa->is_active = ! $coa->is_active;
        $coa->save();
        $status = $coa->is_active ? 'activated' : 'deactivated';

        return redirect()->route('admin.coa.index')
            ->with('success', "Account has {$linkedCount} linked expenses — {$status} instead of deleted.");
    }

    // ─────────────────────────────────────────────────────────────
    //  BULK CREATION
    // ─────────────────────────────────────────────────────────────

    /**
     * Show the bulk account creation form.
     */
    public function bulkCreate()
    {
        $directors = User::role('director')->orderBy('name')->get();
        return view('admin.coa.bulk-create', compact('directors'));
    }

    /**
     * Store multiple accounts in a single transaction.
     */
    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'director_id'              => ['nullable', 'exists:users,id'],
            'accounts'                 => ['required', 'array', 'min:1', 'max:50'],
            'accounts.*.code'          => ['required', 'string', 'max:50', Rule::unique('chart_of_accounts', 'code')->whereNull('deleted_at')],
            'accounts.*.name'          => ['required', 'string', 'max:255'],
            'accounts.*.type'          => ['required', Rule::in(['income', 'expense'])],
            'accounts.*.description'   => ['nullable', 'string', 'max:1000'],
            'accounts.*.budget_limit'  => ['nullable', 'numeric', 'min:0', 'max:999999999'],
        ]);

        $directorId = $validated['director_id'] ?? null;
        $now        = now();
        $createdBy  = Auth::id();
        $count      = 0;

        DB::transaction(function () use ($validated, $directorId, $now, $createdBy, &$count) {
            foreach ($validated['accounts'] as $row) {
                ChartOfAccount::create([
                    'director_id'  => $directorId,
                    'code'         => strtoupper(trim($row['code'])),
                    'name'         => trim($row['name']),
                    'type'         => $row['type'],
                    'description'  => $row['description'] ?? null,
                    'budget_limit' => $row['budget_limit'] ?? null,
                    'is_active'    => true,
                    'created_by'   => $createdBy,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]);
                $count++;
            }
        });

        return redirect()->route('admin.coa.index')
            ->with('success', "{$count} account " . ($count === 1 ? 'head' : 'heads') . " created successfully.");
    }

    /**
     * Clone all account heads from one director to another.
     */
    public function clone(Request $request)
    {
        $validated = $request->validate([
            'source_director_id' => ['required', 'exists:users,id'],
            'target_director_id' => ['required', 'exists:users,id', 'different:source_director_id'],
        ]);

        $sourceAccounts = ChartOfAccount::where('director_id', $validated['source_director_id'])
            ->whereNull('deleted_at')
            ->get();

        if ($sourceAccounts->isEmpty()) {
            return back()->with('error', 'Source director has no account categories to clone.');
        }

        $now = now();
        $createdBy = Auth::id();
        $count = 0;

        DB::transaction(function () use ($sourceAccounts, $validated, $now, $createdBy, &$count) {
            foreach ($sourceAccounts as $account) {
                // Generate a new code for the target director (appending a unique suffix if needed)
                $baseCode = $account->code;
                $newCode = $baseCode . '-' . strtoupper(substr(uniqid(), -4));

                ChartOfAccount::create([
                    'director_id'  => $validated['target_director_id'],
                    'code'         => $newCode,
                    'name'         => $account->name,
                    'type'         => $account->type,
                    'description'  => $account->description,
                    'budget_limit' => $account->budget_limit,
                    'is_active'    => true,
                    'created_by'   => $createdBy,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]);
                $count++;
            }
        });

        return redirect()->route('admin.coa.index')
            ->with('success', "Successfully cloned {$count} account " . ($count === 1 ? 'head' : 'heads') . " to the target director.");
    }
}
