<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\Hash;

class AnggotaApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = User::where('role','siswa');
        if ($request->search)
            $query->where(fn($q)=>$q->where('name','like',"%{$request->search}%")->orWhere('nis','like',"%{$request->search}%"));
        if ($request->kelas) $query->where('kelas',$request->kelas);

        $anggotas = $query->latest()->paginate(15);
        return response()->json(['success'=>true,'data'=>$anggotas->items(),
            'meta'=>['total'=>$anggotas->total(),'last_page'=>$anggotas->lastPage()]]);
    }

    public function show(int $id): JsonResponse
    {
        $user = User::where('role','siswa')->with('pinjams.buku')->findOrFail($id);
        return response()->json(['success'=>true,'data'=>$user]);
    }

    public function store(Request $request): JsonResponse
    {
        $v = $request->validate([
            'name'=>'required|max:100','username'=>'required|max:50|unique:users',
            'email'=>'required|email|unique:users','nis'=>'required|max:20|unique:users',
            'kelas'=>'required|kelola bukumax:20','no_hp'=>'nullable|max:20',
            'password'=>'required|min:6',
        ]);
        $user = User::create([...$v,'role'=>'siswa','password'=>Hash::make($v['password'])]);
        return response()->json(['success'=>true,'message'=>'Anggota berhasil ditambahkan.','data'=>$user],201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $user = User::where('role','siswa')->findOrFail($id);
        $v = $request->validate([
            'name'=>'sometimes|max:100','kelas'=>'sometimes|max:20',
            'no_hp'=>'nullable|max:20','is_active'=>'sometimes|boolean',
            'password'=>'nullable|min:6',
        ]);
        if (isset($v['password'])) $v['password'] = Hash::make($v['password']);
        else unset($v['password']);
        $user->update($v);
        return response()->json(['success'=>true,'message'=>'Anggota berhasil diperbarui.','data'=>$user]);
    }

    public function destroy(int $id): JsonResponse
    {
        $user = User::where('role','siswa')->findOrFail($id);
        if ($user->aktivePinjam()->exists())
            return response()->json(['success'=>false,'message'=>'Anggota masih memiliki pinjaman aktif.'],422);
        $user->delete();
        return response()->json(['success'=>true,'message'=>'Anggota berhasil dihapus.']);
    }
}
