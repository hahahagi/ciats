<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Kreait\Firebase\Factory;
use Illuminate\Routing\Controller;

class CategoryController extends Controller
{
    protected $database;
    protected $tablename = 'categories';

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(config('firebase.credentials'))
            ->withDatabaseUri(config('firebase.database.url'));

        $this->database = $factory->createDatabase();

        $this->middleware(function ($request, $next) {
            if (!Session::has('user')) {
                return redirect('/login');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $user = Session::get('user');
        if (!in_array($user['role'], ['operator', 'admin'])) {
            abort(403, 'Unauthorized');
        }

        $reference = $this->database->getReference($this->tablename);
        $snapshot = $reference->getSnapshot();
        $categories = $snapshot->getValue() ?? [];

        $formattedCategories = [];
        foreach ($categories as $id => $category) {
            $category['id'] = $id;
            $formattedCategories[] = $category;
        }

        return view('categories.index', [
            'categories' => $formattedCategories,
            'user' => $user,
            'title' => 'Kelola Kategori'
        ]);
    }

    public function create()
    {
        $user = Session::get('user');
        if (!in_array($user['role'], ['operator', 'admin'])) {
            abort(403, 'Unauthorized');
        }

        return view('categories.create', [
            'user' => $user,
            'title' => 'Tambah Kategori'
        ]);
    }

    public function store(Request $request)
    {
        $user = Session::get('user');
        if (!in_array($user['role'], ['operator', 'admin'])) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
        ]);

        $newCategory = [
            'name' => $request->name,
            'slug' => \Illuminate\Support\Str::slug($request->name),
            'description' => $request->description ?? '',
            'created_at' => time(),
            'updated_at' => time(),
        ];

        $this->database->getReference($this->tablename)->push($newCategory);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = Session::get('user');
        if (!in_array($user['role'], ['operator', 'admin'])) {
            abort(403, 'Unauthorized');
        }

        $category = $this->database->getReference("{$this->tablename}/{$id}")->getValue();

        if (!$category) {
            return redirect()->route('categories.index')->with('error', 'Kategori tidak ditemukan.');
        }

        return view('categories.edit', [
            'category' => $category,
            'categoryId' => $id,
            'user' => $user,
            'title' => 'Edit Kategori'
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = Session::get('user');
        if (!in_array($user['role'], ['operator', 'admin'])) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
        ]);

        $updateData = [
            'name' => $request->name,
            'slug' => \Illuminate\Support\Str::slug($request->name),
            'description' => $request->description ?? '',
            'updated_at' => time(),
        ];

        $this->database->getReference("{$this->tablename}/{$id}")->update($updateData);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = Session::get('user');
        if (!in_array($user['role'], ['operator', 'admin'])) {
            abort(403, 'Unauthorized');
        }

        $this->database->getReference("{$this->tablename}/{$id}")->remove();

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
